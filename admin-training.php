<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

$conn->query("ALTER TABLE training_sessions ADD COLUMN IF NOT EXISTS instruction TEXT DEFAULT NULL AFTER location");
$conn->query("ALTER TABLE training_sessions ADD COLUMN IF NOT EXISTS is_generated TINYINT(1) NOT NULL DEFAULT 0 AFTER instruction");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$conn->query("UPDATE training_sessions SET is_generated = 1 WHERE is_generated = 0 AND (
    title IN ('Morning Formation', 'Formation Attendance', 'Formation 1', 'Formation 2')
    OR (title = training_type AND start_time = '00:00:00' AND end_time = '00:00:00')
    OR (start_time = '00:00:00' AND end_time = '00:00:00' AND location = 'Training Ground')
)");

$result = $conn->query(
    "SELECT training_sessions.id, training_sessions.training_date,
            training_sessions.training_type, training_sessions.title,
            training_sessions.start_time, training_sessions.end_time,
            COALESCE(NULLIF(TRIM(training_sessions.location), ''), 'Training Ground') AS location,
            COALESCE(NULLIF(TRIM(training_sessions.instruction), ''), NULL) AS instruction,
            training_sessions.created_by,
            COALESCE(NULLIF(TRIM(users.full_name), ''), 'N/A') AS officer_name,
            training_sessions.created_at
     FROM training_sessions
     INNER JOIN users ON users.id = training_sessions.created_by
     WHERE users.role = 'officer' AND COALESCE(training_sessions.is_generated, 0) = 0
     ORDER BY training_sessions.training_date DESC, training_sessions.start_time ASC"
);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Unable to load training schedules.']);
    $conn->close();
    exit;
}

$sessions = [];
while ($session = $result->fetch_assoc()) {
    $sessions[] = $session;
}

echo json_encode(['success' => true, 'sessions' => $sessions]);
$conn->close();
?>
