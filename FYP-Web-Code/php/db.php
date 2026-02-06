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
    
    // 3. Select the DB
    $pdo->exec("USE `$dbname`"); 

    // 4. Create Users Table (Updated with level & xp)
    $userSql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50),
        last_name VARCHAR(50),
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        last_login DATE NULL,
        streak_count INT DEFAULT 0,
        level INT DEFAULT 1,      /* Added this */
        xp INT DEFAULT 0,         /* Added this */
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($userSql);

    // 5. Create Tasks Table (New!)
    $taskSql = "CREATE TABLE IF NOT EXISTS tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        due_date DATE,
        priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
        status ENUM('pending', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($taskSql);

    // 6. Auto-create Admin if missing
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'admin@optiplan.com'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $adminPass = password_hash('12345', PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (first_name, last_name, email, password_hash, role, streak_count) 
                VALUES ('System', 'Admin', 'admin@optiplan.com', ?, 'admin', 0)";
        $pdo->prepare($sql)->execute([$adminPass]);
        // echo "Admin account created successfully.<br>";  <-- DELETE OR COMMENT THIS
    }

    // echo "Database setup completed successfully!";      <-- DELETE OR COMMENT THIS

} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database Config Error: ' . $e->getMessage()]));
}
?>