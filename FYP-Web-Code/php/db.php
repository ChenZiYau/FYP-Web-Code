<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Default for XAMPP
$dbname = 'optiplan_db';

try {
    // 1. Connect to MySQL
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Create DB if missing
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4");
    
    // 3. Select the DB properly (Stray 'text' removed here)
    $pdo->exec("USE `$dbname` "); 

    // 4. Create table with your specific form fields
    $tableSql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50),
        last_name VARCHAR(50),
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($tableSql);

    // 5. Auto-create Admin if missing
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'admin@optiplan.com'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $adminPass = password_hash('12345', PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (first_name, last_name, email, password_hash, role) 
                VALUES ('System', 'Admin', 'admin@optiplan.com', ?, 'admin')";
        $pdo->prepare($sql)->execute([$adminPass]);
    }
} catch (PDOException $e) {
    // If this dies, the error comes from this file!
    die(json_encode(['success' => false, 'message' => 'Database Config Error: ' . $e->getMessage()]));
}
?>