<?php
require_once __DIR__ . '/../../includes/security.php';
configure_secure_session();
session_start();
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

// Only admins can access
require_admin();

// Rate limit: 30 CMS operations per 15 minutes
enforce_rate_limit($pdo, 'api_content', 30, 900);

// Allowed section key prefixes (reject arbitrary keys)
$allowedKeyPrefixes = [
    'hero_', 'problem_', 'feature_', 'roadmap_', 'tutorial_', 'faq',
    'feedback_', 'testimonial', 'about_', 'footer_'
];

/**
 * Validate that a section_key matches an allowed prefix.
 */
function is_valid_section_key(string $key, array $prefixes): bool {
    $key = trim($key);
    if (strlen($key) === 0 || strlen($key) > 100) return false;
    // Only allow alphanumeric + underscores
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $key)) return false;
    foreach ($prefixes as $prefix) {
        if (str_starts_with($key, $prefix)) return true;
    }
    return false;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $section = trim($_GET['section'] ?? '');
        // Validate section filter
        if ($section !== '' && !preg_match('/^[a-zA-Z0-9_]+$/', $section)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid section filter.']);
            break;
        }
        if ($section) {
            $stmt = $pdo->prepare("SELECT section_key, content_value, updated_at FROM site_content WHERE section_key LIKE ? ORDER BY id");
            $stmt->execute([$section . '%']);
        } else {
            $stmt = $pdo->query("SELECT section_key, content_value, updated_at FROM site_content ORDER BY id");
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $rows]);
        break;

    case 'POST':
        // CSRF enforcement for POST
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request body.']);
            break;
        }

        // Validate CSRF from JSON body or header
        $csrfToken = $input['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (empty($csrfToken) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
            break;
        }

        // Batch update support
        if (isset($input['updates']) && is_array($input['updates'])) {
            $stmt = $pdo->prepare("UPDATE site_content SET content_value = ? WHERE section_key = ?");
            $updated = 0;
            foreach ($input['updates'] as $item) {
                if (!isset($item['key']) || !isset($item['value'])) continue;
                $key = trim($item['key']);
                $value = validate_string($item['value'] ?? '', 0, 10000);
                if ($value === false || !is_valid_section_key($key, $allowedKeyPrefixes)) continue;
                $stmt->execute([$value, $key]);
                $updated += $stmt->rowCount();
            }
            echo json_encode(['success' => true, 'updated' => $updated]);
            break;
        }

        // Single update
        if (!isset($input['key']) || !isset($input['value'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing key or value.']);
            break;
        }

        $key = trim($input['key']);
        $value = validate_string($input['value'] ?? '', 0, 10000);

        if (!is_valid_section_key($key, $allowedKeyPrefixes)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid section key.']);
            break;
        }
        if ($value === false) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Value too long (max 10000 characters).']);
            break;
        }

        // Only UPDATE existing keys — do not allow inserting arbitrary new keys
        $stmt = $pdo->prepare("UPDATE site_content SET content_value = ? WHERE section_key = ?");
        $stmt->execute([$value, $key]);

        echo json_encode(['success' => true, 'updated' => $stmt->rowCount()]);
        break;

    case 'DELETE':
        // CSRF enforcement via header for DELETE
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (empty($csrfToken) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
            break;
        }
        // Reset all content to defaults by clearing (re-seeded on next page load)
        $pdo->exec("DELETE FROM site_content");
        echo json_encode(['success' => true, 'message' => 'Content reset. Defaults will be restored on next page load.']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
