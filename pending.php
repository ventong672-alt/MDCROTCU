<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once '../backend/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$result = $conn->query(
    "SELECT id, cadet_id, full_name, email, gender, course, created_at
     FROM cadets
     WHERE status = 'Pending'
     ORDER BY created_at ASC"
);

$cadets = [];
while ($cadet = $result->fetch_assoc()) {
    $cadets[] = $cadet;
}

echo json_encode(['success' => true, 'cadets' => $cadets]);
$conn->close();
?>
