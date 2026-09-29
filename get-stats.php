<?php
/**
 * Get Dashboard Statistics
 * Returns statistics for admin dashboard
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'database.php';

// Get total cadets
$validCadet = "password LIKE CONCAT(CHAR(36), '2%')";
$validOfficer = "password LIKE CONCAT(CHAR(36), '2%')";

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM cadets WHERE status = 'Active' AND $validCadet");
$stmt->execute();
$result = $stmt->get_result();
$totalCadets = $result->fetch_assoc()['count'];

// Get active cadets
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM cadets WHERE status = 'Active' AND $validCadet");
$stmt->execute();
$result = $stmt->get_result();
$activeCadets = $result->fetch_assoc()['count'];

// Get pending cadets
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM cadets WHERE status = 'Pending' AND $validCadet");
$stmt->execute();
$result = $stmt->get_result();
$pendingCadets = $result->fetch_assoc()['count'];

// Get total officers (users with role 'officer')
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'officer' AND status = 'Active' AND $validOfficer");
$stmt->execute();
$result = $stmt->get_result();
$totalOfficers = $result->fetch_assoc()['count'];

// Get pending officers
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'officer' AND status = 'Pending' AND $validOfficer");
$stmt->execute();
$result = $stmt->get_result();
$pendingOfficers = $result->fetch_assoc()['count'];

echo json_encode([
    'success' => true,
    'totalCadets' => (int)$totalCadets,
    'activeCadets' => (int)$activeCadets,
    'pendingCadets' => (int)$pendingCadets,
    'totalOfficers' => (int)$totalOfficers,
    'pendingOfficers' => (int)$pendingOfficers
]);

$stmt->close();
$conn->close();
?>
