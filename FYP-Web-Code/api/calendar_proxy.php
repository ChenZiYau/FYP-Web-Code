<?php
/**
 * calendar_proxy.php — Server-side proxy for Google Calendar API.
 *
 * Keeps the API key on the server so it is never exposed to the client.
 * The frontend calls this endpoint instead of googleapis.com directly.
 */
require_once __DIR__ . '/../includes/security.php';
configure_secure_session();
session_start();
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// Require authentication
require_auth();

// Rate limit: 30 calendar proxy requests per 15 minutes
enforce_rate_limit($pdo, 'calendar_proxy', 30, 900);

// Validate parameters
$timeMin = $_GET['timeMin'] ?? '';
$timeMax = $_GET['timeMax'] ?? '';

// Basic ISO 8601 date validation
if (!preg_match('/^\d{4}-\d{2}-\d{2}T/', $timeMin) || !preg_match('/^\d{4}-\d{2}-\d{2}T/', $timeMax)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid timeMin or timeMax parameter.']);
    exit;
}

$apiKey = env('GOOGLE_API_KEY', '');
$calendarId = env('GOOGLE_CALENDAR_ID', '');

if ($apiKey === '' || $calendarId === '') {
    http_response_code(500);
    echo json_encode(['error' => 'Google Calendar not configured.']);
    exit;
}

$url = 'https://www.googleapis.com/calendar/v3/calendars/'
    . urlencode($calendarId)
    . '/events?'
    . http_build_query([
        'key' => $apiKey,
        'timeMin' => $timeMin,
        'timeMax' => $timeMax,
        'singleEvents' => 'true',
    ]);

// Use cURL to fetch from Google
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_SSL_VERIFYPEER => true,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    error_log('Calendar proxy cURL error: ' . $curlError);
    http_response_code(502);
    echo json_encode(['error' => 'Failed to reach Google Calendar.']);
    exit;
}

http_response_code($httpCode);
echo $response;
