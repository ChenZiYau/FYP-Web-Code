<?php
require_once 'db.php';
session_start();
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

// Get user info from session
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

$name    = trim($user['first_name'] . ' ' . $user['last_name']);
$email   = $user['email'];
$subject = trim($_POST['category'] ?? 'general');
$message = trim($_POST['message'] ?? '');

if ($message === '') {
    echo json_encode(['success' => false, 'message' => 'Please enter a message.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO feedback (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);
    echo json_encode(['success' => true, 'message' => 'Thank you! Your feedback has been submitted.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again later.']);
}
