<?php
require_once __DIR__ . '/../includes/security.php';
configure_secure_session();
session_start();
require_once __DIR__ . '/../includes/db.php';

// Turn off error printing so warnings don't break JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

// SECURITY: Require authentication (was missing — defaulted to user_id 1)
$user_id = require_auth();

// Rate limit: 30 task creations per 15 minutes
enforce_rate_limit($pdo, 'save_task', 30, 900);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    csrf_enforce();

    try {
        // Only accept expected fields
        $input = filter_input_fields(['title', 'description', 'date', 'priority', 'type', 'time_start', 'time_end', 'csrf_token']);

        // Strict input validation
        $title = validate_string($input['title'] ?? '', 1, 255);
        if ($title === false) {
            echo json_encode(['status' => 'error', 'message' => 'Title is required (max 255 characters).']);
            exit;
        }

        $description = validate_string($input['description'] ?? '', 0, 5000);
        if ($description === false) $description = '';

        // Validate type
        $type = validate_enum($input['type'] ?? '', ['task', 'event', 'note'], 'task');

        // Date is optional for notes
        $date = validate_date($input['date'] ?? '');
        if ($date === false && $type !== 'note') {
            echo json_encode(['status' => 'error', 'message' => 'A valid date (YYYY-MM-DD) is required.']);
            exit;
        }
        if ($date === false) $date = date('Y-m-d');

        // Validate priority against allowed ENUM values
        $priority = validate_enum($input['priority'] ?? '', ['low', 'medium', 'high'], 'medium');

        // Event time fields
        $time_start = null;
        $time_end = null;
        if ($type === 'event') {
            $ts = $input['time_start'] ?? '';
            $te = $input['time_end'] ?? '';
            if (preg_match('/^\d{2}:\d{2}$/', $ts)) $time_start = $ts . ':00';
            if (preg_match('/^\d{2}:\d{2}$/', $te)) $time_end = $te . ':00';
        }

        $sql = "INSERT INTO tasks (user_id, title, description, due_date, priority, type, event_time_start, event_time_end) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$user_id, $title, $description, $date, $priority, $type, $time_start, $time_end]);

        if ($result) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save task.']);
        }

    } catch (Exception $e) {
        // Never expose raw DB errors
        error_log('save_task error: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An error occurred. Please try again.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
