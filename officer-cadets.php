<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$result = $conn->query(
    "SELECT c.id, c.cadet_id, c.full_name, c.email, c.gender, c.year_level, c.section, c.company, c.platoon_id, COALESCE(p.name, '') AS platoon_name, c.status
     FROM cadets c LEFT JOIN platoons p ON p.id = c.platoon_id
     ORDER BY full_name ASC"
);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Unable to load registered cadets.']);
    $conn->close();
    exit;
}

$cadets = [];
while ($cadet = $result->fetch_assoc()) {
    $cadets[] = $cadet;
}

echo json_encode(['success' => true, 'cadets' => $cadets]);
$conn->close();
?>
