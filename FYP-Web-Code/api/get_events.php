<?php
require_once __DIR__ . '/../includes/security.php';
configure_secure_session();
session_start();
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// SECURITY: Require authentication (was missing — defaulted to user_id 1)
$user_id = require_auth();

// Rate limit: 60 requests per 15 minutes
enforce_rate_limit($pdo, 'get_events', 60, 900);

// Validate month/year as integers within valid ranges
$month = filter_var($_GET['month'] ?? date('m'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]]);
$year  = filter_var($_GET['year'] ?? date('Y'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 2000, 'max_range' => 2100]]);

if ($month === false || $year === false) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT title, due_date as date FROM tasks
        WHERE user_id = ?
        AND MONTH(due_date) = ?
        AND YEAR(due_date) = ?
        AND status = 'pending'";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $month, $year]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($events);
?>
