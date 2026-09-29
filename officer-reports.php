<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

$conn->query(
    "CREATE TABLE IF NOT EXISTS officer_report_attachments (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        report_id BIGINT UNSIGNED NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        original_name VARCHAR(255) NOT NULL,
        mime_type VARCHAR(100) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_report_attachments_report (report_id),
        CONSTRAINT fk_report_attachments_report FOREIGN KEY (report_id)
            REFERENCES officer_reports (id) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB"
);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = $_POST ?: json_decode(file_get_contents('php://input'), true);
    $officerId = (int)($data['officer_id'] ?? 0);
    $action = $data['action'] ?? 'create';
    $reportId = (int)($data['report_id'] ?? 0);

    if (!$officerId || !$reportId || !in_array($action, ['update', 'delete'], true)) {
        if ($action === 'update' || $action === 'delete') {
            echo json_encode(['success' => false, 'message' => 'A valid report and officer are required.']);
            exit;
        }
    }

    if ($action === 'delete') {
        $attachmentStmt = $conn->prepare('SELECT file_path FROM officer_report_attachments WHERE report_id = ?');
        $attachmentStmt->bind_param('i', $reportId);
        $attachmentStmt->execute();
        $attachmentResult = $attachmentStmt->get_result();
        while ($attachment = $attachmentResult->fetch_assoc()) {
            $filePath = dirname(__DIR__) . '/' . ltrim($attachment['file_path'], '/');
            if (is_file($filePath)) unlink($filePath);
        }
        $attachmentStmt->close();

        $deleteStmt = $conn->prepare('DELETE FROM officer_reports WHERE id = ? AND officer_id = ?');
        $deleteStmt->bind_param('ii', $reportId, $officerId);
        $deleteStmt->execute();
        $deleted = $deleteStmt->affected_rows > 0;
        $deleteStmt->close();
        echo json_encode(['success' => $deleted, 'message' => $deleted ? 'Report deleted successfully.' : 'Report not found or access denied.']);
        $conn->close();
        exit;
    }

    $reportType = trim($data['report_type'] ?? '');
    $title = trim($data['title'] ?? '');
    $content = trim($data['content'] ?? '');

    if (!$officerId || !$reportType || !$title || !$content) {
        echo json_encode(['success' => false, 'message' => 'Complete all report fields.']);
        exit;
    }

    if ($action === 'update') {
        $ownerStmt = $conn->prepare('SELECT id FROM officer_reports WHERE id = ? AND officer_id = ? LIMIT 1');
        $ownerStmt->bind_param('ii', $reportId, $officerId);
        $ownerStmt->execute();
        $ownsReport = $ownerStmt->get_result()->num_rows > 0;
        $ownerStmt->close();
        if (!$ownsReport) {
            echo json_encode(['success' => false, 'message' => 'Report not found or access denied.']);
            $conn->close();
            exit;
        }

        $updateStmt = $conn->prepare('UPDATE officer_reports SET report_type = ?, title = ?, content = ? WHERE id = ? AND officer_id = ?');
        $updateStmt->bind_param('sssii', $reportType, $title, $content, $reportId, $officerId);
        $updateStmt->execute();
        $updated = $updateStmt->errno === 0;
        $updateStmt->close();
        echo json_encode(['success' => $updated, 'message' => $updated ? 'Report updated successfully.' : 'Report not found or access denied.']);
        $conn->close();
        exit;
    }

    $images = [];
    if (!empty($_FILES['images']['name'])) {
        $imageCount = count($_FILES['images']['name']);
        if ($imageCount > 10) {
            echo json_encode(['success' => false, 'message' => 'You can attach up to 10 images.']);
            exit;
        }

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/bmp' => 'bmp',
            'image/svg+xml' => 'svg',
            'image/tiff' => 'tif',
            'image/x-icon' => 'ico'
        ];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);

        for ($index = 0; $index < $imageCount; $index++) {
            if ($_FILES['images']['error'][$index] !== UPLOAD_ERR_OK || $_FILES['images']['size'][$index] > 10 * 1024 * 1024) {
                finfo_close($fileInfo);
                echo json_encode(['success' => false, 'message' => 'Each image must be valid and no larger than 10 MB.']);
                exit;
            }

            $mimeType = finfo_file($fileInfo, $_FILES['images']['tmp_name'][$index]);
            if (strpos($mimeType, 'image/') !== 0) {
                finfo_close($fileInfo);
                echo json_encode(['success' => false, 'message' => 'Only image files can be attached.']);
                exit;
            }

            $extension = $allowedMimeTypes[$mimeType] ?? strtolower(pathinfo($_FILES['images']['name'][$index], PATHINFO_EXTENSION));
            if (!preg_match('/^[a-z0-9]{1,10}$/', $extension)) $extension = 'img';

            $images[] = [
                'tmp_name' => $_FILES['images']['tmp_name'][$index],
                'original_name' => basename($_FILES['images']['name'][$index]),
                'mime_type' => $mimeType,
                'extension' => $extension
            ];
        }
        finfo_close($fileInfo);
    }

    $check = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'officer' AND status = 'Active' LIMIT 1");
    $check->bind_param('i', $officerId);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Officer account is not valid.']);
        exit;
    }
    $check->close();

    $stmt = $conn->prepare('INSERT INTO officer_reports (officer_id, report_type, title, content) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('isss', $officerId, $reportType, $title, $content);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Unable to send report.']);
        $stmt->close();
        $conn->close();
        exit;
    }

    $reportId = $conn->insert_id;
    $uploadDirectory = dirname(__DIR__) . '/uploads/officer-reports';
    if (!is_dir($uploadDirectory)) mkdir($uploadDirectory, 0755, true);
    $attachmentStmt = $conn->prepare('INSERT INTO officer_report_attachments (report_id, file_path, original_name, mime_type) VALUES (?, ?, ?, ?)');

    foreach ($images as $image) {
        $storedName = bin2hex(random_bytes(16)) . '.' . $image['extension'];
        $storedPath = 'uploads/officer-reports/' . $storedName;
        if (!move_uploaded_file($image['tmp_name'], $uploadDirectory . '/' . $storedName)) continue;
        $attachmentStmt->bind_param('isss', $reportId, $storedPath, $image['original_name'], $image['mime_type']);
        $attachmentStmt->execute();
    }
    $attachmentStmt->close();

    echo json_encode(['success' => true, 'message' => 'Report sent to admin successfully.']);
    $stmt->close();
    $conn->close();
    exit;
}

if ($method === 'GET') {
    $officerId = (int)($_GET['officer_id'] ?? 0);
    $query = 'SELECT officer_reports.id, officer_reports.report_type, officer_reports.title, officer_reports.content, officer_reports.created_at, users.full_name AS officer_name, users.officer_id FROM officer_reports INNER JOIN users ON users.id = officer_reports.officer_id';
    if ($officerId) {
        $query .= ' WHERE officer_reports.officer_id = ?';
    }
    $query .= ' ORDER BY officer_reports.created_at DESC';
    $stmt = $conn->prepare($query);
    if ($officerId) $stmt->bind_param('i', $officerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $reports = [];
    while ($row = $result->fetch_assoc()) $reports[] = $row;
    echo json_encode(['success' => true, 'reports' => $reports]);
    $stmt->close();
    $conn->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
?>
