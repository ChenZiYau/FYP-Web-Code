<?php
/**
 * security.php — Central security module for OptiPlan.
 *
 * Provides:
 *  - Secure session configuration
 *  - IP + user-based rate limiting (stored in DB)
 *  - CSRF token generation and validation
 *  - Input validation & sanitization helpers
 *  - Helpers for requiring authentication
 */

// ─── Secure Session Config ───────────────────────────────────────────
// Must be called BEFORE session_start() in every file that includes this.
function configure_secure_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    // Set Secure flag only when running over HTTPS
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? '1' : '0');
}

// ─── Rate Limiting (DB-backed) ───────────────────────────────────────
// Creates the rate_limits table on first use, then checks/increments.

/**
 * Ensure the rate_limits table exists.
 */
function ensure_rate_limit_table(PDO $pdo): void {
    static $created = false;
    if ($created) return;
    $pdo->exec("CREATE TABLE IF NOT EXISTS rate_limits (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        identifier  VARCHAR(255) NOT NULL,
        endpoint    VARCHAR(100) NOT NULL,
        hits        INT NOT NULL DEFAULT 1,
        window_start DATETIME NOT NULL,
        INDEX idx_rl_lookup (identifier, endpoint, window_start)
    )");
    $created = true;
}

/**
 * Check rate limit for the given identifier and endpoint.
 *
 * @param PDO    $pdo
 * @param string $identifier  IP address or "user:<id>"
 * @param string $endpoint    A label like "login", "signup", "feedback"
 * @param int    $maxHits     Maximum requests allowed in the window
 * @param int    $windowSec   Window size in seconds (default 900 = 15 min)
 * @return bool  true if within limits, false if exceeded
 */
function check_rate_limit(PDO $pdo, string $identifier, string $endpoint, int $maxHits, int $windowSec = 900): bool {
    ensure_rate_limit_table($pdo);

    $windowStart = date('Y-m-d H:i:s', time() - $windowSec);

    // Clean old entries periodically (1% chance per request to avoid overhead)
    if (mt_rand(1, 100) === 1) {
        $pdo->prepare("DELETE FROM rate_limits WHERE window_start < ?")->execute([$windowStart]);
    }

    // Count hits in the current window
    $stmt = $pdo->prepare(
        "SELECT COALESCE(SUM(hits), 0) FROM rate_limits
         WHERE identifier = ? AND endpoint = ? AND window_start >= ?"
    );
    $stmt->execute([$identifier, $endpoint, $windowStart]);
    $totalHits = (int) $stmt->fetchColumn();

    if ($totalHits >= $maxHits) {
        return false; // Rate limit exceeded
    }

    // Record this hit
    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare(
        "INSERT INTO rate_limits (identifier, endpoint, hits, window_start)
         VALUES (?, ?, 1, ?)
         ON DUPLICATE KEY UPDATE hits = hits + 1"
    );
    $stmt->execute([$identifier, $endpoint, $now]);

    return true;
}

/**
 * Send a 429 Too Many Requests response and exit.
 */
function rate_limit_exceeded(string $message = 'Too many requests. Please try again later.'): void {
    http_response_code(429);
    header('Retry-After: 900');
    if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') ||
        str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') ||
        !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $message]);
    } else {
        echo '<h1>429 Too Many Requests</h1><p>' . htmlspecialchars($message) . '</p>';
    }
    exit;
}

/**
 * Convenience: enforce rate limit or 429.
 */
function enforce_rate_limit(PDO $pdo, string $endpoint, int $maxHits, int $windowSec = 900): void {
    $ip = get_client_ip();
    if (!check_rate_limit($pdo, $ip, $endpoint, $maxHits, $windowSec)) {
        rate_limit_exceeded();
    }
    // Also rate-limit per user if logged in
    if (isset($_SESSION['user_id'])) {
        if (!check_rate_limit($pdo, 'user:' . $_SESSION['user_id'], $endpoint, $maxHits, $windowSec)) {
            rate_limit_exceeded();
        }
    }
}

/**
 * Get the client's IP address, respecting common proxy headers.
 */
function get_client_ip(): string {
    // Only trust X-Forwarded-For if behind a known reverse proxy.
    // For XAMPP local dev, REMOTE_ADDR is sufficient.
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

// ─── CSRF Protection ─────────────────────────────────────────────────

/**
 * Generate a CSRF token and store it in the session.
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Return an HTML hidden input with the CSRF token.
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Validate the CSRF token from the request.
 * Checks POST body and X-CSRF-Token header.
 *
 * @return bool true if valid
 */
function csrf_validate(): bool {
    $token = $_POST['csrf_token']
        ?? $_SERVER['HTTP_X_CSRF_TOKEN']
        ?? '';

    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Enforce CSRF or return 403.
 */
function csrf_enforce(): void {
    if (!csrf_validate()) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid or missing CSRF token. Please refresh the page and try again.']);
        exit;
    }
}

// ─── Input Validation Helpers ────────────────────────────────────────

/**
 * Validate and sanitize an email address.
 * Returns the cleaned email or false.
 */
function validate_email(string $email): string|false {
    $email = trim($email);
    if (strlen($email) > 100) return false;
    return filter_var($email, FILTER_VALIDATE_EMAIL) ?: false;
}

/**
 * Validate a string field: trimmed, within length limits, no null bytes.
 *
 * @return string|false  Cleaned string or false on failure
 */
function validate_string(string $value, int $minLen = 1, int $maxLen = 255): string|false {
    $value = trim($value);
    // Reject null bytes (binary injection)
    if (str_contains($value, "\0")) return false;
    $len = mb_strlen($value, 'UTF-8');
    if ($len < $minLen || $len > $maxLen) return false;
    return $value;
}

/**
 * Validate that a value is in an allowed list.
 */
function validate_enum(string $value, array $allowed, string $default = ''): string {
    return in_array($value, $allowed, true) ? $value : $default;
}

/**
 * Validate a date string (Y-m-d format).
 */
function validate_date(string $date): string|false {
    $date = trim($date);
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) return false;
    $parts = explode('-', $date);
    if (!checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0])) return false;
    return $date;
}

/**
 * Validate a positive integer.
 */
function validate_positive_int(mixed $value): int|false {
    $val = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $val !== false ? $val : false;
}

/**
 * Validate a non-negative float (for monetary amounts).
 */
function validate_amount(mixed $value): float|false {
    $val = filter_var($value, FILTER_VALIDATE_FLOAT);
    if ($val === false || $val < 0) return false;
    // Cap at a reasonable maximum (10 million)
    if ($val > 10000000) return false;
    return round($val, 2);
}

/**
 * Validate a password meets minimum requirements.
 * Returns true if valid, or an error message string.
 */
function validate_password(string $password): true|string {
    if (strlen($password) < 8) return 'Password must be at least 8 characters.';
    if (strlen($password) > 128) return 'Password must be under 128 characters.';
    return true;
}

// ─── Authentication Helpers ──────────────────────────────────────────

/**
 * Require that the user is logged in. Returns user_id.
 * Sends 401 JSON and exits if not authenticated.
 */
function require_auth(): int {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Authentication required.']);
        exit;
    }
    return (int) $_SESSION['user_id'];
}

/**
 * Require admin role. Returns user_id.
 * Sends 403 JSON and exits if not admin.
 */
function require_admin(): int {
    $userId = require_auth();
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Admin access required.']);
        exit;
    }
    return $userId;
}

/**
 * Strip unexpected fields from POST data. Only keeps whitelisted keys.
 *
 * @param array $allowed  List of allowed field names
 * @param array $source   Input array (defaults to $_POST)
 * @return array  Filtered input
 */
function filter_input_fields(array $allowed, array $source = null): array {
    $source = $source ?? $_POST;
    return array_intersect_key($source, array_flip($allowed));
}
