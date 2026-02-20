<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

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
        $amount      = floatval($_POST['amount'] ?? 0);
        $category    = $_POST['category'] ?? 'other';
        $description = trim($_POST['description'] ?? '');
        $date        = $_POST['date'] ?? date('Y-m-d');

        $allowed = ['food','transport','shopping','entertainment','education','health','bills','other'];
        if (!in_array($category, $allowed)) $category = 'other';
        if ($amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
            break;
        }

        $stmt = $pdo->prepare("INSERT INTO expenses (user_id, amount, category, description, expense_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $amount, $category, $description, $date]);

        $id = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Expense added']);
        break;

    // ─── DELETE EXPENSE ───
    case 'delete_expense':
        $id = intval($_POST['id'] ?? 0);
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
        $total         = floatval($_POST['total'] ?? 0);
        $food          = floatval($_POST['food'] ?? 0);
        $transport     = floatval($_POST['transport'] ?? 0);
        $shopping      = floatval($_POST['shopping'] ?? 0);
        $entertainment = floatval($_POST['entertainment'] ?? 0);
        $education     = floatval($_POST['education'] ?? 0);
        $health        = floatval($_POST['health'] ?? 0);
        $bills         = floatval($_POST['bills'] ?? 0);
        $other         = floatval($_POST['other'] ?? 0);

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
        $stmt->execute([$user_id, $total, $food, $transport, $shopping, $entertainment, $education, $health, $bills, $other]);

        echo json_encode(['success' => true, 'message' => 'Budget saved']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
