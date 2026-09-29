<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$officerId = (int)($data['officer_id'] ?? 0);
$cadetId = (int)($data['cadet_id'] ?? 0);
$action = $data['action'] ?? 'assign';
$company = trim((string)($data['company'] ?? ''));
$platoonName = trim((string)($data['platoon_name'] ?? ''));

if (!$officerId || !$cadetId || ($action !== 'drop' && ($company === '' || $platoonName === ''))) {
    echo json_encode(['success' => false, 'message' => 'Cadet, company, and platoon are required.']);
    exit;
}

$companyColumn = $conn->query("SHOW COLUMNS FROM cadets LIKE 'company'");
if (!$companyColumn || $companyColumn->num_rows === 0) {
    $conn->query("ALTER TABLE cadets ADD COLUMN company VARCHAR(80) DEFAULT NULL AFTER section");
}
$schoolYearColumn = $conn->query("SHOW COLUMNS FROM cadets LIKE 'school_year'");
if (!$schoolYearColumn || $schoolYearColumn->num_rows === 0) {
    $conn->query("ALTER TABLE cadets ADD COLUMN school_year VARCHAR(9) DEFAULT NULL AFTER status");
}

$conn->query("INSERT IGNORE INTO platoons (name) VALUES
    ('Alpha First'), ('Alpha Second'), ('Alpha Third'),
    ('Bravo First'), ('Bravo Second'), ('Bravo Third'),
    ('Charlie First'), ('Charlie Second'), ('Charlie Third'),
    ('Delta First'), ('Delta Second'), ('Delta Third'),
    ('Echo First'), ('Echo Second'), ('Echo Third'),
    ('Foxtrot First'), ('Foxtrot Second'), ('Foxtrot Third'),
    ('HQ'), ('Medics')");

$officer = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'officer' AND status = 'Active' LIMIT 1");
$officer->bind_param('i', $officerId);
$officer->execute();
if ($officer->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Officer account is not valid.']);
    exit;
}
$officer->close();

if ($action === 'drop') {
    $currentSchoolYear = date('n') >= 6 ? date('Y') . '-' . (date('Y') + 1) : (date('Y') - 1) . '-' . date('Y');
    $stmt = $conn->prepare("UPDATE cadets SET company = NULL, platoon_id = NULL, status = 'Withdrawn', school_year = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND status = 'Active'");
    $stmt->bind_param('si', $currentSchoolYear, $cadetId);
    $stmt->execute();
    echo json_encode($stmt->affected_rows > 0
        ? ['success' => true, 'message' => 'Cadet dropped from the current roster.']
        : ['success' => false, 'message' => 'Cadet was not found or is already unassigned.']);
    $stmt->close();
    $conn->close();
    exit;
}

$allowedPlatoons = $company === 'HQ'
    ? ['HQ', 'Medics']
    : [str_replace(' Company', '', $company) . ' First', str_replace(' Company', '', $company) . ' Second', str_replace(' Company', '', $company) . ' Third'];
if (!in_array($platoonName, $allowedPlatoons, true)) {
    echo json_encode(['success' => false, 'message' => 'That platoon does not belong to the selected company.']);
    exit;
}

$platoon = $conn->prepare("SELECT id, name FROM platoons WHERE name = ? AND status = 'Active' LIMIT 1");
$platoon->bind_param('s', $platoonName);
$platoon->execute();
$platoonRecord = $platoon->get_result()->fetch_assoc();
$platoon->close();
if (!$platoonRecord) {
    echo json_encode(['success' => false, 'message' => 'Platoon was not found.']);
    exit;
}

$stmt = $conn->prepare("UPDATE cadets SET company = ?, platoon_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND status = 'Active'");
$stmt->bind_param('sii', $company, $platoonRecord['id'], $cadetId);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Cadet was not found or the platoon did not change.']);
} else {
    echo json_encode(['success' => true, 'message' => 'Cadet assigned to ' . $company . ' Company, ' . $platoonRecord['name'] . ' Platoon.']);
}

$stmt->close();
$conn->close();
?>
