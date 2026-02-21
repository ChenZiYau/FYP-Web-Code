<?php
require_once __DIR__ . '/../includes/security.php';
configure_secure_session();
session_start();
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// SECURITY: Require authentication (was completely missing)
$user_id = require_auth();

// Rate limit: 60 updates per 15 minutes
enforce_rate_limit($pdo, 'update_task', 60, 900);

$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request body.']);
    exit;
}

// Validate CSRF from JSON body or header
$csrfToken = $data['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (empty($csrfToken) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

// Validate task ID
$taskId = validate_positive_int($data['id'] ?? null);
if ($taskId === false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid task ID.']);
    exit;
}

// Validate status against allowed ENUM values
$status = validate_enum($data['status'] ?? '', ['pending', 'completed']);
if ($status === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Status must be "pending" or "completed".']);
    exit;
}

// SECURITY: Only update tasks owned by the current user (ownership check)
$stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
$stmt->execute([$status, $taskId, $user_id]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Task not found or access denied.']);
}
?>
