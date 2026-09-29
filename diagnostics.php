<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$diagnostics = [
    "timestamp" => date('Y-m-d H:i:s'),
    "server" => [
        "php_version" => phpversion(),
        "mysql_support" => extension_loaded('pdo_mysql') ? "✅ Yes" : "❌ No",
        "server_api" => php_sapi_name(),
        "os" => php_uname()
    ],
    "connection_test" => []
];

// Test MySQL Connection
try {
    $host = "localhost";
    $username = "root";
    $password = "";
    
    // Test 1: Connect to MySQL server without database
    try {
        $pdo_test = new PDO(
            "mysql:host=$host;charset=utf8mb4",
            $username,
            $password
        );
        $diagnostics["connection_test"]["mysql_server"] = "✅ Connected to MySQL server";
    } catch (PDOException $e) {
        $diagnostics["connection_test"]["mysql_server"] = "❌ Failed: " . $e->getMessage();
        echo json_encode($diagnostics);
        exit;
    }
    
    // Test 2: Check the active database
    $dbname = "mdcrotcu_db";
    try {
        $databaseExists = $pdo_test->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = " . $pdo_test->quote($dbname))->fetchColumn();
        $diagnostics["connection_test"]["database_exists"] = $databaseExists ? "✅ Database exists" : "❌ Database does not exist";
        if (!$databaseExists) {
            echo json_encode($diagnostics);
            exit;
        }
    } catch (PDOException $e) {
        $diagnostics["connection_test"]["create_database"] = "❌ Failed: " . $e->getMessage();
    }
    
    // Test 3: Connect to database
    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $diagnostics["connection_test"]["database_connection"] = "✅ Connected to database";
    } catch (PDOException $e) {
        $diagnostics["connection_test"]["database_connection"] = "❌ Failed: " . $e->getMessage();
        echo json_encode($diagnostics);
        exit;
    }
    
    // Test 4: Check tables
    $tables = [
        "users" => false,
        "cadets" => false,
        "training_sessions" => false
    ];
    
    $result = $pdo->query("SHOW TABLES")->fetchAll();
    $table_names = array_column($result, 0);
    
    foreach ($tables as $table => $value) {
        $tables[$table] = in_array($table, $table_names);
    }
    
    $diagnostics["connection_test"]["tables"] = $tables;
    
    // Test 5: Check active administrator account
    try {
        if ($tables["users"]) {
            $admin_count = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'")->fetch()['count'];
            $diagnostics["connection_test"]["admin_account"] = "✅ Users table has " . $admin_count . " administrator account(s)";
            
            if ($admin_count > 0) {
                $admin = $pdo->query("SELECT email, full_name, status FROM users WHERE role = 'admin' LIMIT 1")->fetch();
                $diagnostics["connection_test"]["admin_details"] = [
                    "email" => $admin['email'],
                    "full_name" => $admin['full_name'],
                    "status" => $admin['status']
                ];
            }
        } else {
            $diagnostics["connection_test"]["admin_account"] = "⚠️ Users table doesn't exist (run database/schema.sql)";
        }
    } catch (Exception $e) {
        $diagnostics["connection_test"]["admin_account"] = "❌ Error: " . $e->getMessage();
    }
    
    $diagnostics["status"] = "✅ All systems operational";
    
} catch (Exception $e) {
    $diagnostics["status"] = "❌ Error: " . $e->getMessage();
}

echo json_encode($diagnostics, JSON_PRETTY_PRINT);
?>
