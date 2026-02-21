<?php
require_once __DIR__ . '/../../includes/security.php';
configure_secure_session();
session_start();
require_once __DIR__ . '/../../includes/db.php';
header('Content-Type: application/json');

$userId = require_auth();

// Rate limit: 20 settings changes per 15 minutes
enforce_rate_limit($pdo, 'api_settings', 20, 900);

$action = $_GET['action'] ?? '';

// ── Update Username ──
if ($action === 'update_username' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_enforce();

    $input = filter_input_fields(['username', 'csrf_token']);
    $username = trim($input['username'] ?? '');

    if ($username === '' || !preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        echo json_encode(['success' => false, 'message' => 'Username must be 3-50 characters: letters, numbers, or underscores.']);
        exit;
    }

    // Check uniqueness
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$username, $userId]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username taken. Please choose another.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
    $stmt->execute([$username, $userId]);
    $_SESSION['username'] = $username;

    echo json_encode(['success' => true, 'message' => 'Username updated successfully.']);
    exit;
}

// ── Upload Profile Picture ──
if ($action === 'upload_pfp' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_enforce();

    if (!isset($_FILES['pfp']) || $_FILES['pfp']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
        exit;
    }

    $file = $_FILES['pfp'];

    // Validate MIME type using file contents (not user-supplied type)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    if (!isset($allowed[$mime])) {
        echo json_encode(['success' => false, 'message' => 'Only JPG and PNG files are allowed.']);
        exit;
    }

    // Validate size (2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File size must be under 2MB.']);
        exit;
    }

    // Create upload dir
    $uploadDir = '../../uploads/pfps/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Delete old PFP if exists
    $stmt = $pdo->prepare("SELECT pfp_path FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $oldPath = $stmt->fetchColumn();
    if ($oldPath && file_exists('../' . $oldPath)) {
        unlink('../' . $oldPath);
    }

    // Save new file with a safe, non-guessable filename
    $ext = $allowed[$mime];
    $filename = 'pfp_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $relativePath = 'uploads/pfps/' . $filename;
    $fullPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save file.']);
        exit;
    }

    // Update DB
    $stmt = $pdo->prepare("UPDATE users SET pfp_path = ? WHERE id = ?");
    $stmt->execute([$relativePath, $userId]);
    $_SESSION['pfp_path'] = $relativePath;

    echo json_encode(['success' => true, 'message' => 'Profile picture updated.', 'pfp_path' => $relativePath]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
