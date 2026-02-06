<?php
session_start();
require 'db.php'; 

// 1. Turn off error printing so warnings don't break the JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 2. Validate inputs exist using '??' (Null Coalescing Operator)
        // This prevents "Undefined array key" warnings
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $date = $_POST['date'] ?? '';
        $priority = $_POST['priority'] ?? 'medium'; // Default to medium if missing

        // 3. Simple Validation: Don't allow empty title or date
        if (empty($title) || empty($date)) {
            echo json_encode(['status' => 'error', 'message' => 'Title and Date are required']);
            exit;
        }

        $sql = "INSERT INTO tasks (user_id, title, description, due_date, priority) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        // 4. Execute
        $result = $stmt->execute([$user_id, $title, $description, $date, $priority]);

        if ($result) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert into database']);
        }

    } catch (Exception $e) {
        // 5. Catch DB errors securely
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    // 6. Handle case where someone tries to visit the page directly (GET request)
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>