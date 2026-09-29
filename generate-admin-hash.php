<?php
/**
 * Admin Password Hash Generator
 * 
 * Use this script to generate bcrypt password hashes for creating new admin accounts
 * Simply modify the $password variable and run this script in your browser or CLI
 */

// Sample passwords for admin accounts
$admin_accounts = [
    [
        'name' => 'Administrator',
        'email' => 'admin@mdci.edu.ph',
        'password' => 'admin123'
    ],
    [
        'name' => 'Super Administrator',
        'email' => 'superadmin@mdci.edu.ph',
        'password' => 'superadmin123'
    ],
    [
        'name' => 'Operations Admin',
        'email' => 'operations@mdci.edu.ph',
        'password' => 'operations123'
    ]
];

echo "=== ADMIN PASSWORD HASH GENERATOR ===\n";
echo "This script generates bcrypt hashes for admin credentials.\n\n";

foreach ($admin_accounts as $account) {
    $hashed = password_hash($account['password'], PASSWORD_BCRYPT);
    
    echo "===================================\n";
    echo "Name: " . $account['name'] . "\n";
    echo "Email: " . $account['email'] . "\n";
    echo "Password: " . $account['password'] . "\n";
    echo "Hashed: " . $hashed . "\n";
    echo "SQL INSERT:\n";
    echo "INSERT INTO users (full_name, email, password, role, status)\n";
    echo "VALUES (\n";
    echo "  '" . $account['name'] . "',\n";
    echo "  '" . $account['email'] . "',\n";
    echo "  '" . $hashed . "',\n";
    echo "  'admin',\n";
    echo "  'Active'\n";
    echo ");\n\n";
}

/**
 * INTERACTIVE: Generate hash for a custom password
 * Uncomment below and set your password
 */
/*
$custom_password = "your_secure_password_here";
$custom_hash = password_hash($custom_password, PASSWORD_BCRYPT);

echo "===== CUSTOM PASSWORD HASH =====\n";
echo "Password: " . $custom_password . "\n";
echo "Hashed: " . $custom_hash . "\n";
*/

// Function to verify a password against a hash (for testing)
// Verifies a plain password against a stored password hash.
function verify_password($plain_password, $hashed_password) {
    return password_verify($plain_password, $hashed_password);
}

echo "\n=== PASSWORD VERIFICATION TEST ===\n";
$test_hash = '$2y$10$1DXiVb6ZxpH3.FEZHpxH5eWD6fNHMuGNnJMC7v6ZNB1JAP2F8pAKy';
$test_password = 'admin123';
$is_valid = verify_password($test_password, $test_hash);
echo "Test Password: " . $test_password . "\n";
echo "Is Valid: " . ($is_valid ? "YES" : "NO") . "\n";
?>
