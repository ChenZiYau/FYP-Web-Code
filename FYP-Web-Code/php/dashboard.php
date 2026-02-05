<?php
// --- TOP OF FILE: Simple Session Check ---
session_start();

// Use session data or defaults
$user_name = $_SESSION['name'] ?? 'User';
$user_level = 19;
$user_points = 1250;
$next_level_points = 1500;
$progress_percentage = round(($user_points / $next_level_points) * 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - OptiPlan</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body class="dashboard-body">
    
    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-logo">
                <svg class="logo-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="sidebar-logo-text">OptiPlan</span>
            </a>
            <button class="sidebar-toggle" id="sidebarToggle">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
        
    <nav class="sidebar-nav">
        <button class="nav-item create-btn" id="createBtn">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span class="nav-text">Create</span>
        </button>
        
        <a href="index.php" class="nav-item active">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="nav-text">Dashboard</span>
        </a>
        
        <a href="#tasks" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span class="nav-text">Tasks</span>
        </a>
        
        <a href="#schedules" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="nav-text">Schedules</span>
        </a>
        
        <a href="finance.php" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="nav-text">Finance</span>
        </a>
        
        <a href="#collab" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="nav-text">Collab</span>
        </a>
        
        <a href="#chatbot" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            <span class="nav-text">ChatBot</span>
        </a>
    </nav>
        
        <div class="sidebar-footer">
            <a href="logout.php" class="nav-item logout-btn">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="nav-text">Logout</span>
            </a>
        </div>
    </aside>
    
    <!-- Main Content Area -->
    <main class="main-content">
        
        <!-- Top Header Bar -->
        <header class="dashboard-header">
            <div class="header-left">
                <button class="mobile-menu-toggle" id="mobileSidebarToggle">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="welcome-section">
                    <h1 class="welcome-text">Welcome, <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span> !</h1>
                </div>
            </div>
            
            <div class="header-right">
                <div class="level-badge">
                    <span class="level-text">Lvl</span>
                    <div class="level-circle">
                        <span class="level-number"><?php echo $user_level; ?></span>
                        <div class="notification-dot"></div>
                    </div>
                </div>
                
                <button class="settings-btn" id="settingsBtn">
                    <svg class="settings-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="settings-text">Settings</span>
                    <div class="notification-dot"></div>
                </button>
            </div>
        </header>
        
        <!-- Dashboard Content Grid -->
        <div class="dashboard-grid">
            
            <!-- Calendar Section -->
            <section class="calendar-section">
                <div class="calendar-card">
                    <div class="calendar-header">
                        <button class="calendar-nav-btn" id="prevMonth">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <h2 class="calendar-month" id="calendarMonth"></h2>
                        <button class="calendar-nav-btn" id="nextMonth">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="calendar-grid" id="calendarGrid">
                        <!-- Calendar will be generated by JavaScript -->
                    </div>
                </div>
            </section>
            
            <!-- Quick Stats Cards -->
            <section class="stats-section">
                <div class="stat-card">
                    <div class="stat-icon tasks-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">12</div>
                        <div class="stat-label">Active Tasks</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon schedule-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">5</div>
                        <div class="stat-label">Today's Events</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon streak-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">7</div>
                        <div class="stat-label">Day Streak</div>
                    </div>
                </div>
            </section>
            
            <!-- Upcoming Tasks/Events -->
            <section class="upcoming-section">
                <div class="section-header">
                    <h2 class="section-title">Upcoming Tasks</h2>
                    <a href="#tasks" class="view-all-link">View All →</a>
                </div>
                
                <div class="tasks-list">
                    <div class="task-item priority-high">
                        <div class="task-checkbox">
                            <input type="checkbox" id="task1">
                            <label for="task1"></label>
                        </div>
                        <div class="task-info">
                            <div class="task-title">Complete Math Assignment</div>
                            <div class="task-meta">
                                <span class="task-date">Due: Nov 20</span>
                                <span class="task-priority">High Priority</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="task-item priority-medium">
                        <div class="task-checkbox">
                            <input type="checkbox" id="task2">
                            <label for="task2"></label>
                        </div>
                        <div class="task-info">
                            <div class="task-title">Study for Chemistry Quiz</div>
                            <div class="task-meta">
                                <span class="task-date">Due: Nov 21</span>
                                <span class="task-priority">Medium</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="task-item priority-low">
                        <div class="task-checkbox">
                            <input type="checkbox" id="task3">
                            <label for="task3"></label>
                        </div>
                        <div class="task-info">
                            <div class="task-title">Read Chapter 5 - History</div>
                            <div class="task-meta">
                                <span class="task-date">Due: Nov 22</span>
                                <span class="task-priority">Low</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="task-item priority-medium">
                        <div class="task-checkbox">
                            <input type="checkbox" id="task4">
                            <label for="task4"></label>
                        </div>
                        <div class="task-info">
                            <div class="task-title">Group Project Meeting</div>
                            <div class="task-meta">
                                <span class="task-date">Due: Nov 23</span>
                                <span class="task-priority">Medium</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Progress Section -->
            <section class="progress-section">
                <div class="section-header">
                    <h2 class="section-title">Your Progress</h2>
                </div>
                
                <div class="progress-card">
                    <div class="progress-header">
                        <span class="progress-label">Level Progress</span>
                        <span class="progress-percentage"><?php echo $progress_percentage; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $progress_percentage; ?>%"></div>
                    </div>
                    <div class="progress-footer">
                        <span class="progress-current"><?php echo $user_points; ?> XP</span>
                        <span class="progress-target"><?php echo $next_level_points; ?> XP to Level <?php echo $user_level + 1; ?></span>
                    </div>
                </div>
                
                <div class="achievements-preview">
                    <h3 class="achievements-title">Recent Achievements</h3>
                    <div class="achievements-list">
                        <div class="achievement-badge">
                            <div class="achievement-icon">🏆</div>
                            <span class="achievement-name">Task Master</span>
                        </div>
                        <div class="achievement-badge">
                            <div class="achievement-icon">🔥</div>
                            <span class="achievement-name">7 Day Streak</span>
                        </div>
                        <div class="achievement-badge">
                            <div class="achievement-icon">⭐</div>
                            <span class="achievement-name">Early Bird</span>
                        </div>
                    </div>
                </div>
            </section>
            
        </div>
        
    </main>
    
    <!-- Create Task/Event Modal -->
    <div class="modal" id="createModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Create New</h2>
                <button class="modal-close" id="closeModal">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="modal-tabs">
                <button class="modal-tab active" data-tab="task">Task</button>
                <button class="modal-tab" data-tab="event">Event</button>
                <button class="modal-tab" data-tab="note">Note</button>
            </div>
            
            <form class="modal-form" id="createForm">
                <div class="form-group">
                    <label for="itemTitle">Title</label>
                    <input type="text" id="itemTitle" name="title" placeholder="Enter title..." required>
                </div>
                
                <div class="form-group">
                    <label for="itemDescription">Description</label>
                    <textarea id="itemDescription" name="description" placeholder="Add details..." rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="itemDate">Date</label>
                        <input type="date" id="itemDate" name="date">
                    </div>
                    
                    <div class="form-group">
                        <label for="itemPriority">Priority</label>
                        <select id="itemPriority" name="priority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelBtn">Cancel</button>
                    <button type="submit" class="btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../../JavaScript/dashboard.js"></script>
</body>
</html>