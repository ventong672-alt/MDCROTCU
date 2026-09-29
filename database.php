<?php

date_default_timezone_set('Asia/Manila');

$host = "localhost";
$dbname = "rotc_db";
$username = "root";
$password = "";

try {
    // First, try to connect to the specific database
    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password
        );
    } catch (PDOException $e) {
        // If database doesn't exist, create it
        if (strpos($e->getMessage(), 'Unknown database') !== false) {
            // Connect to MySQL server without database
            $pdo = new PDO(
                "mysql:host=$host;charset=utf8mb4",
                $username,
                $password
            );
            
            // Create the database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
            
            // Now connect to the newly created database
            $pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password
            );
        } else {
            throw $e;
        }
    }

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("SET time_zone = '+08:00'");

} catch (PDOException $e) {
    die(json_encode([
        "success" => false,
        "error" => "Database connection failed",
        "message" => $e->getMessage(),
        "hint" => "Make sure XAMPP MySQL is running and credentials are correct"
    ]));
}
?>