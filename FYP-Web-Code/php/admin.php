<?php
require_once 'db.php';
session_start();

// 1. Secure the page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

try {
    // 2. Fetch Real Stats
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
    // For now, we'll count everyone as "Online" if they have a session, 
    // but typically you'd check a 'last_login' timestamp.
    $onlineUsers = 1; 
    $totalFeedback = 0; // Set to 0 until you build a feedback table

    // 3. Fetch Real User List
    $stmt = $pdo->query("SELECT id, first_name, last_name, email, created_at FROM users ORDER BY created_at DESC");
    $db_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the database users to match your template's array structure
    $users = [];
    foreach ($db_users as $u) {
        $users[] = [
            'id' => $u['id'],
            'name' => $u['first_name'] . ' ' . $u['last_name'],
            'email' => $u['email'],
            'status' => 'online', // Default for now
            'joined' => $u['created_at']
        ];
    }

    // Filter for online users (demo logic)
    $onlineUsersList = $users; 

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Keep your sample feedback array for now so the UI doesn't look empty
$feedbacks = [
    ['id' => 1, 'user' => 'System', 'avatar' => 'S', 'rating' => 5, 'text' => 'Database connected successfully!', 'date' => 'Just now']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiPlan Dashboard - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <a href="index.php" class="sidebar-logo">
                    <svg class="sidebar-logo-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>AdminPlan</span>
                </a>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <h3 class="nav-section-title">Main Menu</h3>
                    <ul class="nav-menu">
                        <li class="nav-item active" data-section="user-database">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>User Database</span>
                        </li>
                        <li class="nav-item" data-section="user-online">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
                            </svg>
                            <span>User Online</span>
                        </li>
                        <li class="nav-item" data-section="feedback">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            <span>Feedback</span>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <a href="logout.php" class="admin-profile" onclick="handleLogout(event)">
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
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-content">
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="header-title" id="pageTitle">User Database</h1>
                    <div class="header-actions">
                        <div class="search-bar">
                            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" class="search-input" placeholder="Search..." id="searchInput">
                        </div>
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
                            <span class="stat-label">Total Users</span>
                            <div class="stat-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo number_format($totalUsers); ?></div>
                        <div class="stat-change positive">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <span>12% from last month</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <span class="stat-label">Users Online</span>
                            <div class="stat-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $onlineUsers; ?></div>
                        <div class="stat-change positive">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <span>8% from yesterday</span>
                        </div>
                    </div>

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
                            <span>24% from last week</span>
                        </div>
                    </div>
                </div>

                <!-- User Database Section -->
                <div class="data-section" id="section-user-database">
                    <div class="section-header">
                        <h2 class="section-title">All Users</h2>
                        <div class="section-actions">
                            <button class="btn btn-secondary" onclick="exportData()">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Export
                            </button>
                            <button class="btn btn-primary" onclick="addNewUser()">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add User
                            </button>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Joined Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 2)); ?></div>
                                            <div class="user-info">
                                                <div class="user-name"><?php echo htmlspecialchars($user['name']); ?></div>
                                                <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $user['status']; ?>">
                                            <span class="status-dot"></span>
                                            <?php echo ucfirst($user['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['joined'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn" onclick="viewUser(<?php echo $user['id']; ?>)" title="View">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button class="action-btn" onclick="editUser(<?php echo $user['id']; ?>)" title="Edit">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button class="action-btn" onclick="deleteUser(<?php echo $user['id']; ?>)" title="Delete">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- User Online Section -->
                <div class="data-section hidden" id="section-user-online">
                    <div class="section-header">
                        <h2 class="section-title">Users Currently Online</h2>
                        <div class="section-actions">
                            <button class="btn btn-secondary" onclick="refreshOnlineUsers()">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Last Active</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($onlineUsersList as $user): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 2)); ?></div>
                                            <div class="user-info">
                                                <div class="user-name"><?php echo htmlspecialchars($user['name']); ?></div>
                                                <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge online">
                                            <span class="status-dot"></span>
                                            Online
                                        </span>
                                    </td>
                                    <td>Active now</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn" onclick="viewUser(<?php echo $user['id']; ?>)" title="View">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button class="action-btn" onclick="sendMessage(<?php echo $user['id']; ?>)" title="Message">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Feedback Section -->
                <div class="data-section hidden" id="section-feedback">
                    <div class="section-header">
                        <h2 class="section-title">User Feedback</h2>
                        <div class="section-actions">
                            <button class="btn btn-secondary" onclick="filterFeedback()">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filter
                            </button>
                        </div>
                    </div>

                    <div class="feedback-grid">
                        <?php foreach ($feedbacks as $feedback): ?>
                        <div class="feedback-card">
                            <div class="feedback-header">
                                <div class="feedback-user">
                                    <div class="user-avatar"><?php echo htmlspecialchars($feedback['avatar']); ?></div>
                                    <div class="user-info">
                                        <div class="user-name"><?php echo htmlspecialchars($feedback['user']); ?></div>
                                    </div>
                                </div>
                                <div class="feedback-rating">
                                    <?php echo str_repeat('⭐', $feedback['rating']); ?>
                                </div>
                            </div>
                            <p class="feedback-text"><?php echo htmlspecialchars($feedback['text']); ?></p>
                            <div class="feedback-footer">
                                <span class="feedback-date"><?php echo htmlspecialchars($feedback['date']); ?></span>
                                <div class="feedback-actions">
                                    <button class="action-btn" onclick="replyFeedback(<?php echo $feedback['id']; ?>)" title="Reply">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                        </svg>
                                    </button>
                                    <button class="action-btn" onclick="deleteFeedback(<?php echo $feedback['id']; ?>)" title="Delete">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="../JavaScript/admin.js"></script>
</body>
</html>