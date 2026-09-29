<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
require_once '../backend/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = strtolower(trim($data['email'] ?? ''));
$otp = trim($data['otp'] ?? '');
$password = $data['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[0-9]{6}$/', $otp) || strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Enter a valid email, 6-digit OTP, and password of at least 8 characters.']);
    exit;
}

$otpHash = hash('sha256', $otp);
$stmt = $conn->prepare(
    'SELECT id, account_type, account_id FROM password_resets
     WHERE account_email = ? AND token_hash = ? AND used_at IS NULL
       AND expires_at > NOW() AND attempts < 5 LIMIT 1'
);
$stmt->bind_param('ss', $email, $otpHash);
$stmt->execute();
$result = $stmt->get_result();
$reset = $result->fetch_assoc();
$stmt->close();

if (!$reset) {
    $failed = $conn->prepare('UPDATE password_resets SET attempts = attempts + 1 WHERE account_email = ? AND used_at IS NULL AND expires_at > NOW()');
    $failed->bind_param('s', $email);
    $failed->execute();
    $failed->close();
    echo json_encode(['success' => false, 'message' => 'The OTP is invalid, expired, or has reached its attempt limit.']);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$conn->begin_transaction();

if ($reset['account_type'] === 'cadet') {
    $update = $conn->prepare('UPDATE cadets SET password = ? WHERE id = ? AND status = \'Active\'');
} else {
    $update = $conn->prepare('UPDATE users SET password = ? WHERE id = ? AND status = \'Active\'');
}

$accountId = (int) $reset['account_id'];
$update->bind_param('si', $hashedPassword, $accountId);
$updated = $update->execute() && $update->affected_rows === 1;
$update->close();

if (!$updated) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'The account could not be updated.']);
    exit;
}

$markUsed = $conn->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = ? AND used_at IS NULL');
$resetId = (int) $reset['id'];
$markUsed->bind_param('i', $resetId);
$marked = $markUsed->execute() && $markUsed->affected_rows === 1;
$markUsed->close();

if (!$marked) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'The reset link could not be completed.']);
    exit;
}

$conn->commit();
echo json_encode(['success' => true, 'message' => 'Password updated successfully. You can now log in.']);
$conn->close();
?>
