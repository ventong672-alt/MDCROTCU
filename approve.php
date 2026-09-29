<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once '../backend/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cadetId = trim($data['cadet_id'] ?? '');
$action = $data['action'] ?? '';

if ($cadetId === '' || !in_array($action, ['approve', 'reject'], true)) {
    echo json_encode(['success' => false, 'message' => 'Cadet ID and a valid action are required.']);
    exit;
}

$status = $action === 'approve' ? 'Active' : 'Inactive';
$stmt = $conn->prepare("UPDATE cadets SET status = ? WHERE cadet_id = ? AND status = 'Pending'");
$stmt->bind_param('ss', $status, $cadetId);
$stmt->execute();

if ($stmt->affected_rows !== 1) {
    echo json_encode(['success' => false, 'message' => 'Pending cadet was not found or was already reviewed.']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => $action === 'approve' ? 'Cadet approved successfully.' : 'Cadet registration rejected.'
]);
$stmt->close();
$conn->close();
?>
