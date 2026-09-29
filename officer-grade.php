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
$subject = trim((string)($data['subject'] ?? 'ROTC Leadership'));
$semester = trim((string)($data['semester'] ?? 'First Semester'));
$term = trim($data['term'] ?? '');
$score = $data['score'] ?? null;
$professionalEvaluation = trim((string)($data['professional_evaluation'] ?? 'Satisfactory'));
$allowedTerms = ['Prelim', 'Midterm', 'Finals'];
$allowedSemesters = ['First Semester', 'Second Semester'];

if (!$officerId || !$cadetId || $subject === '' || !in_array($semester, $allowedSemesters, true) || !in_array($term, $allowedTerms, true) || !is_numeric($score) || $score < 0 || $score > 100) {
    echo json_encode(['success' => false, 'message' => 'Complete all fields and enter a score from 0 to 100.']);
    exit;
}

$check = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'officer' AND status = 'Active' LIMIT 1");
$check->bind_param('i', $officerId);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Officer account is not approved.']);
    exit;
}
$check->close();

$checkCadet = $conn->prepare("SELECT id FROM cadets WHERE id = ? AND status = 'Active' LIMIT 1");
$checkCadet->bind_param('i', $cadetId);
$checkCadet->execute();
if ($checkCadet->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Cadet is not approved or was not found.']);
    exit;
}
$checkCadet->close();

$numericScore = (float)$score;

$conn->query("ALTER TABLE grade_records ADD COLUMN IF NOT EXISTS semester VARCHAR(30) NOT NULL DEFAULT 'First Semester' AFTER subject");
$conn->query("ALTER TABLE grade_records ADD COLUMN IF NOT EXISTS professional_evaluation VARCHAR(50) DEFAULT NULL AFTER term");

$columnsStmt = $conn->query("SHOW COLUMNS FROM grade_records LIKE 'professional_evaluation'");
$hasEvaluationColumn = $columnsStmt && $columnsStmt->num_rows > 0;

if ($hasEvaluationColumn) {
    $stmt = $conn->prepare('INSERT INTO grade_records (cadet_id, subject, semester, category, score, term, professional_evaluation, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE score = VALUES(score), category = VALUES(category), professional_evaluation = VALUES(professional_evaluation), recorded_by = VALUES(recorded_by), updated_at = CURRENT_TIMESTAMP');
    $category = $professionalEvaluation;
    $stmt->bind_param('isssdssi', $cadetId, $subject, $semester, $category, $numericScore, $term, $professionalEvaluation, $officerId);
} else {
    $stmt = $conn->prepare('INSERT INTO grade_records (cadet_id, subject, semester, category, score, term, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE score = VALUES(score), category = VALUES(category), recorded_by = VALUES(recorded_by), updated_at = CURRENT_TIMESTAMP');
    $category = $professionalEvaluation;
    $stmt->bind_param('isssdsi', $cadetId, $subject, $semester, $category, $numericScore, $term, $officerId);
}

echo json_encode($stmt->execute()
    ? ['success' => true, 'message' => 'ROTC evaluation saved successfully.']
    : ['success' => false, 'message' => 'Unable to save evaluation.']);
$stmt->close();
$conn->close();
?>
