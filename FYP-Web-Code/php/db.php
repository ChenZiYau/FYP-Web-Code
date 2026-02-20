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

    // 6. Expenses table
    $pdo->exec("CREATE TABLE IF NOT EXISTS expenses (
        id           INT AUTO_INCREMENT PRIMARY KEY,
        user_id      INT NOT NULL,
        amount       DECIMAL(10,2) NOT NULL,
        category     ENUM('food','transport','shopping','entertainment','education','health','bills','other') NOT NULL DEFAULT 'other',
        description  VARCHAR(255),
        expense_date DATE NOT NULL,
        created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 7. Budgets table (one row per user)
    $pdo->exec("CREATE TABLE IF NOT EXISTS budgets (
        id                   INT AUTO_INCREMENT PRIMARY KEY,
        user_id              INT NOT NULL UNIQUE,
        total_budget         DECIMAL(10,2) DEFAULT 0,
        food_budget          DECIMAL(10,2) DEFAULT 0,
        transport_budget     DECIMAL(10,2) DEFAULT 0,
        shopping_budget      DECIMAL(10,2) DEFAULT 0,
        entertainment_budget DECIMAL(10,2) DEFAULT 0,
        education_budget     DECIMAL(10,2) DEFAULT 0,
        health_budget        DECIMAL(10,2) DEFAULT 0,
        bills_budget         DECIMAL(10,2) DEFAULT 0,
        other_budget         DECIMAL(10,2) DEFAULT 0,
        updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 8. Finance settings (income / starting balance per user)
    $pdo->exec("CREATE TABLE IF NOT EXISTS finance_settings (
        id               INT AUTO_INCREMENT PRIMARY KEY,
        user_id          INT NOT NULL UNIQUE,
        starting_balance DECIMAL(10,2) DEFAULT 0,
        main_income      DECIMAL(10,2) DEFAULT 0,
        side_income      DECIMAL(10,2) DEFAULT 0,
        updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 9. Feedback table (landing page contact form — no login required)
    $pdo->exec("CREATE TABLE IF NOT EXISTS feedback (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        name       VARCHAR(100),
        email      VARCHAR(100),
        subject    VARCHAR(255),
        message    TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 10. Auto-create Admin if missing
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