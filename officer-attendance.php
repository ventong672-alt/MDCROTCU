<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

$conn->query("ALTER TABLE attendance_records ADD COLUMN IF NOT EXISTS time_out TIME DEFAULT NULL AFTER time_in");
$conn->query("ALTER TABLE training_sessions ADD COLUMN IF NOT EXISTS is_generated TINYINT(1) NOT NULL DEFAULT 0 AFTER instruction");

// Sends a JSON response and stops request processing.
function jsonResponse(array $payload): void
{
    echo json_encode($payload);
    exit;
}

// Confirms that an active officer account exists.
function validateOfficer(mysqli $conn, int $officerId): bool
{
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'officer' AND status = 'Active' LIMIT 1");
    $stmt->bind_param('i', $officerId);
    $stmt->execute();
    $valid = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $valid;
}

// Resolves an active cadet by database ID or cadet code.
function resolveCadet(mysqli $conn, string $cadetCode): ?array
{
    $stmt = $conn->prepare("SELECT id, cadet_id FROM cadets WHERE (id = ? OR cadet_id = ?) AND status = 'Active' LIMIT 1");
    $numericId = ctype_digit($cadetCode) ? (int)$cadetCode : 0;
    $stmt->bind_param('is', $numericId, $cadetCode);
    $stmt->execute();
    $cadet = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $cadet;
}

$conn->query("UPDATE training_sessions SET location = 'Training Ground' WHERE TRIM(COALESCE(location, '')) = '' OR LOWER(TRIM(location)) = 'rotc formation'");

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $result = $conn->query("SELECT a.id, c.cadet_id AS cadet_code, t.training_date AS date, t.training_type, a.status, a.time_in, a.time_out, COALESCE(a.remarks, '') AS remarks
        FROM attendance_records a
        INNER JOIN cadets c ON c.id = a.cadet_id
        INNER JOIN training_sessions t ON t.id = a.training_id
        WHERE c.status = 'Active'
        ORDER BY t.training_date DESC, c.full_name ASC");
    if (!$result) jsonResponse(['success' => false, 'message' => 'Unable to load attendance records.']);

    $attendance = [];
    while ($row = $result->fetch_assoc()) $attendance[] = $row;
    jsonResponse(['success' => true, 'attendance' => $attendance]);
}

if ($method !== 'POST') jsonResponse(['success' => false, 'message' => 'Invalid request method.']);

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$action = $data['action'] ?? 'save';
$officerId = (int)($data['officer_id'] ?? 0);
if (!$officerId || !validateOfficer($conn, $officerId)) jsonResponse(['success' => false, 'message' => 'Officer account is not valid.']);

if ($action === 'save') {
    $cadet = resolveCadet($conn, trim((string)($data['cadet_id'] ?? '')));
    $date = trim((string)($data['date'] ?? ''));
    $status = trim((string)($data['status'] ?? ''));
    $timeIn = trim((string)($data['time_in'] ?? '')) ?: null;
    $timeOut = trim((string)($data['time_out'] ?? '')) ?: null;
    $trainingType = trim((string)($data['training_type'] ?? $data['session_type'] ?? 'Formation 1')) ?: 'Formation 1';
    $location = trim((string)($data['location'] ?? '')) ?: 'Training Ground';
    $remarks = trim((string)($data['remarks'] ?? ''));
    $allowedStatuses = ['Present', 'Late', 'Absent', 'Excused'];

    if (!$cadet || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !in_array($status, $allowedStatuses, true)) {
        jsonResponse(['success' => false, 'message' => 'Complete the attendance fields.']);
    }

    $normalizedType = strtoupper(trim($trainingType));
    if ($normalizedType === 'FORMATION') {
        $trainingType = 'FORMATION';
    }

    $sessionStmt = $conn->prepare("SELECT id FROM training_sessions WHERE training_date = ? AND training_type = ? ORDER BY id DESC LIMIT 1");
    $sessionStmt->bind_param('ss', $date, $trainingType);
    $sessionStmt->execute();
    $trainingId = (int)($sessionStmt->get_result()->fetch_assoc()['id'] ?? 0);
    $sessionStmt->close();

    if (!$trainingId) {
        $title = $trainingType === 'FORMATION' ? 'Formation Attendance' : $trainingType;
        $type = $trainingType;
        $start = $timeIn ?: '00:00:00';
        $end = $timeOut ?: $start;
        $createStmt = $conn->prepare('INSERT INTO training_sessions (training_date, training_type, title, start_time, end_time, location, is_generated, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $createStmt->bind_param('ssssssii', $date, $type, $title, $start, $end, $location, $isGenerated, $officerId);
        $isGenerated = 1;
        if (!$createStmt->execute()) jsonResponse(['success' => false, 'message' => 'Unable to create the attendance session.']);
        $trainingId = $conn->insert_id;
        $createStmt->close();
    }

    $stmt = $conn->prepare('INSERT INTO attendance_records (cadet_id, training_id, status, time_in, time_out, remarks, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status), time_in = VALUES(time_in), time_out = VALUES(time_out), remarks = VALUES(remarks), recorded_by = VALUES(recorded_by)');
    $stmt->bind_param('iissssi', $cadet['id'], $trainingId, $status, $timeIn, $timeOut, $remarks, $officerId);
    $success = $stmt->execute();
    $stmt->close();
    jsonResponse(['success' => $success, 'message' => $success ? 'Attendance saved.' : 'Unable to save attendance.']);
}

jsonResponse(['success' => false, 'message' => 'Invalid attendance action.']);
?>
