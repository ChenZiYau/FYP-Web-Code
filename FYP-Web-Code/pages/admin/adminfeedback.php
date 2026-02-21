<?php
require_once __DIR__ . '/../../includes/security.php';
configure_secure_session();
session_start();
require_once __DIR__ . '/../../includes/db.php';

// Secure: admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Rate limit: 30 admin feedback operations per 15 minutes
enforce_rate_limit($pdo, 'admin_feedback', 30, 900);

// Handle delete (with CSRF validation and type-safe ID)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    csrf_enforce();
    $deleteId = validate_positive_int($_POST['delete_id']);
    if ($deleteId !== false) {
        $stmt = $pdo->prepare("DELETE FROM feedback WHERE id = ?");
        $stmt->execute([$deleteId]);
    }
    header('Location: adminfeedback.php');
    exit;
}

// Filter
$filter = $_GET['category'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$sql = "SELECT * FROM feedback";
$params = [];
$conditions = [];

if ($filter !== 'all') {
    $conditions[] = "subject = ?";
    $params[] = $filter;
}

if ($search !== '') {
    $conditions[] = "(name LIKE ? OR email LIKE ? OR message LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($conditions) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats
$totalFeedback = $pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn();
$todayFeedback = $pdo->query("SELECT COUNT(*) FROM feedback WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$bugCount      = $pdo->query("SELECT COUNT(*) FROM feedback WHERE subject = 'bug'")->fetchColumn();
$featureCount  = $pdo->query("SELECT COUNT(*) FROM feedback WHERE subject = 'feature'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Management - OptiPlan Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="../landing/index.php" class="sidebar-logo">
                    <svg class="sidebar-logo-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>AdminPlan</span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <h3 class="nav-section-title">Main Menu</h3>
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="admin.php" class="nav-link-page">
                                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                <span>Overview</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="adminuserdb.php" class="nav-link-page">
                                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>User Database</span>
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="adminfeedback.php" class="nav-link-page">
                                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                <span>Feedback</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="sidebar-footer">
                <a href="../auth/logout.php" class="admin-profile">
                    <div class="admin-avatar">AD</div>
                    <div class="admin-info">
                        <div class="admin-name">Admin User</div>
                        <div class="admin-role">Administrator</div>
                    </div>
                    <svg class="logout-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-content">
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="header-title">Feedback Management</h1>
                    <div class="header-actions">
                        <button class="header-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="notification-dot"></span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Content Section -->
            <section class="content-section">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Total Feedback</span>
                            <div class="stat-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $totalFeedback; ?></div>
                        <div class="stat-change positive">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <span>Today: <?php echo $todayFeedback; ?></span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Bug Reports</span>
                            <div class="stat-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $bugCount; ?></div>
                        <div class="stat-change positive">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <span>Needs attention</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Feature Requests</span>
                            <div class="stat-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $featureCount; ?></div>
                        <div class="stat-change positive">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <span>From users</span>
                        </div>
                    </div>
                </div>

                <!-- Feedback Section -->
                <div class="data-section">
                    <div class="section-header">
                        <h2 class="section-title">All Feedback</h2>
                        <div class="section-actions">
                            <form method="GET" style="display:flex;gap:0.75rem;align-items:center;">
                                <select name="category" class="fb-category-filter" onchange="this.form.submit()">
                                    <option value="all" <?php echo $filter==='all'?'selected':''; ?>>All Categories</option>
                                    <option value="general" <?php echo $filter==='general'?'selected':''; ?>>General</option>
                                    <option value="bug" <?php echo $filter==='bug'?'selected':''; ?>>Bug Reports</option>
                                    <option value="feature" <?php echo $filter==='feature'?'selected':''; ?>>Feature Requests</option>
                                    <option value="support" <?php echo $filter==='support'?'selected':''; ?>>Help & Support</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <?php if (empty($feedbacks)): ?>
                            <div class="fb-empty">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p>No feedback found.</p>
                            </div>
                        <?php else: ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Category</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($feedbacks as $fb): ?>
                                    <tr>
                                        <td>
                                            <div class="user-cell">
                                                <div class="user-avatar"><?php echo strtoupper(substr($fb['name'], 0, 2)); ?></div>
                                                <div class="user-info">
                                                    <div class="user-name"><?php echo htmlspecialchars($fb['name']); ?></div>
                                                    <div class="user-email"><?php echo htmlspecialchars($fb['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $fb['subject'] === 'bug' ? 'offline' : 'online'; ?>">
                                                <span class="status-dot"></span>
                                                <?php echo ucfirst(htmlspecialchars($fb['subject'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($fb['created_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="action-btn" title="View" onclick="viewFeedback(<?php echo htmlspecialchars(json_encode($fb), ENT_QUOTES, 'UTF-8'); ?>)">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this feedback?');">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="delete_id" value="<?php echo (int)$fb['id']; ?>">
                                                    <button type="submit" class="action-btn" title="Delete">
                                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Feedback Detail Modal -->
    <div class="fb-modal-overlay" id="fbModal" onclick="closeFeedbackModal(event)">
        <div class="fb-modal">
            <div class="fb-modal-header">
                <h3 class="fb-modal-title">Feedback Details</h3>
                <button class="fb-modal-close" onclick="closeFeedbackModal()">&times;</button>
            </div>
            <div class="fb-modal-body">
                <div class="fb-modal-row">
                    <span class="fb-modal-label">Name</span>
                    <span class="fb-modal-value" id="modalName"></span>
                </div>
                <div class="fb-modal-row">
                    <span class="fb-modal-label">Email</span>
                    <span class="fb-modal-value" id="modalEmail"></span>
                </div>
                <div class="fb-modal-row">
                    <span class="fb-modal-label">Category</span>
                    <span class="fb-modal-value" id="modalCategory"></span>
                </div>
                <div class="fb-modal-row">
                    <span class="fb-modal-label">Date</span>
                    <span class="fb-modal-value" id="modalDate"></span>
                </div>
                <div class="fb-modal-row fb-modal-row--full">
                    <span class="fb-modal-label">Message</span>
                    <p class="fb-modal-message" id="modalMessage"></p>
                </div>
            </div>
        </div>
    </div>

    <script src="admin.js"></script>
    <script>
    function viewFeedback(fb) {
        document.getElementById('modalName').textContent = fb.name;
        document.getElementById('modalEmail').textContent = fb.email;
        document.getElementById('modalCategory').textContent = fb.subject.charAt(0).toUpperCase() + fb.subject.slice(1);
        document.getElementById('modalDate').textContent = new Date(fb.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
        document.getElementById('modalMessage').textContent = fb.message;
        document.getElementById('fbModal').classList.add('active');
    }

    function closeFeedbackModal(e) {
        if (e && e.target !== e.currentTarget) return;
        document.getElementById('fbModal').classList.remove('active');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeFeedbackModal();
    });
    </script>
</body>
</html>
