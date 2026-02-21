<?php
/**
 * env_loader.php — Loads .env file into getenv() / $_ENV.
 *
 * Reads key=value pairs from the .env file in the project root.
 * Skips comments (#) and blank lines.
 */
function load_env(string $path = null): void {
    static $loaded = false;
    if ($loaded) return;

    $path = $path ?? dirname(__DIR__) . '/.env';

    if (!file_exists($path)) {
        // Fail gracefully — fall back to system environment or defaults
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        // Skip comments
        if ($line === '' || $line[0] === '#') continue;

        // Parse KEY=VALUE (supports quoted values)
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove surrounding quotes if present
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }

            // Only set if not already defined (system env takes priority)
            if (!getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }

    $loaded = true;
}

/**
 * Get an environment variable with a default fallback.
 */
function env(string $key, string $default = ''): string {
    return getenv($key) ?: ($_ENV[$key] ?? $default);
}
