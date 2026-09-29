<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

$conn->query("ALTER TABLE training_sessions ADD COLUMN IF NOT EXISTS instruction TEXT DEFAULT NULL AFTER location");
$conn->query("ALTER TABLE training_sessions ADD COLUMN IF NOT EXISTS is_generated TINYINT(1) NOT NULL DEFAULT 0 AFTER instruction");
$conn->query("ALTER TABLE training_sessions ADD COLUMN IF NOT EXISTS announcement_id BIGINT UNSIGNED DEFAULT NULL AFTER is_generated");
$conn->query("ALTER TABLE training_sessions MODIFY COLUMN training_type VARCHAR(80) NOT NULL DEFAULT 'OTHER'");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$officerId = (int)($data['officer_id'] ?? 0);
$date = trim($data['training_date'] ?? '');
$type = strtoupper(trim($data['training_type'] ?? 'OTHER'));
$title = trim($data['title'] ?? '');
$start = trim($data['start_time'] ?? '');
$end = trim($data['end_time'] ?? '');
$location = trim($data['location'] ?? '');
$instruction = trim($data['instruction'] ?? '');
$action = trim($data['action'] ?? 'create');
$trainingId = (int)($data['training_id'] ?? 0);

if (!$officerId || ($action !== 'delete' && (!$date || !$type || !$title || !$start || !$end || !$location || strlen($type) > 80))) {
    echo json_encode(['success' => false, 'message' => 'Complete all training fields.']);
    exit;
}

$check = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'officer' AND status = 'Active' LIMIT 1");
$check->bind_param('i', $officerId);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Officer account is not valid.']);
    exit;
}
$check->close();

if ($action === 'delete') {
    if (!$trainingId) {
        echo json_encode(['success' => false, 'message' => 'Training session ID is required.']);
        exit;
    }
    $stmt = $conn->prepare('DELETE FROM training_sessions WHERE id = ? AND created_by = ?');
    $stmt->bind_param('ii', $trainingId, $officerId);
    $stmt->execute();
    $deleted = $stmt->affected_rows > 0;
    $stmt->close();
    echo json_encode(['success' => $deleted, 'message' => $deleted ? 'Training session deleted.' : 'Training session not found or not owned by this officer.']);
    $conn->close();
    exit;
}

if ($action === 'update') {
    if (!$trainingId) {
        echo json_encode(['success' => false, 'message' => 'Training session ID is required.']);
        exit;
    }
    $stmt = $conn->prepare('UPDATE training_sessions SET training_date = ?, training_type = ?, title = ?, start_time = ?, end_time = ?, location = ?, instruction = ?, is_generated = 0 WHERE id = ? AND created_by = ?');
    $stmt->bind_param('sssssssii', $date, $type, $title, $start, $end, $location, $instruction, $trainingId, $officerId);
} else {
    $isGenerated = 0;
    $stmt = $conn->prepare('INSERT INTO training_sessions (training_date, training_type, title, start_time, end_time, location, instruction, is_generated, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssssssii', $date, $type, $title, $start, $end, $location, $instruction, $isGenerated, $officerId);
}

$saved = $stmt->execute();
if (!$saved) {
    echo json_encode(['success' => false, 'message' => 'Unable to save training schedule.']);
    $stmt->close();
    $conn->close();
    exit;
}

$trainingId = $action === 'update' ? $trainingId : $conn->insert_id;
$announcementId = 0;
if ($action === 'update') {
    $announcementLookup = $conn->prepare('SELECT announcement_id FROM training_sessions WHERE id = ? AND created_by = ? LIMIT 1');
    $announcementLookup->bind_param('ii', $trainingId, $officerId);
    $announcementLookup->execute();
    $announcementId = (int)($announcementLookup->get_result()->fetch_assoc()['announcement_id'] ?? 0);
    $announcementLookup->close();
}

$announcementTitle = 'Training Schedule: ' . $title;
$announcementMessage = "5W1H TRAINING ANNOUNCEMENT\n\n"
    . "Who: All ROTC cadets\n"
    . "What: {$title} ({$type})\n"
    . "When: {$date}, {$start} to {$end}\n"
    . "Where: {$location}\n"
    . "Why: To support ROTC training, readiness, and participation.\n"
    . "How: " . ($instruction !== '' ? $instruction : 'Attend on time and follow the instructions of the ROTC officers.');

if ($announcementId) {
    $announcementStmt = $conn->prepare('UPDATE announcements SET title = ?, message = ? WHERE id = ? AND officer_id = ?');
    $announcementStmt->bind_param('ssii', $announcementTitle, $announcementMessage, $announcementId, $officerId);
    $announcementSaved = $announcementStmt->execute();
    $announcementStmt->close();
} else {
    $announcementStmt = $conn->prepare('INSERT INTO announcements (officer_id, title, message) VALUES (?, ?, ?)');
    $announcementStmt->bind_param('iss', $officerId, $announcementTitle, $announcementMessage);
    $announcementSaved = $announcementStmt->execute();
    if ($announcementSaved) {
        $announcementId = $conn->insert_id;
        $linkStmt = $conn->prepare('UPDATE training_sessions SET announcement_id = ? WHERE id = ? AND created_by = ?');
        $linkStmt->bind_param('iii', $announcementId, $trainingId, $officerId);
        $linkStmt->execute();
        $linkStmt->close();
    }
    $announcementStmt->close();
}

echo json_encode($announcementSaved
    ? ['success' => true, 'message' => $action === 'update' ? 'Training schedule updated and announcement refreshed.' : 'Training schedule created and announced.']
    : ['success' => false, 'message' => 'Training schedule saved, but its announcement could not be published.']);
$stmt->close();
$conn->close();
?>
