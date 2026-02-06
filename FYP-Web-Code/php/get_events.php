<?php
session_start();
require 'db.php'; // Ensure db.php is in the same folder

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 1;

// Get tasks that are PENDING and match the requested month/year
// Note: We use the 'due_date' for the calendar
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

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