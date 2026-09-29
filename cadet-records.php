<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$cadetId = trim($_SESSION['cadet_id'] ?? '');
if ($cadetId === '') {
    echo json_encode(['success' => false, 'message' => 'Cadet login is required.']);
    exit;
}

$companyColumn = $conn->query("SHOW COLUMNS FROM cadets LIKE 'company'");
if (!$companyColumn || $companyColumn->num_rows === 0) {
    $conn->query("ALTER TABLE cadets ADD COLUMN company VARCHAR(80) DEFAULT NULL AFTER section");
}

$cadetStmt = $conn->prepare('SELECT c.id, c.cadet_id, c.full_name, c.year_level, c.section, c.company, COALESCE(p.name, \'\') AS platoon, c.status FROM cadets c LEFT JOIN platoons p ON p.id = c.platoon_id WHERE c.cadet_id = ? AND c.status = \'Active\' LIMIT 1');
$cadetStmt->bind_param('s', $cadetId);
$cadetStmt->execute();
$cadet = $cadetStmt->get_result()->fetch_assoc();

if (!$cadet) {
    echo json_encode(['success' => false, 'message' => 'Approved cadet record not found.']);
    exit;
}

$grades = [];
$columns = $conn->query("SHOW COLUMNS FROM grade_records LIKE 'professional_evaluation'");
$hasEvaluation = $columns && $columns->num_rows > 0;

$gradeQuery = 'SELECT subject, category, score AS grade, semester, term';
if ($hasEvaluation) {
    $gradeQuery .= ', professional_evaluation';
}
$gradeQuery .= ' FROM grade_records WHERE cadet_id = ? ORDER BY subject ASC, term ASC';

$gradeStmt = $conn->prepare($gradeQuery);
$gradeStmt->bind_param('i', $cadet['id']);
$gradeStmt->execute();
$gradeResult = $gradeStmt->get_result();
while ($row = $gradeResult->fetch_assoc()) $grades[] = $row;

$conduct = [];
$conductStmt = $conn->prepare("SELECT record_date AS date, LOWER(type) AS type, points, reason AS title FROM conduct_records WHERE cadet_id = ? ORDER BY record_date DESC");
$conductStmt->bind_param('i', $cadet['id']);
$conductStmt->execute();
$conductResult = $conductStmt->get_result();
while ($row = $conductResult->fetch_assoc()) $conduct[] = $row;

$attendance = [];
$conn->query("ALTER TABLE attendance_records ADD COLUMN IF NOT EXISTS time_out TIME DEFAULT NULL AFTER time_in");
$attendanceStmt = $conn->prepare("SELECT training_sessions.training_date AS date, attendance_records.status, attendance_records.time_in, attendance_records.time_out, COALESCE(attendance_records.remarks, '') AS remarks FROM attendance_records INNER JOIN training_sessions ON training_sessions.id = attendance_records.training_id WHERE attendance_records.cadet_id = ? ORDER BY training_sessions.training_date DESC");
$attendanceStmt->bind_param('i', $cadet['id']);
$attendanceStmt->execute();
$attendanceResult = $attendanceStmt->get_result();
while ($row = $attendanceResult->fetch_assoc()) $attendance[] = $row;

$weightedEvaluation = ['components' => [], 'scores' => [], 'final_grade' => null];
$weightedEvaluations = [];
$weightedTable = $conn->query("SHOW TABLES LIKE 'rotc_grading_components'");
if ($weightedTable && $weightedTable->num_rows > 0) {
    $legacyWeights = $conn->query("SELECT COUNT(*) AS total FROM rotc_grading_components WHERE (component_key = 'attendance' AND weight = 20) OR (component_key = 'military_training' AND weight = 25) OR (component_key = 'examinations' AND weight = 20) OR (component_key = 'practical' AND weight = 20) OR (component_key = 'conduct' AND weight = 15)");
    if ($legacyWeights && (int)$legacyWeights->fetch_assoc()['total'] === 5) {
        $conn->query("UPDATE rotc_grading_components SET weight = CASE component_key WHEN 'attendance' THEN 25 WHEN 'military_training' THEN 0 WHEN 'examinations' THEN 50 WHEN 'practical' THEN 0 WHEN 'conduct' THEN 25 ELSE weight END, label = CASE WHEN component_key = 'conduct' THEN 'Attitude / Merit & Demerit' ELSE label END WHERE component_key IN ('attendance', 'military_training', 'examinations', 'practical', 'conduct')");
    }
    $componentResult = $conn->query('SELECT component_key, label, weight FROM rotc_grading_components ORDER BY sort_order ASC');
    while ($component = $componentResult->fetch_assoc()) {
        if (in_array($component['component_key'], ['military_training', 'practical'], true)) {
            continue;
        }
        $weightedEvaluation['components'][] = $component;
    }
    $weightedStmt = $conn->prepare("SELECT component_key, score FROM rotc_weighted_scores WHERE cadet_id = ? AND semester = 'First Semester'");
    $weightedStmt->bind_param('i', $cadet['id']);
    $weightedStmt->execute();
    $weightedResult = $weightedStmt->get_result();
    while ($score = $weightedResult->fetch_assoc()) $weightedEvaluation['scores'][$score['component_key']] = (float)$score['score'];
    $weightedStmt->close();
    $gradeScores = array_values(array_filter(array_map(static fn($row) => is_numeric($row['grade']) ? (float)$row['grade'] : null, $grades), static fn($score) => $score !== null));
    $gradeAverage = $gradeScores ? array_sum($gradeScores) / count($gradeScores) : null;
    $attendanceTotal = count($attendance);
    $attendancePresent = count(array_filter($attendance, static fn($row) => in_array($row['status'], ['Present', 'Excused'], true)));
    $automaticScores = [
        'attendance' => $attendanceTotal ? ($attendancePresent / $attendanceTotal) * 100 : null,
        'examinations' => $gradeAverage,
        'conduct' => max(0, min(100, 100 + array_sum(array_map(static fn($row) => strtolower($row['type']) === 'merit' ? (float)$row['points'] : -(float)$row['points'], $conduct))))
    ];
    foreach ($automaticScores as $key => $score) {
        if (!array_key_exists($key, $weightedEvaluation['scores']) && $score !== null) {
            $weightedEvaluation['scores'][$key] = round($score, 2);
        }
    }
    if (count($weightedEvaluation['components']) > 0 && count($weightedEvaluation['scores']) === count($weightedEvaluation['components'])) {
        $weightedEvaluation['final_grade'] = 0;
        foreach ($weightedEvaluation['components'] as $component) {
            $weightedEvaluation['final_grade'] += $weightedEvaluation['scores'][$component['component_key']] * ((float)$component['weight'] / 100);
        }
        $weightedEvaluation['final_grade'] = round($weightedEvaluation['final_grade'], 2);
    }

    $allWeightedStmt = $conn->prepare('SELECT semester, component_key, score FROM rotc_weighted_scores WHERE cadet_id = ? ORDER BY semester ASC');
    $allWeightedStmt->bind_param('i', $cadet['id']);
    $allWeightedStmt->execute();
    $allWeightedResult = $allWeightedStmt->get_result();
    while ($score = $allWeightedResult->fetch_assoc()) {
        $semester = $score['semester'];
        if (!isset($weightedEvaluations[$semester])) {
            $weightedEvaluations[$semester] = ['components' => $weightedEvaluation['components'], 'scores' => [], 'final_grade' => null];
        }
        $weightedEvaluations[$semester]['scores'][$score['component_key']] = (float)$score['score'];
    }
    $allWeightedStmt->close();
    foreach ($weightedEvaluations as &$semesterEvaluation) {
        if (count($semesterEvaluation['scores']) === count($semesterEvaluation['components'])) {
            $semesterEvaluation['final_grade'] = 0;
            foreach ($semesterEvaluation['components'] as $component) {
                $semesterEvaluation['final_grade'] += $semesterEvaluation['scores'][$component['component_key']] * ((float)$component['weight'] / 100);
            }
            $semesterEvaluation['final_grade'] = round($semesterEvaluation['final_grade'], 2);
        }
    }
    unset($semesterEvaluation);
}

echo json_encode([
    'success' => true,
    'cadet' => $cadet,
    'grades' => $grades,
    'conduct' => $conduct,
    'attendance' => $attendance,
    'weighted_evaluation' => $weightedEvaluation,
    'weighted_evaluations' => $weightedEvaluations
]);

$conn->close();
?>
