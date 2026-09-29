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
$email = strtolower(trim($data['email'] ?? ''));
$password = $data['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Please enter your email and password.']);
    exit;
}

$stmt = $conn->prepare("SELECT id, full_name, email, password, role, status FROM users WHERE email = ? AND role = 'officer' LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$officer = $result->fetch_assoc();

if (!$officer || !password_verify($password, $officer['password'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect officer email or password.']);
    exit;
}

if ($officer['status'] !== 'Active') {
    $message = $officer['status'] === 'Pending'
        ? 'Your officer registration is waiting for admin approval. You cannot log in until an admin approves your account.'
        : 'This officer account is not active. Contact the administrator for assistance.';

    echo json_encode([
        'success' => false,
        'status' => $officer['status'],
        'message' => $message,
        'action' => 'Wait for admin approval, then try logging in again.'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Officer login successful.',
    'officer_id' => (int) $officer['id'],
    'full_name' => $officer['full_name'],
    'email' => $officer['email'],
    'role' => $officer['role']
]);
$stmt->close();
$conn->close();
?>
