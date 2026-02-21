<?php
require_once 'db.php';
session_start();

header('Content-Type: application/json');

// Only admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Fetch all content or filter by section prefix
        $section = $_GET['section'] ?? '';
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
        $input = json_decode(file_get_contents('php://input'), true);

        // Batch update support
        if (isset($input['updates']) && is_array($input['updates'])) {
            $stmt = $pdo->prepare("UPDATE site_content SET content_value = ? WHERE section_key = ?");
            $updated = 0;
            foreach ($input['updates'] as $item) {
                if (isset($item['key']) && isset($item['value'])) {
                    $stmt->execute([trim($item['value']), trim($item['key'])]);
                    $updated += $stmt->rowCount();
                }
            }
            echo json_encode(['success' => true, 'updated' => $updated]);
            break;
        }

        // Single update
        if (!isset($input['key']) || !isset($input['value'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing key or value']);
            break;
        }

        $stmt = $pdo->prepare("UPDATE site_content SET content_value = ? WHERE section_key = ?");
        $stmt->execute([trim($input['value']), trim($input['key'])]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            // Key might not exist, try insert
            $stmt2 = $pdo->prepare("INSERT INTO site_content (section_key, content_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE content_value = VALUES(content_value)");
            $stmt2->execute([trim($input['key']), trim($input['value'])]);
            echo json_encode(['success' => true]);
        }
        break;

    case 'DELETE':
        // Reset all content to defaults by dropping and re-seeding
        $pdo->exec("DELETE FROM site_content");
        // Force re-seed on next page load by setting count to 0
        echo json_encode(['success' => true, 'message' => 'Content reset. Defaults will be restored on next page load.']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
