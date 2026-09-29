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
$search = strtolower(trim($data['search'] ?? ''));
$status = trim($data['status'] ?? '');

if (empty($search) && empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a search term.']);
    exit;
}

$searchTerm = '%' . $search . '%';

if (!empty($status)) {
    $query = "
        SELECT id, cadet_id, full_name, email, gender, course, year_level, section, platoon_id, status, created_at
        FROM cadets
        WHERE status = ? AND cadet_id = ?
        ORDER BY full_name ASC
        LIMIT 10
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $status, $search);
} elseif (preg_match('/^\d+$/', $search)) {
    $query = "
        SELECT 
            id,
            cadet_id,
            full_name,
            email,
            gender,
            course,
            year_level,
            section,
            platoon_id,
            status,
            created_at
        FROM cadets
        WHERE id = ? OR cadet_id = ?
        ORDER BY full_name ASC
        LIMIT 10
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('is', $search, $search);
} else {
    $query = "
        SELECT 
            id,
            cadet_id,
            full_name,
            email,
            gender,
            course,
            year_level,
            section,
            platoon_id,
            status,
            created_at
        FROM cadets
        WHERE cadet_id LIKE ? 
           OR email LIKE ? 
           OR full_name LIKE ?
        ORDER BY full_name ASC LIMIT 10
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();

$cadets = [];
while ($row = $result->fetch_assoc()) {
    $cadets[] = $row;
}

if (empty($cadets)) {
    echo json_encode(['success' => false, 'message' => 'No cadets found.', 'results' => []]);
} else {
    echo json_encode(['success' => true, 'message' => 'Search completed.', 'results' => $cadets]);
}

$stmt->close();
$conn->close();
?>
