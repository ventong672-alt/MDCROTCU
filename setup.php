<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../config/database.php";

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'check-status') {
    // Check if tables exist
    try {
        $tables = $pdo->query("SHOW TABLES")->fetchAll();
        $table_names = array_column($tables, 0);
        
        $admins_exist = in_array('admins', $table_names);
        $officers_exist = in_array('officers', $table_names);
        $cadets_exist = in_array('cadets', $table_names);
        
        // Check if any admin exists
        $admin_count = 0;
        if ($admins_exist) {
            $result = $pdo->query("SELECT COUNT(*) as count FROM admins")->fetch();
            $admin_count = $result['count'] ?? 0;
        }
        
        echo json_encode([
            "success" => true,
            "database_connected" => true,
            "tables" => [
                "admins" => $admins_exist,
                "officers" => $officers_exist,
                "cadets" => $cadets_exist
            ],
            "admin_count" => $admin_count
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "database_connected" => false,
            "error" => $e->getMessage()
        ]);
    }
} elseif ($action === 'init-database') {
    // Create tables if they don't exist
    try {
        $sql_commands = [
            "CREATE TABLE IF NOT EXISTS admins (
                id INT AUTO_INCREMENT PRIMARY KEY,
                admin_id VARCHAR(50) UNIQUE NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                status VARCHAR(20) DEFAULT 'ACTIVE',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_login TIMESTAMP NULL
            )",
            "CREATE TABLE IF NOT EXISTS officers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                officer_id VARCHAR(50) UNIQUE NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                rank VARCHAR(50),
                status VARCHAR(20) DEFAULT 'PENDING',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_login TIMESTAMP NULL
            )",
            "CREATE TABLE IF NOT EXISTS cadets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cadet_id VARCHAR(50) UNIQUE NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                batch VARCHAR(50),
                status VARCHAR(20) DEFAULT 'ACTIVE',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_login TIMESTAMP NULL
            )"
        ];
        
        foreach ($sql_commands as $sql) {
            $pdo->exec($sql);
        }
        
        echo json_encode([
            "success" => true,
            "message" => "Database tables created successfully"
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error creating tables: " . $e->getMessage()
        ]);
    }
} elseif ($action === 'create-admin') {
    // Create admin account
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
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid action"
    ]);
}
?>
