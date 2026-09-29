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
$officerId = trim($data['officer_id'] ?? '');
$fullName = trim($data['full_name'] ?? '');
$email = strtolower(trim($data['email'] ?? ''));
$yearLevel = trim((string)($data['year_level'] ?? ''));
$course = trim((string)($data['course'] ?? ''));
$password = $data['password'] ?? '';

$allowedYearLevels = ['First Year', 'Second Year', 'Third Year', 'Fourth Year'];
$allowedCourses = [
    'Diploma in Software Engineering',
    'Diploma in Digital Arts',
    'Bachelor of Science in Criminology',
    'Bachelor of Information Technology',
    'Bachelor of Science in Computer Science',
    'Diploma in Tourism Management',
    'College of Tourism Management',
    'Bachelor of Business Administration',
    'College of Arts and Education'
];

if ($officerId === '' || $fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($yearLevel, $allowedYearLevels, true) || !in_array($course, $allowedCourses, true) || strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Enter an officer ID, name, valid email, and password of at least 8 characters.']);
    exit;
}

$userColumns = $conn->query("SHOW COLUMNS FROM users LIKE 'year_level'");
if (!$userColumns || $userColumns->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN year_level ENUM('First Year', 'Second Year', 'Third Year', 'Fourth Year') DEFAULT NULL AFTER email, ADD COLUMN course VARCHAR(150) DEFAULT NULL AFTER year_level");
}

$check = $conn->prepare('SELECT id FROM users WHERE email = ? OR officer_id = ? LIMIT 1');
$check->bind_param('ss', $email, $officerId);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'This email is already registered.']);
    exit;
}
$check->close();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (officer_id, full_name, email, year_level, course, password, role, status) VALUES (?, ?, ?, ?, ?, ?, 'officer', 'Pending')");
$stmt->bind_param('ssssss', $officerId, $fullName, $email, $yearLevel, $course, $hashedPassword);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Officer registration failed.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Registration submitted. Wait for admin approval before logging in.']);
$stmt->close();
$conn->close();
?>
