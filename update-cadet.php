<?php
/**
 * Update Cadet Information
 * Allows admin to update cadet details
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$cadetId = (int)($data['cadet_id'] ?? 0);
$fullName = trim($data['full_name'] ?? '');
$email = strtolower(trim($data['email'] ?? ''));
$course = trim($data['course'] ?? '');
$yearLevel = $data['year_level'] ?? null;
$section = trim($data['section'] ?? '');
$platoonId = ($data['platoon_id'] !== '' && $data['platoon_id'] !== null) ? (int)$data['platoon_id'] : null;
$status = $data['status'] ?? 'Pending';

// Validation
if (!$cadetId || !$fullName || !$email || !$course) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

// Check if cadet exists
$stmt = $conn->prepare("SELECT id FROM cadets WHERE id = ?");
$stmt->bind_param('i', $cadetId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Cadet not found.']);
    exit;
}

// Update cadet
$stmt = $conn->prepare("
    UPDATE cadets 
    SET 
        full_name = ?,
        email = ?,
        course = ?,
        year_level = ?,
        section = ?,
        platoon_id = ?,
        status = ?,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = ?
");

$stmt->bind_param(
    'ssssssi',
    $fullName,
    $email,
    $course,
    $yearLevel,
    $section,
    $platoonId,
    $status,
    $cadetId
);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Cadet information updated successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update cadet information: ' . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>
