<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$conn->query("ALTER TABLE grade_records ADD COLUMN IF NOT EXISTS semester VARCHAR(30) NOT NULL DEFAULT 'First Semester' AFTER subject");
$conn->query("ALTER TABLE grade_records ADD COLUMN IF NOT EXISTS professional_evaluation VARCHAR(50) DEFAULT NULL AFTER term");

$evalCols = $conn->query("SHOW COLUMNS FROM grade_records LIKE 'professional_evaluation'");
$hasEvaluation = $evalCols && $evalCols->num_rows > 0;
$actionCols = $conn->query("SHOW COLUMNS FROM grade_records LIKE 'action_taken'");
$hasAction = $actionCols && $actionCols->num_rows > 0;

$selectFields = "SELECT grade_records.cadet_id, cadets.cadet_id AS cadet_code, cadets.full_name,
            grade_records.subject, grade_records.semester, grade_records.term, grade_records.score";
if ($hasEvaluation) {
    $selectFields .= ", grade_records.professional_evaluation";
}
if ($hasAction) {
    $selectFields .= ", grade_records.action_taken";
}

$query = $selectFields . "
     FROM grade_records
     INNER JOIN cadets ON cadets.id = grade_records.cadet_id
     WHERE cadets.status = 'Active'
    ORDER BY cadets.full_name ASC, grade_records.subject ASC, grade_records.semester ASC, grade_records.term ASC";

$result = $conn->query($query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Unable to load grades.']);
    exit;
}

$grades = [];
while ($row = $result->fetch_assoc()) $grades[] = $row;
echo json_encode(['success' => true, 'grades' => $grades]);
$conn->close();
?>
