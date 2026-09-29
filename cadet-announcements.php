<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';
session_start();

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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (empty($_SESSION['cadet_id'])) {
    echo json_encode(['success' => false, 'message' => 'Cadet login is required.']);
    exit;
}

$result = $conn->query("SELECT announcements.id, announcements.title, announcements.message, announcements.created_at, users.full_name AS officer_name FROM announcements INNER JOIN users ON users.id = announcements.officer_id WHERE users.status = 'Active' ORDER BY announcements.created_at DESC LIMIT 30");
$announcements = [];
while ($row = $result->fetch_assoc()) {
    $row['attachments'] = [];
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
?>
