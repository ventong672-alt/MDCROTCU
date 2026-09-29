<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

$conn->query("CREATE TABLE IF NOT EXISTS officer_instruction_attachments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    instruction_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_instruction_attachments_instruction (instruction_id),
    CONSTRAINT fk_instruction_attachments_instruction FOREIGN KEY (instruction_id) REFERENCES officer_instructions (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB");

$data = $_POST ?: (json_decode(file_get_contents('php://input'), true) ?: []);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $officerId = (int)($data['officer_id'] ?? 0);
    $action = $data['action'] ?? 'send';

    if ($action === 'mark_read') {
        $instructionId = (int)($data['instruction_id'] ?? 0);
        $stmt = $conn->prepare('UPDATE officer_instructions SET is_read = 1 WHERE id = ? AND officer_id = ?');
        $stmt->bind_param('ii', $instructionId, $officerId);
        $stmt->execute();
        echo json_encode(['success' => $stmt->affected_rows > 0, 'message' => 'Instruction marked as read.']);
        $stmt->close();
        $conn->close();
        exit;
    }

    $message = trim($data['message'] ?? '');
    if (!$officerId || $message === '') {
        echo json_encode(['success' => false, 'message' => 'Officer and instruction are required.']);
        exit;
    }

    $check = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'officer' LIMIT 1");
    $check->bind_param('i', $officerId);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Officer not found.']);
        exit;
    }
    $check->close();

    $stmt = $conn->prepare('INSERT INTO officer_instructions (officer_id, message) VALUES (?, ?)');
    $stmt->bind_param('is', $officerId, $message);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Unable to send instruction.']);
        $stmt->close();
        $conn->close();
        exit;
    }

    $instructionId = $conn->insert_id;
    if (!empty($_FILES['attachment']['name'])) {
        if ($_FILES['attachment']['error'] !== UPLOAD_ERR_OK || $_FILES['attachment']['size'] > 20 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'The attachment must be valid and no larger than 20 MB.']);
            $stmt->close();
            $conn->close();
            exit;
        }

        $uploadDirectory = dirname(__DIR__) . '/uploads/admin-instructions';
        if (!is_dir($uploadDirectory)) mkdir($uploadDirectory, 0755, true);
        $originalName = basename($_FILES['attachment']['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $storedName = bin2hex(random_bytes(16)) . ($extension ? '.' . preg_replace('/[^a-z0-9]+/i', '', $extension) : '');
        $storedPath = 'uploads/admin-instructions/' . $storedName;
        if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDirectory . '/' . $storedName)) {
            echo json_encode(['success' => false, 'message' => 'Instruction sent, but the attachment could not be stored.']);
            $stmt->close();
            $conn->close();
            exit;
        }

        $mimeType = mime_content_type($uploadDirectory . '/' . $storedName) ?: 'application/octet-stream';
        $attachmentStmt = $conn->prepare('INSERT INTO officer_instruction_attachments (instruction_id, file_path, original_name, mime_type) VALUES (?, ?, ?, ?)');
        $attachmentStmt->bind_param('isss', $instructionId, $storedPath, $originalName, $mimeType);
        $attachmentStmt->execute();
        $attachmentStmt->close();
    }

    echo json_encode(['success' => true, 'message' => 'Instruction sent successfully.']);
    $stmt->close();
    $conn->close();
    exit;
}

if ($method === 'GET') {
    $officerId = (int)($_GET['officer_id'] ?? 0);
    if (!$officerId) {
        echo json_encode(['success' => false, 'message' => 'Officer ID is required.']);
        exit;
    }

    $stmt = $conn->prepare(
        'SELECT id, message, is_read, created_at FROM officer_instructions WHERE officer_id = ? ORDER BY created_at DESC LIMIT 20'
    );
    $stmt->bind_param('i', $officerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $instructions = [];
    while ($row = $result->fetch_assoc()) {
        $attachmentStmt = $conn->prepare('SELECT file_path, original_name, mime_type FROM officer_instruction_attachments WHERE instruction_id = ? ORDER BY id ASC');
        $attachmentStmt->bind_param('i', $row['id']);
        $attachmentStmt->execute();
        $attachmentResult = $attachmentStmt->get_result();
        $row['attachments'] = [];
        while ($attachment = $attachmentResult->fetch_assoc()) $row['attachments'][] = $attachment;
        $attachmentStmt->close();
        $instructions[] = $row;
    }
    echo json_encode(['success' => true, 'instructions' => $instructions]);
    $stmt->close();
    $conn->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
?>
