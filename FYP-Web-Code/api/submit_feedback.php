<?php
require_once __DIR__ . '/../includes/security.php';
configure_secure_session();
session_start();
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Require logged-in user
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to submit feedback.', 'require_login' => true]);
    exit;
}

// Rate limit: 5 feedback submissions per 15 minutes
enforce_rate_limit($pdo, 'feedback', 5, 900);

// CSRF validation
csrf_enforce();

// Get user info from session (don't trust client-supplied name/email)
$userId = (int) $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

// Only accept expected fields
$input = filter_input_fields(['category', 'message', 'csrf_token']);

$name    = trim($user['first_name'] . ' ' . $user['last_name']);
$email   = $user['email'];

// Validate category against allowed values
$subject = validate_enum($input['category'] ?? '', ['general', 'bug', 'feature', 'support'], 'general');

// Validate message
$message = validate_string($input['message'] ?? '', 1, 5000);
if ($message === false) {
    echo json_encode(['success' => false, 'message' => 'Please enter a message (max 5000 characters).']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO feedback (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);
    echo json_encode(['success' => true, 'message' => 'Thank you! Your feedback has been submitted.']);
} catch (PDOException $e) {
    error_log('Feedback error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again later.']);
}
