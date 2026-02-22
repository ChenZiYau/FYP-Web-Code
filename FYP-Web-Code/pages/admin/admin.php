<?php
require_once __DIR__ . '/../../includes/security.php';
configure_secure_session();
session_start();
require_once __DIR__ . '/../../includes/db.php';

// 1. Secure the page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

try {
    // 2. Fetch Real Stats
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
    // For now, we'll count everyone as "Online" if they have a session, 
    // but typically you'd check a 'last_login' timestamp.
    $onlineUsers = 1; 
    $totalFeedback = $pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn();

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

    // Recent activity: latest signups + feedback
    $recentActivity = [];

    $recentUsers = $pdo->query("SELECT first_name, last_name, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($recentUsers as $ru) {
        $recentActivity[] = [
            'avatar' => strtoupper(substr($ru['first_name'],0,1) . substr($ru['last_name'],0,1)),
            'text' => htmlspecialchars($ru['first_name'] . ' ' . $ru['last_name']) . ' joined OptiPlan',
            'time' => $ru['created_at'],
            'type' => 'user'
        ];
    }

    $recentFb = $pdo->query("SELECT name, subject, created_at FROM feedback ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($recentFb as $rf) {
        $recentActivity[] = [
            'avatar' => strtoupper(substr($rf['name'],0,2)),
            'text' => htmlspecialchars($rf['name']) . ' submitted ' . htmlspecialchars($rf['subject']) . ' feedback',
            'time' => $rf['created_at'],
            'type' => 'feedback'
        ];
    }

    // Content edit logs
    $recentEdits = $pdo->query("SELECT admin_name, section_key, action, created_at FROM content_edit_log ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($recentEdits as $edit) {
        $label = str_replace('_', ' ', $edit['section_key']);
        if ($edit['action'] === 'save_defaults') {
            $desc = 'saved all content as defaults';
        } elseif ($edit['action'] === 'reset_to_defaults') {
            $desc = 'reset content to saved defaults';
        } elseif ($edit['action'] === 'reset_to_original') {
            $desc = 'reset content to original defaults';
        } else {
            $desc = 'edited "' . htmlspecialchars($label) . '"';
        }
        $recentActivity[] = [
            'avatar' => strtoupper(substr($edit['admin_name'], 0, 2)),
            'text' => htmlspecialchars($edit['admin_name']) . ' ' . $desc,
            'time' => $edit['created_at'],
            'type' => 'content'
        ];
    }

    // Sort by time descending
    usort($recentActivity, function($a, $b) {
        return strtotime($b['time']) - strtotime($a['time']);
    });
    $recentActivity = array_slice($recentActivity, 0, 8);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiPlan Dashboard - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <a href="../landing/index.php" class="sidebar-logo">
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
                        <li class="nav-item active" data-section="overview">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>Overview</span>
                        </li>
                        <li class="nav-item">
                            <a href="adminuserdb.php" class="nav-link-page">
                                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>User Database</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="adminfeedback.php" class="nav-link-page">
                                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                <span>Feedback</span>
                            </a>
                        </li>
                        <li class="nav-item" data-section="website-content">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Website Content</span>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <a href="../auth/logout.php" class="admin-profile" onclick="handleLogout(event)">
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

                <!-- ========== SYSTEM OVERVIEW ========== -->
                <div class="data-section" id="section-overview">
                    <!-- Quick Stats -->
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
                                <span>Registered users</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <span class="stat-label">
                                    Active Sessions
                                    <span class="pulse-dot"></span>
                                </span>
                                <div class="stat-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="stat-value">
                                <?php echo $onlineUsers; ?>
                                <span class="stat-live-tag">LIVE</span>
                            </div>
                            <div class="stat-change positive">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                <span>Currently online</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <span class="stat-label">Support Tickets</span>
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
                                <span>Total submissions</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity Feed -->
                    <div class="section-header" style="margin-top:1.5rem;">
                        <h2 class="section-title">Recent Activity</h2>
                    </div>
                    <div class="activity-feed">
                        <?php if (empty($recentActivity)): ?>
                            <div class="activity-empty">No recent activity yet.</div>
                        <?php else: ?>
                            <?php foreach ($recentActivity as $act): ?>
                            <div class="activity-item">
                                <div class="activity-avatar <?php echo $act['type']; ?>"><?php echo $act['avatar']; ?></div>
                                <div class="activity-body">
                                    <p class="activity-text"><?php echo $act['text']; ?></p>
                                    <span class="activity-time"><?php echo date('M j, g:ia', strtotime($act['time'])); ?></span>
                                </div>
                                <div class="activity-type-icon">
                                    <?php if ($act['type'] === 'user'): ?>
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                    <?php elseif ($act['type'] === 'content'): ?>
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <?php else: ?>
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ========== WEBSITE CONTENT CMS ========== -->
                <div class="data-section hidden" id="section-website-content">
                    <div class="section-header">
                        <h2 class="section-title">Website Content Editor</h2>
                        <div class="section-actions">
                            <input type="text" class="cms-search-input" id="cmsSearchInput" placeholder="Search content fields...">
                            <button class="btn btn-primary" onclick="saveAsDefaults()">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Save as Defaults
                            </button>
                            <button class="btn btn-secondary" onclick="resetAllContent()">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset to Defaults
                            </button>
                        </div>
                    </div>

                    <div class="cms-flash" id="cmsFlash" style="display:none;"></div>

                    <div class="cms-sections" id="cmsSections">
                        <!-- Populated by JavaScript -->
                        <div class="cms-loading">Loading content...</div>
                    </div>
                </div>

            </section>
        </main>
    </div>

    <script>window.CSRF_TOKEN = <?php echo json_encode(csrf_token()); ?>;</script>
    <script src="admin.js"></script>
</body>
</html>