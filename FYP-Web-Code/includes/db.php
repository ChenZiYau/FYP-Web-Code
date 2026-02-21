<?php
require_once __DIR__ . '/env_loader.php';
load_env();

$host   = env('DB_HOST', 'localhost');
$user   = env('DB_USER', 'root');
$pass   = env('DB_PASS', '');
$dbname = env('DB_NAME', 'optiplan_db');

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
        level INT DEFAULT 1,
        xp INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($userSql);

    // 5. Create Tasks Table
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

    // 9. Feedback table (landing page contact form)
    $pdo->exec("CREATE TABLE IF NOT EXISTS feedback (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        name       VARCHAR(100),
        email      VARCHAR(100),
        subject    VARCHAR(255),
        message    TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 10. Add username and pfp_path columns if missing
    $cols = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('username', $cols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN username VARCHAR(50) NULL UNIQUE AFTER last_name");
    }
    if (!in_array('pfp_path', $cols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN pfp_path VARCHAR(255) NULL AFTER username");
    }

    // 11. Site Content table (CMS for index.php)
    $pdo->exec("CREATE TABLE IF NOT EXISTS site_content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        section_key VARCHAR(100) UNIQUE NOT NULL,
        content_value TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Add default_value column if missing (stores admin-set defaults)
    $cols = array_column($pdo->query("SHOW COLUMNS FROM site_content")->fetchAll(PDO::FETCH_ASSOC), 'Field');
    if (!in_array('default_value', $cols)) {
        $pdo->exec("ALTER TABLE site_content ADD COLUMN default_value TEXT NULL AFTER content_value");
    }

    // Seed default site content if table is empty
    $contentCount = $pdo->query("SELECT COUNT(*) FROM site_content")->fetchColumn();
    if ($contentCount == 0) {
        $defaults = [
            // Hero
            ['hero_badge', 'AI-Powered Productivity'],
            ['hero_title_line1', 'One Dashboard.'],
            ['hero_title_line2', 'Organized.'],
            ['hero_description', 'Stop switching between different applications. Optiplan unifies your schedule, studies and budgeting into one singular intelligent platform, designed speifically for students and young interns.'],
            ['hero_cta_primary', 'Get Started Free'],
            ['hero_cta_secondary', 'Watch Demo'],
            ['hero_stat1_number', '75%+'],
            ['hero_stat1_label', 'Students use 3+ apps daily'],
            ['hero_stat2_number', '1'],
            ['hero_stat2_label', 'Platform solves it all'],
            // Hero Cards
            ['hero_card1_icon', '📅'],
            ['hero_card1_title', 'Smart Scheduling'],
            ['hero_card1_desc', 'AI-optimized calendar'],
            ['hero_card2_icon', '📚'],
            ['hero_card2_title', 'Study Notes'],
            ['hero_card2_desc', 'Personalized flip cards'],
            ['hero_card3_icon', '💰'],
            ['hero_card3_title', 'Budget Tracking'],
            ['hero_card3_desc', 'Financial awareness'],
            ['hero_card4_icon', '🔜'],
            ['hero_card4_title', 'Many More'],
            ['hero_card4_desc', 'More updates to come...'],
            // Problem Section
            ['problem_label', 'The Problem'],
            ['problem_title', 'Fragmented Productivity is Killing Your Time'],
            ['problem_card1_number', '01'],
            ['problem_card1_title', 'App Overload'],
            ['problem_card1_desc', 'Constantly switching between calendar apps, study tools, and budget trackers wastes valuable time and mental energy.'],
            ['problem_card2_number', '02'],
            ['problem_card2_title', 'Missed Tasks'],
            ['problem_card2_desc', 'Important deadlines and activities fall through the cracks when scattered across multiple disconnected platforms.'],
            ['problem_card3_number', '03'],
            ['problem_card3_title', 'No Integration'],
            ['problem_card3_desc', "Your schedule doesn't talk to your budget. Your study plan doesn't sync with your calendar. Everything exists in silos."],
            // Features Section
            ['feature_label', 'Features'],
            ['feature_title', 'Three Tools. One Platform. Zero Hassle.'],
            ['feature_desc', 'Everything you need to stay organized, productive, and financially aware.'],
            // Roadmap Section
            ['roadmap_label', 'Roadmap'],
            ['roadmap_title', 'Our Growth Journey'],
            ['roadmap_desc', 'From concept to a fully intelligent productivity platform — hover each milestone to explore the details.'],
            // Tutorial Section
            ['tutorial_label', 'Tutorial'],
            ['tutorial_title', 'See OptiPlan in Action'],
            ['tutorial_desc', 'Watch a quick walkthrough of how OptiPlan can transform your productivity.'],
            // FAQ Section
            ['faq_label', 'FAQ'],
            ['faq_title', 'Frequently Asked Questions'],
            ['faq1_question', 'What makes OptiPlan different from other productivity apps?'],
            ['faq1_answer', 'OptiPlan is the only platform that integrates scheduling, study support, and budgeting into one unified dashboard. While other apps focus on just one area, OptiPlan connects all aspects of student life with AI-powered insights.'],
            ['faq2_question', 'Is OptiPlan free to use?'],
            ['faq2_answer', 'Yes! OptiPlan offers a free tier with core features. Premium features like advanced AI insights and unlimited integrations are available with a student subscription.'],
            ['faq3_question', 'Can I sync OptiPlan with my university calendar?'],
            ['faq3_answer', 'Absolutely! OptiPlan supports calendar imports from most university systems and can automatically sync your class schedules, assignment deadlines, and exam dates.'],
            ['faq4_question', 'How does the AI-powered scheduling work?'],
            ['faq4_answer', 'Our AI learns from your behavior patterns and preferences to suggest optimal time slots for tasks, detect scheduling conflicts, and recommend breaks. The more you use OptiPlan, the smarter it becomes.'],
            ['faq5_question', 'Is my data secure?'],
            ['faq5_answer', 'Yes! We use industry-standard encryption to protect your data. Your information is private, never sold to third parties, and you can export or delete it at any time.'],
            // Feedback Form Section
            ['feedback_label', 'Contact Us'],
            ['feedback_title', 'We Value Your Feedback'],
            ['feedback_desc', 'Have a suggestion or found a bug? Let us know how we can improve OptiPlan for you.'],
            // Testimonials Section
            ['testimonial_label', 'Testimonials'],
            ['testimonial_title', 'What Students Are Saying'],
            ['testimonial1_text', 'OptiPlan changed how I manage my time. I went from constantly stressed to actually having free time. The budget tracker alone saved me hundreds this semester!'],
            ['testimonial1_name', 'Sarah Martinez'],
            ['testimonial1_role', 'Computer Science, Year 3'],
            ['testimonial1_initials', 'SM'],
            ['testimonial2_text', 'Finally, an app that gets student life. The AI scheduling is scary accurate at predicting when I need breaks. My grades improved and I\'m less stressed.'],
            ['testimonial2_name', 'James Chen'],
            ['testimonial2_role', 'Business Admin, Year 2'],
            ['testimonial2_initials', 'JC'],
            ['testimonial3_text', 'I used to juggle 5 different apps. Now everything is in one place. The study planner helped me ace my finals, and the interface is actually beautiful.'],
            ['testimonial3_name', 'Emma Park'],
            ['testimonial3_role', 'Psychology, Year 4'],
            ['testimonial3_initials', 'EP'],
            // About Creator Section
            ['about_creator_label', 'About the Creator'],
            ['about_creator_title', 'Built by a Student, For Students'],
            ['about_creator_p1', 'Hi! I am a student who had experienced firsthand the frustration of juggling multiple apps just to organize scheduling, studying and budeeting. After almost missing too many assignments and project deadlines, I realized I needed a better solution. That\'s when OptiPlan was born.'],
            ['about_creator_p2', 'OptiPlan is my final year project and a passion project aimed at making student\'s life more manageable. I hope it helps you as much as it\'s helpde me.'],
            // About OptiPlan Section
            ['about_optiplan_label', 'About OptiPlan'],
            ['about_optiplan_title', 'Built for Students Who Want More'],
            ['about_optiplan_stat_number', '3-in-1'],
            ['about_optiplan_stat_label', 'Integrated Platform'],
            ['about_optiplan_p1', "Managing student life shouldn't require a dozen different tools. OptiPlan unifies the essential pillars of your day—scheduling, study management, and financial health—into one streamlined interface."],
            ['about_optiplan_p2', 'Our technology is designed to be unobtrusive yet impactful, adapting to the nuances of your schedule to provide actionable insights. From deadline management to budget tracking, OptiPlan ensures your most important data is always in sync, allowing you to focus on what truly matters.'],
            // Footer
            ['footer_tagline', 'Your all-in-one AI-powered productivity dashboard for students and young professionals.'],
            ['footer_copyright', '© 2026 OptiPlan. All rights reserved.'],
        ];
        $insertStmt = $pdo->prepare("INSERT INTO site_content (section_key, content_value) VALUES (?, ?)");
        foreach ($defaults as $row) {
            $insertStmt->execute($row);
        }
    }

    // 12. Auto-create Admin if missing (password from env, not hardcoded)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'admin@optiplan.com'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $adminPass = password_hash(env('ADMIN_SEED_PASSWORD', 'change-me-immediately'), PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (first_name, last_name, email, password_hash, role, streak_count)
                VALUES ('System', 'Admin', 'admin@optiplan.com', ?, 'admin', 0)";
        $pdo->prepare($sql)->execute([$adminPass]);
    }

} catch (PDOException $e) {
    // Never expose raw DB errors to the client
    error_log('OptiPlan DB Error: ' . $e->getMessage());
    die(json_encode(['success' => false, 'message' => 'A database error occurred. Please try again later.']));
}
?>
