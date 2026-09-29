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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$result = $conn->query(
    "SELECT officer_reports.id, officer_reports.report_type, officer_reports.title,
            officer_reports.content, officer_reports.created_at,
            users.full_name AS officer_name, users.officer_id
     FROM officer_reports
     INNER JOIN users ON users.id = officer_reports.officer_id
     WHERE users.role = 'officer'
     ORDER BY officer_reports.created_at DESC"
);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Unable to load officer reports.']);
    exit;
}

$reports = [];
while ($report = $result->fetch_assoc()) $reports[] = $report;

foreach ($reports as &$report) {
    $attachmentStmt = $conn->prepare('SELECT file_path, original_name, mime_type FROM officer_report_attachments WHERE report_id = ? ORDER BY id ASC');
    $attachmentStmt->bind_param('i', $report['id']);
    $attachmentStmt->execute();
    $attachmentResult = $attachmentStmt->get_result();
    $report['attachments'] = [];
    while ($attachment = $attachmentResult->fetch_assoc()) $report['attachments'][] = $attachment;
    $attachmentStmt->close();
}
unset($report);

echo json_encode(['success' => true, 'reports' => $reports]);
$conn->close();
?>
