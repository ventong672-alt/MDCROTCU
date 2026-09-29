<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

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

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $result = $conn->query("SELECT r.id, c.cadet_id AS cadet_code, r.record_date AS date, r.type, r.points, r.reason, COALESCE(u.full_name, 'Officer') AS recorded_by
        FROM conduct_records r
        INNER JOIN cadets c ON c.id = r.cadet_id
        LEFT JOIN users u ON u.id = r.recorded_by
        WHERE c.status = 'Active'
        ORDER BY r.record_date DESC, r.id DESC");
    if (!$result) jsonResponse(['success' => false, 'message' => 'Unable to load conduct records.']);

    $conduct = [];
    while ($row = $result->fetch_assoc()) $conduct[] = $row;
    jsonResponse(['success' => true, 'conduct' => $conduct]);
}

if ($method !== 'POST') jsonResponse(['success' => false, 'message' => 'Invalid request method.']);

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$action = $data['action'] ?? 'save';
$officerId = (int)($data['officer_id'] ?? 0);
if (!$officerId || !validateOfficer($conn, $officerId)) jsonResponse(['success' => false, 'message' => 'Officer account is not valid.']);

if ($action === 'delete') {
    $recordId = (int)($data['record_id'] ?? 0);
    $stmt = $conn->prepare('DELETE FROM conduct_records WHERE id = ? AND recorded_by = ?');
    $stmt->bind_param('ii', $recordId, $officerId);
    $stmt->execute();
    $deleted = $stmt->affected_rows > 0;
    $stmt->close();
    jsonResponse(['success' => $deleted, 'message' => $deleted ? 'Conduct record deleted.' : 'Conduct record not found.']);
}

if ($action === 'save') {
    $cadet = resolveCadet($conn, trim((string)($data['cadet_id'] ?? '')));
    $date = trim((string)($data['date'] ?? ''));
    $type = trim((string)($data['type'] ?? ''));
    $points = (int)($data['points'] ?? 0);
    $reason = trim((string)($data['reason'] ?? ''));

    if (!$cadet || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !in_array($type, ['Merit', 'Demerit'], true) || $points < 1 || $reason === '') {
        jsonResponse(['success' => false, 'message' => 'Complete the conduct fields.']);
    }

    $stmt = $conn->prepare('INSERT INTO conduct_records (cadet_id, record_date, type, points, reason, recorded_by) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('issisi', $cadet['id'], $date, $type, $points, $reason, $officerId);
    $success = $stmt->execute();
    $recordId = $stmt->insert_id;
    $stmt->close();
    jsonResponse(['success' => $success, 'record_id' => $recordId, 'message' => $success ? 'Conduct record saved.' : 'Unable to save conduct record.']);
}

jsonResponse(['success' => false, 'message' => 'Invalid conduct action.']);
?>
