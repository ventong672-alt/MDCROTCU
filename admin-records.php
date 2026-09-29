<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$officerResult = $conn->query(
    "SELECT id, officer_id, full_name, email, role, status, created_at
     FROM users
         WHERE role = 'officer'
             AND password LIKE CONCAT(CHAR(36), '2%')
     ORDER BY full_name ASC"
);

$cadetResult = $conn->query(
    "SELECT id, cadet_id, full_name, email, course, year_level, section, platoon_id, status, created_at
     FROM cadets
        WHERE status = 'Active'
            AND password LIKE CONCAT(CHAR(36), '2%')
     ORDER BY full_name ASC"
);

if (!$officerResult || !$cadetResult) {
    echo json_encode(['success' => false, 'message' => 'Unable to load admin records.']);
    $conn->close();
    exit;
}

$officers = [];
while ($officer = $officerResult->fetch_assoc()) {
    $officers[] = $officer;
}

$cadets = [];
while ($cadet = $cadetResult->fetch_assoc()) {
    $cadets[] = $cadet;
}

echo json_encode([
    'success' => true,
    'officers' => $officers,
    'cadets' => $cadets
]);

$conn->close();
?>
