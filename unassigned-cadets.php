<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$companyColumn = $conn->query("SHOW COLUMNS FROM cadets LIKE 'company'");
if (!$companyColumn || $companyColumn->num_rows === 0) {
    $conn->query("ALTER TABLE cadets ADD COLUMN company VARCHAR(80) DEFAULT NULL AFTER section");
}

$result = $conn->query("SELECT c.id, c.cadet_id, c.full_name, c.year_level, c.company, c.platoon_id, COALESCE(p.name, '') AS platoon_name, c.status FROM cadets c LEFT JOIN platoons p ON p.id = c.platoon_id WHERE c.status = 'Active' AND (c.company IS NULL OR c.company = '' OR c.platoon_id IS NULL) ORDER BY c.full_name ASC");

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Unable to load unassigned cadets.']);
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
