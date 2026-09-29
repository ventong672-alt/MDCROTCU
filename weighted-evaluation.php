<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

$conn->query("CREATE TABLE IF NOT EXISTS rotc_grading_components (
    id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    component_key VARCHAR(50) NOT NULL,
    label VARCHAR(100) NOT NULL,
    weight DECIMAL(5,2) NOT NULL,
    sort_order TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_grading_component_key (component_key)
) ENGINE=InnoDB");
$conn->query("CREATE TABLE IF NOT EXISTS rotc_weighted_scores (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cadet_id BIGINT UNSIGNED NOT NULL,
    semester VARCHAR(30) NOT NULL DEFAULT 'First Semester',
    component_key VARCHAR(50) NOT NULL,
    score DECIMAL(5,2) NOT NULL,
    recorded_by BIGINT UNSIGNED DEFAULT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_weighted_score (cadet_id, semester, component_key)
) ENGINE=InnoDB");

$hiddenComponentKeys = ['military_training', 'practical'];

$defaults = [
    ['attendance', 'Attendance', 25, 1],
    ['examinations', 'Examinations / Written Work', 50, 2],
    ['conduct', 'Attitude / Merit & Demerit', 25, 3]
];
foreach ($defaults as $component) {
    $stmt = $conn->prepare('INSERT IGNORE INTO rotc_grading_components (component_key, label, weight, sort_order) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssdi', $component[0], $component[1], $component[2], $component[3]);
    $stmt->execute();
    $stmt->close();
}

$legacyWeights = $conn->query("SELECT COUNT(*) AS total FROM rotc_grading_components WHERE (component_key = 'attendance' AND weight = 20) OR (component_key = 'military_training' AND weight = 25) OR (component_key = 'examinations' AND weight = 20) OR (component_key = 'practical' AND weight = 20) OR (component_key = 'conduct' AND weight = 15)");
if ($legacyWeights && (int)$legacyWeights->fetch_assoc()['total'] === 5) {
    $conn->query("UPDATE rotc_grading_components SET weight = CASE component_key WHEN 'attendance' THEN 25 WHEN 'military_training' THEN 0 WHEN 'examinations' THEN 50 WHEN 'practical' THEN 0 WHEN 'conduct' THEN 25 ELSE weight END, label = CASE WHEN component_key = 'conduct' THEN 'Attitude / Merit & Demerit' ELSE label END WHERE component_key IN ('attendance', 'military_training', 'examinations', 'practical', 'conduct')");
}
$conn->query("DELETE FROM rotc_grading_components WHERE component_key IN ('military_training', 'practical')");

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $semester = trim($_GET['semester'] ?? 'First Semester');
    $cadetId = (int)($_GET['cadet_id'] ?? 0);
    $components = [];
    $result = $conn->query('SELECT component_key, label, weight, sort_order FROM rotc_grading_components ORDER BY sort_order ASC');
    while ($row = $result->fetch_assoc()) {
        if (in_array($row['component_key'], $hiddenComponentKeys, true)) {
            continue;
        }
        $components[] = $row;
    }
    $scores = [];
    $evaluations = [];
    if ($cadetId) {
        $stmt = $conn->prepare('SELECT component_key, score FROM rotc_weighted_scores WHERE cadet_id = ? AND semester = ?');
        $stmt->bind_param('is', $cadetId, $semester);
        $stmt->execute();
        $scoreResult = $stmt->get_result();
        while ($row = $scoreResult->fetch_assoc()) $scores[$row['component_key']] = (float)$row['score'];
        $stmt->close();
    } else {
        $evaluationResult = $conn->query("SELECT s.cadet_id, c.cadet_id AS cadet_code, c.full_name, s.semester, s.component_key, s.score FROM rotc_weighted_scores s INNER JOIN cadets c ON c.id = s.cadet_id WHERE c.status = 'Active' ORDER BY c.full_name ASC, s.semester ASC");
        while ($row = $evaluationResult->fetch_assoc()) {
            $key = $row['cadet_id'] . '-' . $row['semester'];
            if (!isset($evaluations[$key])) {
                $evaluations[$key] = ['cadet_id' => $row['cadet_id'], 'cadet_code' => $row['cadet_code'], 'full_name' => $row['full_name'], 'semester' => $row['semester'], 'scores' => [], 'final_grade' => null];
            }
            $evaluations[$key]['scores'][$row['component_key']] = (float)$row['score'];
        }
        foreach ($evaluations as &$evaluation) {
            if (count($evaluation['scores']) === count($components)) {
                $evaluation['final_grade'] = 0;
                foreach ($components as $component) $evaluation['final_grade'] += $evaluation['scores'][$component['component_key']] * ((float)$component['weight'] / 100);
                $evaluation['final_grade'] = round($evaluation['final_grade'], 2);
            }
        }
        unset($evaluation);
        $evaluations = array_values($evaluations);
    }
    echo json_encode(['success' => true, 'components' => $components, 'scores' => $scores, 'evaluations' => $evaluations]);
    $conn->close();
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    if (($data['action'] ?? '') === 'configure') {
        $adminId = (int)($data['admin_id'] ?? 0);
        $components = $data['components'] ?? [];
        $check = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'admin' AND status = 'Active' LIMIT 1");
        $check->bind_param('i', $adminId);
        $check->execute();
        if ($check->get_result()->num_rows === 0 || !is_array($components)) {
            echo json_encode(['success' => false, 'message' => 'Admin account is not valid.']);
            exit;
        }
        $check->close();
        $filteredComponents = array_values(array_filter($components, static fn($component) => isset($component['component_key']) && !in_array($component['component_key'], ['military_training', 'practical'], true)));
        $total = 0;
        foreach ($filteredComponents as $component) {
            if (!isset($component['component_key'], $component['weight']) || !is_numeric($component['weight']) || $component['weight'] < 0) {
                echo json_encode(['success' => false, 'message' => 'Weights must be valid numbers.']);
                exit;
            }
            $total += (float)$component['weight'];
        }
        if (abs($total - 100) > 0.01) {
            echo json_encode(['success' => false, 'message' => 'Component weights must total exactly 100%.']);
            exit;
        }
        foreach ($filteredComponents as $component) {
            $weight = (float)$component['weight'];
            $stmt = $conn->prepare('UPDATE rotc_grading_components SET weight = ? WHERE component_key = ?');
            $stmt->bind_param('ds', $weight, $component['component_key']);
            $stmt->execute();
            $stmt->close();
        }
        echo json_encode(['success' => true, 'message' => 'Grading weights updated successfully.']);
        $conn->close();
        exit;
    }
    $officerId = (int)($data['officer_id'] ?? 0);
    $cadetId = (int)($data['cadet_id'] ?? 0);
    $semester = trim($data['semester'] ?? 'First Semester');
    $scores = $data['scores'] ?? [];
    if (!$officerId || !$cadetId || !in_array($semester, ['First Semester', 'Second Semester'], true) || !is_array($scores)) {
        echo json_encode(['success' => false, 'message' => 'Complete the weighted evaluation fields.']);
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
    $allowed = ['attendance', 'examinations', 'conduct'];
    foreach ($allowed as $key) {
        if (!isset($scores[$key]) || !is_numeric($scores[$key]) || $scores[$key] < 0 || $scores[$key] > 100) {
            echo json_encode(['success' => false, 'message' => 'Enter a score from 0 to 100 for every component.']);
            exit;
        }
        $score = (float)$scores[$key];
        $stmt = $conn->prepare('INSERT INTO rotc_weighted_scores (cadet_id, semester, component_key, score, recorded_by) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE score = VALUES(score), recorded_by = VALUES(recorded_by), updated_at = CURRENT_TIMESTAMP');
        $stmt->bind_param('issdi', $cadetId, $semester, $key, $score, $officerId);
        $stmt->execute();
        $stmt->close();
    }
    echo json_encode(['success' => true, 'message' => 'Weighted evaluation saved successfully.']);
    $conn->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
?>
