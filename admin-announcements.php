<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

$conn->query("CREATE TABLE IF NOT EXISTS announcements (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    officer_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_announcements_created_at (created_at),
    CONSTRAINT fk_announcements_author FOREIGN KEY (officer_id) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB");

$conn->query("CREATE TABLE IF NOT EXISTS announcement_replies (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    announcement_id BIGINT UNSIGNED NOT NULL,
    officer_id BIGINT UNSIGNED NOT NULL,
    sender_role VARCHAR(20) NOT NULL DEFAULT 'officer',
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_announcement_replies_announcement (announcement_id),
    CONSTRAINT fk_announcement_replies_announcement FOREIGN KEY (announcement_id) REFERENCES announcements (id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_announcement_replies_officer FOREIGN KEY (officer_id) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB");
$conn->query("ALTER TABLE announcement_replies ADD COLUMN IF NOT EXISTS sender_role VARCHAR(20) NOT NULL DEFAULT 'officer'");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT announcements.id, announcements.title, announcements.message, announcements.created_at FROM announcements INNER JOIN users ON users.id = announcements.officer_id WHERE users.role = 'admin' ORDER BY announcements.created_at DESC LIMIT 20");
    $announcements = [];
    while ($announcement = $result->fetch_assoc()) {
        $announcement['replies'] = [];
        $replyStmt = $conn->prepare('SELECT announcement_replies.message, announcement_replies.sender_role, announcement_replies.created_at, users.full_name AS officer_name FROM announcement_replies INNER JOIN users ON users.id = announcement_replies.officer_id WHERE announcement_replies.announcement_id = ? ORDER BY announcement_replies.created_at ASC');
        $replyStmt->bind_param('i', $announcement['id']);
        $replyStmt->execute();
        $replyResult = $replyStmt->get_result();
        while ($reply = $replyResult->fetch_assoc()) $announcement['replies'][] = $reply;
        $replyStmt->close();
        $announcements[] = $announcement;
    }
    echo json_encode(['success' => true, 'announcements' => $announcements]);
    $conn->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$adminId = (int)($data['admin_id'] ?? 0);
$action = $data['action'] ?? 'publish';
$title = trim((string)($data['title'] ?? ''));
$message = trim((string)($data['message'] ?? ''));

if ($action === 'reply') {
    $announcementId = (int)($data['announcement_id'] ?? 0);
    if (!$adminId || !$announcementId || $message === '') {
        echo json_encode(['success' => false, 'message' => 'A reply message is required.']);
        exit;
    }
    $replyStmt = $conn->prepare("INSERT INTO announcement_replies (announcement_id, officer_id, sender_role, message) SELECT ?, ?, 'admin', ? FROM announcements WHERE id = ? LIMIT 1");
    $replyStmt->bind_param('iisi', $announcementId, $adminId, $message, $announcementId);
    $replyStmt->execute();
    $success = $replyStmt->affected_rows > 0;
    $replyStmt->close();
    echo json_encode(['success' => $success, 'message' => $success ? 'Reply sent to the officer.' : 'Announcement was not found.']);
    $conn->close();
    exit;
}

if (!$adminId || $title === '' || $message === '') {
    echo json_encode(['success' => false, 'message' => 'Announcement title and message are required.']);
    exit;
}

$check = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'admin' AND status = 'Active' LIMIT 1");
if (!$check) {
    echo json_encode(['success' => false, 'message' => 'Unable to validate admin account: ' . $conn->error]);
    $conn->close();
    exit;
}
$check->bind_param('i', $adminId);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Admin account is not valid.']);
    exit;
}
$check->close();

$stmt = $conn->prepare('INSERT INTO announcements (officer_id, title, message) VALUES (?, ?, ?)');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Unable to prepare announcement: ' . $conn->error]);
    $conn->close();
    exit;
}
$stmt->bind_param('iss', $adminId, $title, $message);
$success = $stmt->execute();
echo json_encode(['success' => $success, 'message' => $success ? 'Announcement published to officers.' : ('Unable to publish announcement: ' . $stmt->error)]);
$stmt->close();
$conn->close();
?>
