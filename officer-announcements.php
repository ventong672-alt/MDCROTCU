<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

$conn->query("CREATE TABLE IF NOT EXISTS announcement_attachments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    announcement_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_announcement_attachments_announcement (announcement_id),
    CONSTRAINT fk_announcement_attachments_announcement FOREIGN KEY (announcement_id) REFERENCES announcements (id) ON UPDATE CASCADE ON DELETE CASCADE
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data) || empty($data)) {
        $data = $_POST;
    }

    $officerId = (int)($data['officer_id'] ?? 0);
    $action = $data['action'] ?? 'publish';

    if ($action === 'reply') {
        echo json_encode(['success' => false, 'message' => 'Officer announcements are view-only.']);
        $conn->close();
        exit;
    }

    $title = trim((string)($data['title'] ?? ''));
    $message = trim((string)($data['message'] ?? ''));

    if (!$officerId || $title === '' || $message === '') {
        echo json_encode(['success' => false, 'message' => 'Announcement title and message are required.']);
        exit;
    }

    $check = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'officer' AND status = 'Active' LIMIT 1");
    $check->bind_param('i', $officerId);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Officer account is not valid.']);
        exit;
    }
    $check->close();

    $uploadedAttachment = null;
    if (!empty($_FILES['attachment']['name'])) {
        $attachmentFile = $_FILES['attachment'];
        if ($attachmentFile['error'] !== UPLOAD_ERR_OK || $attachmentFile['size'] > 50 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Attachment must be valid and no larger than 50 MB.']);
            $conn->close();
            exit;
        }

        $uploadDirectory = dirname(__DIR__) . '/uploads/officer-announcements';
        if (!is_dir($uploadDirectory)) mkdir($uploadDirectory, 0755, true);

        $originalName = basename($attachmentFile['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $storedName = bin2hex(random_bytes(16)) . ($extension ? '.' . preg_replace('/[^a-z0-9]+/i', '', $extension) : '');
        $storedPath = 'uploads/officer-announcements/' . $storedName;

        if (!move_uploaded_file($attachmentFile['tmp_name'], $uploadDirectory . '/' . $storedName)) {
            echo json_encode(['success' => false, 'message' => 'Attachment could not be stored.']);
            $conn->close();
            exit;
        }

        $uploadedAttachment = [
            'file_path' => $storedPath,
            'original_name' => $originalName,
            'mime_type' => mime_content_type($uploadDirectory . '/' . $storedName) ?: 'application/octet-stream'
        ];
    }

    $stmt = $conn->prepare('INSERT INTO announcements (officer_id, title, message) VALUES (?, ?, ?)');
    $stmt->bind_param('iss', $officerId, $title, $message);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Unable to publish announcement.']);
        $stmt->close();
        $conn->close();
        exit;
    }

    $announcementId = $conn->insert_id;
    if ($uploadedAttachment) {
        $attachmentStmt = $conn->prepare('INSERT INTO announcement_attachments (announcement_id, file_path, original_name, mime_type) VALUES (?, ?, ?, ?)');
        $attachmentStmt->bind_param('isss', $announcementId, $uploadedAttachment['file_path'], $uploadedAttachment['original_name'], $uploadedAttachment['mime_type']);
        $attachmentStmt->execute();
        $attachmentStmt->close();
    }

    echo json_encode(['success' => true, 'message' => 'Announcement published.']);
    $stmt->close();
    $conn->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT announcements.id, announcements.title, announcements.message, announcements.created_at, users.full_name AS officer_name, users.role AS author_role FROM announcements INNER JOIN users ON users.id = announcements.officer_id WHERE users.status = 'Active' ORDER BY announcements.created_at DESC LIMIT 30");
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Unable to load announcements.']);
        $conn->close();
        exit;
    }
    $announcements = [];
    while ($row = $result->fetch_assoc()) {
        $row['is_admin_announcement'] = $row['author_role'] === 'admin';
        $row['replies'] = [];
        $row['attachments'] = [];

        $replyStmt = $conn->prepare('SELECT announcement_replies.message, announcement_replies.sender_role, announcement_replies.created_at, users.full_name AS officer_name FROM announcement_replies INNER JOIN users ON users.id = announcement_replies.officer_id WHERE announcement_replies.announcement_id = ? ORDER BY announcement_replies.created_at ASC');
        $replyStmt->bind_param('i', $row['id']);
        $replyStmt->execute();
        $replyResult = $replyStmt->get_result();
        while ($reply = $replyResult->fetch_assoc()) $row['replies'][] = $reply;
        $replyStmt->close();

        $attachmentStmt = $conn->prepare('SELECT file_path, original_name, mime_type FROM announcement_attachments WHERE announcement_id = ? ORDER BY id ASC');
        $attachmentStmt->bind_param('i', $row['id']);
        $attachmentStmt->execute();
        $attachmentResult = $attachmentStmt->get_result();
        while ($attachment = $attachmentResult->fetch_assoc()) $row['attachments'][] = $attachment;
        $attachmentStmt->close();

        $announcements[] = $row;
    }
    echo json_encode(['success' => true, 'announcements' => $announcements]);
    $conn->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
?>
