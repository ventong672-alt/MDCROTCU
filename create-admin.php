<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../config/database.php";

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$full_name = trim($_POST["full_name"] ?? "");

if (!$email || !$password) {
    echo json_encode([
        "success" => false,
        "message" => "Email and password are required"
    ]);
    exit;
}

try {
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        echo json_encode([
            "success" => false,
            "message" => "Admin with this email already exists"
        ]);
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert new admin
    $insert = $pdo->prepare("
        INSERT INTO admins (admin_id, full_name, email, password, status)
        VALUES (?, ?, ?, ?, 'ACTIVE')
    ");

    $admin_id = 'ADM' . time();
    $insert->execute([
        $admin_id,
        $full_name ?: 'Administrator',
        $email,
        $hashed_password
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Admin account created successfully",
        "admin" => [
            "admin_id" => $admin_id,
            "email" => $email,
            "full_name" => $full_name
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>
