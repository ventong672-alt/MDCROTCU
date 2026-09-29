<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$officerId = (int)($data['officer_id'] ?? 0);

if (!$officerId) {
    echo json_encode(['success' => false, 'message' => 'Officer ID is required.']);
    exit;
}

$stmt = $conn->prepare("UPDATE users SET status = 'Active', updated_at = CURRENT_TIMESTAMP WHERE id = ? AND role = 'officer' AND status = 'Pending'");
$stmt->bind_param('i', $officerId);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Pending officer account not found.']);
} else {
    echo json_encode(['success' => true, 'message' => 'Officer approved successfully.']);
}

$stmt->close();
$conn->close();
?>
