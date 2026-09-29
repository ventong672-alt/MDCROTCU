<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../config/database.php";

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'test-database') {
    // Test database connection
    try {
        $result = $pdo->query("SELECT 1")->fetch();
        echo json_encode([
            "success" => true,
            "message" => "Database connection successful"
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Database connection failed: " . $e->getMessage()
        ]);
    }
} elseif ($action === 'check-admin') {
    // Check if admin exists
    try {
        $email = $_POST['email'] ?? '';
        
        if (!$email) {
            echo json_encode([
                "success" => false,
                "message" => "Email is required"
            ]);
            exit;
        }

        $stmt = $pdo->prepare("SELECT id, email, full_name, status FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin) {
            echo json_encode([
                "success" => true,
                "admin_exists" => true,
                "admin" => $admin
            ]);
        } else {
            echo json_encode([
                "success" => true,
                "admin_exists" => false,
                "message" => "Admin with this email does not exist"
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error checking admin: " . $e->getMessage()
        ]);
    }
} elseif ($action === 'test-login') {
    // Test login with provided credentials
    try {
        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";

        if (!$email || !$password) {
            echo json_encode([
                "success" => false,
                "message" => "Email and password are required"
            ]);
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if (!$admin) {
            echo json_encode([
                "success" => false,
                "message" => "Admin not found"
            ]);
            exit;
        }

        // Test password verification
        $password_matches = password_verify($password, $admin["password"]);

        if (!$password_matches) {
            echo json_encode([
                "success" => false,
                "message" => "Password is incorrect",
                "debug" => [
                    "password_entered" => $password,
                    "hash_in_database" => $admin["password"],
                    "verification_result" => "Failed"
                ]
            ]);
            exit;
        }

        if ($admin["status"] !== "ACTIVE") {
            echo json_encode([
                "success" => false,
                "message" => "Admin account is not active. Status: " . $admin["status"]
            ]);
            exit;
        }

        // Login successful
        echo json_encode([
            "success" => true,
            "message" => "Login test successful",
            "admin" => [
                "id" => $admin["id"],
                "admin_id" => $admin["admin_id"],
                "full_name" => $admin["full_name"],
                "email" => $admin["email"],
                "status" => $admin["status"]
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error testing login: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid action"
    ]);
}
?>
