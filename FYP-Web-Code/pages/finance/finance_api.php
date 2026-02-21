<?php
require_once __DIR__ . '/../../includes/security.php';
configure_secure_session();
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

require_once __DIR__ . '/../../includes/db.php';

$user_id = (int) $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Rate limit: 60 finance API calls per 15 minutes
enforce_rate_limit($pdo, 'finance_api', 60, 900);

// CSRF enforcement for state-changing operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_enforce();
}

switch ($action) {

    // ─── GET ALL EXPENSES ───
    case 'get_expenses':
        $stmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id = ? ORDER BY expense_date DESC, created_at DESC");
        $stmt->execute([$user_id]);
        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'expenses' => $expenses]);
        break;

    // ─── ADD EXPENSE ───
    case 'add_expense':
        $input = filter_input_fields(['amount', 'category', 'description', 'date', 'action', 'csrf_token']);

        $amount = validate_amount($input['amount'] ?? 0);
        if ($amount === false || $amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Amount must be a positive number.']);
            break;
        }

        $category = validate_enum(
            $input['category'] ?? '',
            ['food','transport','shopping','entertainment','education','health','bills','other'],
            'other'
        );

        $description = validate_string($input['description'] ?? '', 0, 255);
        if ($description === false) $description = '';

        $date = validate_date($input['date'] ?? '');
        if ($date === false) $date = date('Y-m-d');

        $stmt = $pdo->prepare("INSERT INTO expenses (user_id, amount, category, description, expense_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $amount, $category, $description, $date]);

        $id = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Expense added']);
        break;

    // ─── DELETE EXPENSE ───
    case 'delete_expense':
        $id = validate_positive_int($_POST['id'] ?? 0);
        if ($id === false) {
            echo json_encode(['success' => false, 'message' => 'Invalid expense ID.']);
            break;
        }
        // Ownership check: only delete expenses belonging to this user
        $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        echo json_encode(['success' => true, 'message' => 'Expense deleted']);
        break;

    // ─── GET BUDGET ───
    case 'get_budget':
        $stmt = $pdo->prepare("SELECT * FROM budgets WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $budget = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$budget) {
            $budget = [
                'total_budget' => 0, 'food_budget' => 0, 'transport_budget' => 0,
                'shopping_budget' => 0, 'entertainment_budget' => 0, 'education_budget' => 0,
                'health_budget' => 0, 'bills_budget' => 0, 'other_budget' => 0
            ];
        }
        echo json_encode(['success' => true, 'budget' => $budget]);
        break;

    // ─── SAVE BUDGET ───
    case 'save_budget':
        $input = filter_input_fields(['total', 'food', 'transport', 'shopping', 'entertainment', 'education', 'health', 'bills', 'other', 'action', 'csrf_token']);

        // Validate all budget amounts
        $fields = ['total', 'food', 'transport', 'shopping', 'entertainment', 'education', 'health', 'bills', 'other'];
        $values = [];
        foreach ($fields as $f) {
            $val = validate_amount($input[$f] ?? 0);
            $values[$f] = ($val === false) ? 0.0 : $val;
        }

        $stmt = $pdo->prepare("INSERT INTO budgets (user_id, total_budget, food_budget, transport_budget, shopping_budget, entertainment_budget, education_budget, health_budget, bills_budget, other_budget)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                total_budget = VALUES(total_budget),
                food_budget = VALUES(food_budget),
                transport_budget = VALUES(transport_budget),
                shopping_budget = VALUES(shopping_budget),
                entertainment_budget = VALUES(entertainment_budget),
                education_budget = VALUES(education_budget),
                health_budget = VALUES(health_budget),
                bills_budget = VALUES(bills_budget),
                other_budget = VALUES(other_budget)");
        $stmt->execute([$user_id, $values['total'], $values['food'], $values['transport'], $values['shopping'], $values['entertainment'], $values['education'], $values['health'], $values['bills'], $values['other']]);

        echo json_encode(['success' => true, 'message' => 'Budget saved']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
