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
$cadetId = (int)($data['cadet_id'] ?? 0);

if (!$cadetId) {
    echo json_encode(['success' => false, 'message' => 'Cadet ID is required.']);
    exit;
}

$stmt = $conn->prepare('DELETE FROM cadets WHERE id = ?');
$stmt->bind_param('i', $cadetId);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Cadet deleted successfully.']);
} else {
    echo json_encode([
        'success' => false,
        'message' => $stmt->errno === 1451
            ? 'This cadet cannot be deleted because related attendance, grade, or conduct records exist.'
            : 'Cadet not found or could not be deleted.'
    ]);
}

$stmt->close();
$conn->close();
?>
