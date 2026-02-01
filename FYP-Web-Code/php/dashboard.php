<?php
session_start();

// Initialize session variables
if (!isset($_SESSION['language'])) $_SESSION['language'] = 'en';
if (!isset($_SESSION['theme_color'])) $_SESSION['theme_color'] = '#6366f1';
if (!isset($_SESSION['todos'])) $_SESSION['todos'] = [];
if (!isset($_SESSION['flashcards'])) $_SESSION['flashcards'] = [];
if (!isset($_SESSION['expenses'])) $_SESSION['expenses'] = [];
if (!isset($_SESSION['budget'])) $_SESSION['budget'] = 1000;
if (!isset($_SESSION['xp'])) $_SESSION['xp'] = 0;
if (!isset($_SESSION['level'])) $_SESSION['level'] = 1;
if (!isset($_SESSION['achievements'])) $_SESSION['achievements'] = [];
if (!isset($_SESSION['chat_history'])) $_SESSION['chat_history'] = [];
if (!isset($_SESSION['calendar_events'])) $_SESSION['calendar_events'] = [];
if (!isset($_SESSION['google_token'])) $_SESSION['google_token'] = null;

// Language translations
$translations = [
    'en' => [
        'dashboard' => 'Dashboard',
        'schedule' => 'Schedule',
        'flashcards' => 'Flashcards',
        'ai_chat' => 'AI Chat',
        'finance' => 'Finance',
        'progress' => 'Progress',
        'settings' => 'Settings',
        'welcome' => 'Welcome to OptiPlan',
        'welcome_desc' => 'Your all-in-one productivity companion',
        'quick_stats' => 'Quick Stats',
        'tasks_today' => 'Tasks Today',
        'cards_to_review' => 'Cards to Review',
        'budget_remaining' => 'Budget Remaining',
        'current_level' => 'Current Level',
        'add_task' => 'Add Task',
        'task_title' => 'Task Title',
        'due_date' => 'Due Date',
        'priority' => 'Priority',
        'high' => 'High',
        'medium' => 'Medium',
        'low' => 'Low',
        'calendar_view' => 'Calendar View',
        'todo_list' => 'To-Do List',
        'create_card' => 'Create Flashcard',
        'question' => 'Question',
        'answer' => 'Answer',
        'category' => 'Category',
        'upload_notes' => 'Upload Notes/PDF',
        'share_deck' => 'Share Deck',
        'my_cards' => 'My Flashcards',
        'flip_card' => 'Flip Card',
        'chat_with_ai' => 'Chat with AI',
        'type_message' => 'Type your message...',
        'send' => 'Send',
        'finance_manager' => 'Finance Manager',
        'monthly_budget' => 'Monthly Budget',
        'add_expense' => 'Add Expense',
        'description' => 'Description',
        'amount' => 'Amount',
        'expense_type' => 'Type',
        'food' => 'Food',
        'transport' => 'Transport',
        'entertainment' => 'Entertainment',
        'utilities' => 'Utilities',
        'other' => 'Other',
        'recent_expenses' => 'Recent Expenses',
        'total_spent' => 'Total Spent',
        'your_progress' => 'Your Progress',
        'level' => 'Level',
        'experience' => 'Experience',
        'recent_achievements' => 'Recent Achievements',
        'achievement_first_task' => 'First Task Completed!',
        'achievement_flashcard_master' => 'Flashcard Master',
        'achievement_budget_hero' => 'Budget Hero',
        'achievement_chat_starter' => 'AI Chat Starter',
        'google_calendar' => 'Google Calendar Sync',
        'connect_google' => 'Connect Google Calendar',
        'sync_status' => 'Sync Status',
        'connected' => 'Connected',
        'not_connected' => 'Not Connected',
        'app_settings' => 'Application Settings',
        'theme_color' => 'Theme Color',
        'language' => 'Language',
        'save_settings' => 'Save Settings',
        'delete' => 'Delete',
        'complete' => 'Complete',
        'no_tasks' => 'No tasks yet. Add one above!',
        'no_cards' => 'No flashcards yet. Create one above!',
        'no_expenses' => 'No expenses recorded yet.',
    ],
    'ms' => [
        'dashboard' => 'Papan Pemuka',
        'schedule' => 'Jadual',
        'flashcards' => 'Kad Imbas',
        'ai_chat' => 'Sembang AI',
        'finance' => 'Kewangan',
        'progress' => 'Kemajuan',
        'settings' => 'Tetapan',
        'welcome' => 'Selamat Datang ke OptiPlan',
        'welcome_desc' => 'Teman produktiviti serba boleh anda',
        'quick_stats' => 'Statistik Pantas',
        'tasks_today' => 'Tugasan Hari Ini',
        'cards_to_review' => 'Kad untuk Semak',
        'budget_remaining' => 'Baki Bajet',
        'current_level' => 'Tahap Semasa',
        'add_task' => 'Tambah Tugasan',
        'task_title' => 'Tajuk Tugasan',
        'due_date' => 'Tarikh Tamat',
        'priority' => 'Keutamaan',
        'high' => 'Tinggi',
        'medium' => 'Sederhana',
        'low' => 'Rendah',
        'calendar_view' => 'Paparan Kalendar',
        'todo_list' => 'Senarai Tugasan',
        'create_card' => 'Cipta Kad Imbas',
        'question' => 'Soalan',
        'answer' => 'Jawapan',
        'category' => 'Kategori',
        'upload_notes' => 'Muat Naik Nota/PDF',
        'share_deck' => 'Kongsi Dek',
        'my_cards' => 'Kad Imbas Saya',
        'flip_card' => 'Balik Kad',
        'chat_with_ai' => 'Sembang dengan AI',
        'type_message' => 'Taip mesej anda...',
        'send' => 'Hantar',
        'finance_manager' => 'Pengurus Kewangan',
        'monthly_budget' => 'Bajet Bulanan',
        'add_expense' => 'Tambah Perbelanjaan',
        'description' => 'Keterangan',
        'amount' => 'Jumlah',
        'expense_type' => 'Jenis',
        'food' => 'Makanan',
        'transport' => 'Pengangkutan',
        'entertainment' => 'Hiburan',
        'utilities' => 'Utiliti',
        'other' => 'Lain-lain',
        'recent_expenses' => 'Perbelanjaan Terkini',
        'total_spent' => 'Jumlah Dibelanja',
        'your_progress' => 'Kemajuan Anda',
        'level' => 'Tahap',
        'experience' => 'Pengalaman',
        'recent_achievements' => 'Pencapaian Terkini',
        'achievement_first_task' => 'Tugasan Pertama Selesai!',
        'achievement_flashcard_master' => 'Master Kad Imbas',
        'achievement_budget_hero' => 'Wira Bajet',
        'achievement_chat_starter' => 'Pemula Sembang AI',
        'google_calendar' => 'Segerak Google Calendar',
        'connect_google' => 'Sambung Google Calendar',
        'sync_status' => 'Status Segerak',
        'connected' => 'Bersambung',
        'not_connected' => 'Tidak Bersambung',
        'app_settings' => 'Tetapan Aplikasi',
        'theme_color' => 'Warna Tema',
        'language' => 'Bahasa',
        'save_settings' => 'Simpan Tetapan',
        'delete' => 'Padam',
        'complete' => 'Selesai',
        'no_tasks' => 'Tiada tugasan lagi. Tambah satu di atas!',
        'no_cards' => 'Tiada kad imbas lagi. Cipta satu di atas!',
        'no_expenses' => 'Tiada perbelanjaan direkodkan lagi.',
    ]
];

$lang = $_SESSION['language'];
$t = $translations[$lang];

// Helper function for XP and leveling
function addXP($amount) {
    $_SESSION['xp'] += $amount;
    $xpNeeded = $_SESSION['level'] * 100;
    if ($_SESSION['xp'] >= $xpNeeded) {
        $_SESSION['xp'] -= $xpNeeded;
        $_SESSION['level']++;
    }
}

function addAchievement($key) {
    if (!in_array($key, $_SESSION['achievements'])) {
        $_SESSION['achievements'][] = $key;
        addXP(50);
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_todo':
            $todo = [
                'id' => uniqid(),
                'title' => htmlspecialchars($_POST['title'] ?? ''),
                'due_date' => $_POST['due_date'] ?? '',
                'priority' => $_POST['priority'] ?? 'medium',
                'completed' => false,
                'created' => date('Y-m-d H:i:s')
            ];
            $_SESSION['todos'][] = $todo;
            addXP(10);
            if (count($_SESSION['todos']) === 1) addAchievement('first_task');
            break;
            
        case 'complete_todo':
            $id = $_POST['todo_id'] ?? '';
            foreach ($_SESSION['todos'] as &$todo) {
                if ($todo['id'] === $id) {
                    $todo['completed'] = true;
                    addXP(20);
                    break;
                }
            }
            break;
            
        case 'delete_todo':
            $id = $_POST['todo_id'] ?? '';
            $_SESSION['todos'] = array_filter($_SESSION['todos'], fn($t) => $t['id'] !== $id);
            break;
            
        case 'add_flashcard':
            $card = [
                'id' => uniqid(),
                'question' => htmlspecialchars($_POST['question'] ?? ''),
                'answer' => htmlspecialchars($_POST['answer'] ?? ''),
                'category' => htmlspecialchars($_POST['category'] ?? 'General'),
                'shared' => isset($_POST['shared']),
                'created' => date('Y-m-d H:i:s')
            ];
            $_SESSION['flashcards'][] = $card;
            addXP(15);
            if (count($_SESSION['flashcards']) >= 10) addAchievement('flashcard_master');
            break;
            
        case 'delete_flashcard':
            $id = $_POST['card_id'] ?? '';
            $_SESSION['flashcards'] = array_filter($_SESSION['flashcards'], fn($c) => $c['id'] !== $id);
            break;
            
        case 'add_expense':
            $expense = [
                'id' => uniqid(),
                'description' => htmlspecialchars($_POST['description'] ?? ''),
                'amount' => floatval($_POST['amount'] ?? 0),
                'type' => $_POST['expense_type'] ?? 'other',
                'date' => date('Y-m-d H:i:s')
            ];
            $_SESSION['expenses'][] = $expense;
            $totalSpent = array_sum(array_column($_SESSION['expenses'], 'amount'));
            if ($totalSpent <= $_SESSION['budget'] * 0.5) addAchievement('budget_hero');
            break;
            
        case 'delete_expense':
            $id = $_POST['expense_id'] ?? '';
            $_SESSION['expenses'] = array_filter($_SESSION['expenses'], fn($e) => $e['id'] !== $id);
            break;
            
        case 'update_budget':
            $_SESSION['budget'] = floatval($_POST['budget'] ?? 1000);
            break;
            
        case 'send_chat':
            $message = htmlspecialchars($_POST['message'] ?? '');
            if (!empty($message)) {
                $_SESSION['chat_history'][] = ['role' => 'user', 'content' => $message, 'time' => date('H:i')];
                // Simulated AI response
                $responses = [
                    "That's a great question! Let me help you with that.",
                    "I understand. Here's what I suggest...",
                    "Interesting! Have you considered looking at it from this angle?",
                    "I'm here to help! What else would you like to know?",
                    "Great progress! Keep up the good work with your studies.",
                ];
                $_SESSION['chat_history'][] = [
                    'role' => 'ai',
                    'content' => $responses[array_rand($responses)],
                    'time' => date('H:i')
                ];
                if (count($_SESSION['chat_history']) === 2) addAchievement('chat_starter');
            }
            break;
            
        case 'update_settings':
            if (isset($_POST['language'])) $_SESSION['language'] = $_POST['language'];
            if (isset($_POST['theme_color'])) $_SESSION['theme_color'] = $_POST['theme_color'];
            $lang = $_SESSION['language'];
            $t = $translations[$lang];
            break;
            
        case 'add_calendar_event':
            $event = [
                'id' => uniqid(),
                'title' => htmlspecialchars($_POST['event_title'] ?? ''),
                'date' => $_POST['event_date'] ?? '',
                'time' => $_POST['event_time'] ?? '',
            ];
            $_SESSION['calendar_events'][] = $event;
            addXP(10);
            break;
    }
    
    // Redirect to prevent form resubmission
    $redirectPage = $_GET['page'] ?? 'dashboard';
    header("Location: dashboard.php?page=" . urlencode($redirectPage));
    exit;
}

// Google Calendar OAuth2 Boilerplate
class GoogleCalendarAPI {
    private $clientId = 'YOUR_CLIENT_ID';
    private $clientSecret = 'YOUR_CLIENT_SECRET';
    private $redirectUri = 'YOUR_REDIRECT_URI';
    private $scope = 'https://www.googleapis.com/auth/calendar';
    
    public function getAuthUrl() {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => $this->scope,
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }
    
    public function exchangeCode($code) {
        $url = 'https://oauth2.googleapis.com/token';
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    public function refreshToken($refreshToken) {
        $url = 'https://oauth2.googleapis.com/token';
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    public function getEvents($accessToken) {
        $url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}

// Handle Google OAuth callback
if (isset($_GET['code'])) {
    $googleApi = new GoogleCalendarAPI();
    $tokens = $googleApi->exchangeCode($_GET['code']);
    if (isset($tokens['access_token'])) {
        $_SESSION['google_token'] = $tokens;
    }
    header('Location: dashboard.php?page=schedule');
    exit;
}

$page = $_GET['page'] ?? 'dashboard';
$themeColor = $_SESSION['theme_color'];

// Calculate stats
$totalTasks = count($_SESSION['todos']);
$completedTasks = count(array_filter($_SESSION['todos'], fn($t) => $t['completed']));
$pendingTasks = $totalTasks - $completedTasks;
$totalCards = count($_SESSION['flashcards']);
$totalExpenses = array_sum(array_column($_SESSION['expenses'], 'amount'));
$budgetRemaining = $_SESSION['budget'] - $totalExpenses;
$xpProgress = ($_SESSION['xp'] / ($_SESSION['level'] * 100)) * 100;
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiPlan - <?php echo $t[$page] ?? 'Dashboard'; ?></title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        :root {
            --primary-color: <?php echo $themeColor; ?>;
            --primary-light: <?php echo $themeColor; ?>20;
            --primary-dark: <?php echo $themeColor; ?>dd;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <a href="dashboard.php" class="logo">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                        <rect width="32" height="32" rx="8" fill="var(--primary-color)"/>
                        <path d="M8 16L14 22L24 10" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>OptiPlan</span>
                </a>
            </div>
            <div class="header-right">
                <div class="lang-toggle">
                    <button class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>" onclick="setLanguage('en')">EN</button>
                    <button class="lang-btn <?php echo $lang === 'ms' ? 'active' : ''; ?>" onclick="setLanguage('ms')">MS</button>
                </div>
                <div class="theme-picker">
                    <input type="color" id="themeColor" value="<?php echo $themeColor; ?>" title="<?php echo $t['theme_color']; ?>">
                </div>
                <div class="profile-dropdown">
                    <button class="profile-btn">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 20c0-4 4-6 8-6s8 2 8 6"/>
                        </svg>
                    </button>
                    <div class="dropdown-menu">
                        <a href="dashboard.php?page=settings"><?php echo $t['settings']; ?></a>
                    </div>
                </div>
            </div>
        </header>

        <div class="main-wrapper">
            <!-- Sidebar -->
            <aside class="sidebar">
                <nav class="sidebar-nav">
                    <a href="dashboard.php?page=dashboard" class="nav-item <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        <span><?php echo $t['dashboard']; ?></span>
                    </a>
                    <a href="dashboard.php?page=schedule" class="nav-item <?php echo $page === 'schedule' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                            <line x1="9" y1="4" x2="9" y2="2"/>
                            <line x1="15" y1="4" x2="15" y2="2"/>
                        </svg>
                        <span><?php echo $t['schedule']; ?></span>
                    </a>
                    <a href="dashboard.php?page=flashcards" class="nav-item <?php echo $page === 'flashcards' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="6" width="16" height="12" rx="2"/>
                            <rect x="6" y="4" width="16" height="12" rx="2"/>
                        </svg>
                        <span><?php echo $t['flashcards']; ?></span>
                    </a>
                    <a href="dashboard.php?page=chat" class="nav-item <?php echo $page === 'chat' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                        </svg>
                        <span><?php echo $t['ai_chat']; ?></span>
                    </a>
                    <a href="dashboard.php?page=finance" class="nav-item <?php echo $page === 'finance' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                        </svg>
                        <span><?php echo $t['finance']; ?></span>
                    </a>
                    <a href="dashboard.php?page=progress" class="nav-item <?php echo $page === 'progress' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                            <polyline points="17 6 23 6 23 12"/>
                        </svg>
                        <span><?php echo $t['progress']; ?></span>
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="main-content">
                <?php
                switch ($page) {
                    case 'dashboard':
                        ?>
                        <div class="page-header">
                            <h1><?php echo $t['welcome']; ?></h1>
                            <p><?php echo $t['welcome_desc']; ?></p>
                        </div>
                        
                        <section class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon tasks">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 11l3 3L22 4"/>
                                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                                    </svg>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-value"><?php echo $pendingTasks; ?></span>
                                    <span class="stat-label"><?php echo $t['tasks_today']; ?></span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon cards">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="6" width="16" height="12" rx="2"/>
                                        <rect x="6" y="4" width="16" height="12" rx="2"/>
                                    </svg>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-value"><?php echo $totalCards; ?></span>
                                    <span class="stat-label"><?php echo $t['cards_to_review']; ?></span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon budget">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="1" x2="12" y2="23"/>
                                        <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                                    </svg>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-value">$<?php echo number_format($budgetRemaining, 2); ?></span>
                                    <span class="stat-label"><?php echo $t['budget_remaining']; ?></span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon level">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-value"><?php echo $_SESSION['level']; ?></span>
                                    <span class="stat-label"><?php echo $t['current_level']; ?></span>
                                </div>
                            </div>
                        </section>

                        <section class="dashboard-grid">
                            <div class="quick-tasks card">
                                <h3><?php echo $t['todo_list']; ?></h3>
                                <ul class="task-list">
                                    <?php 
                                    $recentTodos = array_slice(array_filter($_SESSION['todos'], fn($t) => !$t['completed']), 0, 5);
                                    if (empty($recentTodos)): ?>
                                        <li class="empty-state"><?php echo $t['no_tasks']; ?></li>
                                    <?php else:
                                        foreach ($recentTodos as $todo): ?>
                                        <li class="task-item priority-<?php echo $todo['priority']; ?>">
                                            <span><?php echo $todo['title']; ?></span>
                                            <span class="task-date"><?php echo $todo['due_date']; ?></span>
                                        </li>
                                    <?php endforeach; endif; ?>
                                </ul>
                                <a href="dashboard.php?page=schedule" class="view-all">View All →</a>
                            </div>
                            
                            <div class="progress-widget card">
                                <h3><?php echo $t['your_progress']; ?></h3>
                                <div class="level-display">
                                    <span class="level-badge"><?php echo $t['level']; ?> <?php echo $_SESSION['level']; ?></span>
                                    <div class="xp-bar">
                                        <div class="xp-fill" style="width: <?php echo $xpProgress; ?>%"></div>
                                    </div>
                                    <span class="xp-text"><?php echo $_SESSION['xp']; ?> / <?php echo $_SESSION['level'] * 100; ?> XP</span>
                                </div>
                            </div>
                        </section>
                        <?php
                        break;

                    case 'schedule':
                        $currentMonth = date('n');
                        $currentYear = date('Y');
                        $daysInMonth = date('t');
                        $firstDay = date('w', strtotime("$currentYear-$currentMonth-01"));
                        ?>
                        <div class="page-header">
                            <h1><?php echo $t['schedule']; ?></h1>
                        </div>

                        <div class="schedule-layout">
                            <section class="todo-section card">
                                <h3><?php echo $t['add_task']; ?></h3>
                                <form method="POST" class="todo-form">
                                    <input type="hidden" name="action" value="add_todo">
                                    <div class="form-group">
                                        <label for="title"><?php echo $t['task_title']; ?></label>
                                        <input type="text" id="title" name="title" required>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="due_date"><?php echo $t['due_date']; ?></label>
                                            <input type="date" id="due_date" name="due_date" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="priority"><?php echo $t['priority']; ?></label>
                                            <select id="priority" name="priority">
                                                <option value="high"><?php echo $t['high']; ?></option>
                                                <option value="medium" selected><?php echo $t['medium']; ?></option>
                                                <option value="low"><?php echo $t['low']; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><?php echo $t['add_task']; ?></button>
                                </form>

                                <h3 style="margin-top: 2rem;"><?php echo $t['todo_list']; ?></h3>
                                <ul class="task-list full">
                                    <?php if (empty($_SESSION['todos'])): ?>
                                        <li class="empty-state"><?php echo $t['no_tasks']; ?></li>
                                    <?php else:
                                        foreach ($_SESSION['todos'] as $todo): ?>
                                        <li class="task-item priority-<?php echo $todo['priority']; ?> <?php echo $todo['completed'] ? 'completed' : ''; ?>">
                                            <div class="task-content">
                                                <span class="task-title"><?php echo $todo['title']; ?></span>
                                                <span class="task-date"><?php echo $todo['due_date']; ?></span>
                                            </div>
                                            <div class="task-actions">
                                                <?php if (!$todo['completed']): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="complete_todo">
                                                    <input type="hidden" name="todo_id" value="<?php echo $todo['id']; ?>">
                                                    <button type="submit" class="btn-icon" title="<?php echo $t['complete']; ?>">✓</button>
                                                </form>
                                                <?php endif; ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="delete_todo">
                                                    <input type="hidden" name="todo_id" value="<?php echo $todo['id']; ?>">
                                                    <button type="submit" class="btn-icon delete" title="<?php echo $t['delete']; ?>">×</button>
                                                </form>
                                            </div>
                                        </li>
                                    <?php endforeach; endif; ?>
                                </ul>
                            </section>

                            <section class="calendar-section card">
                                <h3><?php echo $t['calendar_view']; ?></h3>
                                <div class="calendar-header">
                                    <span class="month-name"><?php echo date('F Y'); ?></span>
                                </div>
                                <div class="calendar-grid">
                                    <div class="calendar-day-header">Sun</div>
                                    <div class="calendar-day-header">Mon</div>
                                    <div class="calendar-day-header">Tue</div>
                                    <div class="calendar-day-header">Wed</div>
                                    <div class="calendar-day-header">Thu</div>
                                    <div class="calendar-day-header">Fri</div>
                                    <div class="calendar-day-header">Sat</div>
                                    <?php
                                    for ($i = 0; $i < $firstDay; $i++) {
                                        echo '<div class="calendar-day empty"></div>';
                                    }
                                    for ($day = 1; $day <= $daysInMonth; $day++) {
                                        $dateStr = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                                        $hasTask = !empty(array_filter($_SESSION['todos'], fn($t) => $t['due_date'] === $dateStr));
                                        $isToday = $day == date('j');
                                        $class = 'calendar-day';
                                        if ($isToday) $class .= ' today';
                                        if ($hasTask) $class .= ' has-task';
                                        echo "<div class=\"$class\"><span>$day</span></div>";
                                    }
                                    ?>
                                </div>
                                
                                <div class="google-sync">
                                    <h4><?php echo $t['google_calendar']; ?></h4>
                                    <div class="sync-status">
                                        <span class="status-indicator <?php echo $_SESSION['google_token'] ? 'connected' : ''; ?>"></span>
                                        <span><?php echo $t['sync_status']; ?>: <?php echo $_SESSION['google_token'] ? $t['connected'] : $t['not_connected']; ?></span>
                                    </div>
                                    <?php if (!$_SESSION['google_token']): ?>
                                    <a href="#" class="btn btn-secondary" onclick="alert('Configure Google API credentials to enable this feature')"><?php echo $t['connect_google']; ?></a>
                                    <?php endif; ?>
                                </div>
                            </section>
                        </div>
                        <?php
                        break;

                    case 'flashcards':
                        ?>
                        <div class="page-header">
                            <h1><?php echo $t['flashcards']; ?></h1>
                        </div>

                        <div class="flashcards-layout">
                            <section class="create-card card">
                                <h3><?php echo $t['create_card']; ?></h3>
                                <form method="POST" class="flashcard-form">
                                    <input type="hidden" name="action" value="add_flashcard">
                                    <div class="form-group">
                                        <label for="question"><?php echo $t['question']; ?></label>
                                        <textarea id="question" name="question" rows="3" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="answer"><?php echo $t['answer']; ?></label>
                                        <textarea id="answer" name="answer" rows="3" required></textarea>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="category"><?php echo $t['category']; ?></label>
                                            <input type="text" id="category" name="category" placeholder="General">
                                        </div>
                                        <div class="form-group checkbox-group">
                                            <label>
                                                <input type="checkbox" name="shared">
                                                <?php echo $t['share_deck']; ?>
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><?php echo $t['create_card']; ?></button>
                                </form>

                                <div class="upload-section">
                                    <h4><?php echo $t['upload_notes']; ?></h4>
                                    <div class="upload-area" id="uploadArea">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                            <polyline points="17 8 12 3 7 8"/>
                                            <line x1="12" y1="3" x2="12" y2="15"/>
                                        </svg>
                                        <p>Drag & drop files here or click to browse</p>
                                        <input type="file" id="fileUpload" accept=".pdf,.txt,.doc,.docx" hidden>
                                    </div>
                                </div>
                            </section>

                            <section class="cards-list card">
                                <h3><?php echo $t['my_cards']; ?></h3>
                                <div class="flashcards-grid">
                                    <?php if (empty($_SESSION['flashcards'])): ?>
                                        <p class="empty-state"><?php echo $t['no_cards']; ?></p>
                                    <?php else:
                                        foreach ($_SESSION['flashcards'] as $card): ?>
                                        <div class="flashcard" onclick="flipCard(this)">
                                            <div class="flashcard-inner">
                                                <div class="flashcard-front">
                                                    <span class="card-category"><?php echo $card['category']; ?></span>
                                                    <p><?php echo $card['question']; ?></p>
                                                    <span class="flip-hint"><?php echo $t['flip_card']; ?></span>
                                                </div>
                                                <div class="flashcard-back">
                                                    <p><?php echo $card['answer']; ?></p>
                                                    <?php if ($card['shared']): ?>
                                                        <span class="shared-badge">Shared</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <form method="POST" class="card-delete" onclick="event.stopPropagation();">
                                                <input type="hidden" name="action" value="delete_flashcard">
                                                <input type="hidden" name="card_id" value="<?php echo $card['id']; ?>">
                                                <button type="submit" class="btn-icon delete">×</button>
                                            </form>
                                        </div>
                                    <?php endforeach; endif; ?>
                                </div>
                            </section>
                        </div>
                        <?php
                        break;

                    case 'chat':
                        ?>
                        <div class="page-header">
                            <h1><?php echo $t['ai_chat']; ?></h1>
                        </div>

                        <section class="chat-container card">
                            <div class="chat-messages" id="chatMessages">
                                <?php if (empty($_SESSION['chat_history'])): ?>
                                    <div class="chat-welcome">
                                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="1.5">
                                            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                                        </svg>
                                        <h3><?php echo $t['chat_with_ai']; ?></h3>
                                        <p>Ask me anything about your studies, schedule, or productivity!</p>
                                    </div>
                                <?php else:
                                    foreach ($_SESSION['chat_history'] as $msg): ?>
                                    <div class="chat-message <?php echo $msg['role']; ?>">
                                        <div class="message-content">
                                            <?php echo $msg['content']; ?>
                                        </div>
                                        <span class="message-time"><?php echo $msg['time']; ?></span>
                                    </div>
                                <?php endforeach; endif; ?>
                            </div>
                            <form method="POST" class="chat-input-form">
                                <input type="hidden" name="action" value="send_chat">
                                <input type="text" name="message" placeholder="<?php echo $t['type_message']; ?>" autocomplete="off" required>
                                <button type="submit" class="btn btn-primary"><?php echo $t['send']; ?></button>
                            </form>
                        </section>
                        <?php
                        break;

                    case 'finance':
                        $expensesByType = [];
                        foreach ($_SESSION['expenses'] as $exp) {
                            $type = $exp['type'];
                            if (!isset($expensesByType[$type])) $expensesByType[$type] = 0;
                            $expensesByType[$type] += $exp['amount'];
                        }
                        ?>
                        <div class="page-header">
                            <h1><?php echo $t['finance_manager']; ?></h1>
                        </div>

                        <div class="finance-layout">
                            <section class="budget-overview card">
                                <h3><?php echo $t['monthly_budget']; ?></h3>
                                <form method="POST" class="budget-form">
                                    <input type="hidden" name="action" value="update_budget">
                                    <div class="budget-display">
                                        <span class="currency">$</span>
                                        <input type="number" name="budget" value="<?php echo $_SESSION['budget']; ?>" class="budget-input">
                                    </div>
                                    <button type="submit" class="btn btn-secondary">Update</button>
                                </form>
                                
                                <div class="budget-progress">
                                    <div class="progress-bar">
                                        <div class="progress-fill <?php echo $totalExpenses > $_SESSION['budget'] ? 'over' : ''; ?>" 
                                             style="width: <?php echo min(100, ($totalExpenses / $_SESSION['budget']) * 100); ?>%"></div>
                                    </div>
                                    <div class="budget-stats">
                                        <span><?php echo $t['total_spent']; ?>: $<?php echo number_format($totalExpenses, 2); ?></span>
                                        <span><?php echo $t['budget_remaining']; ?>: $<?php echo number_format($budgetRemaining, 2); ?></span>
                                    </div>
                                </div>

                                <?php if (!empty($expensesByType)): ?>
                                <div class="expense-breakdown">
                                    <h4>Breakdown by Category</h4>
                                    <div class="breakdown-bars">
                                        <?php foreach ($expensesByType as $type => $amount): 
                                            $percentage = ($amount / $totalExpenses) * 100;
                                        ?>
                                        <div class="breakdown-item">
                                            <span class="breakdown-label"><?php echo $t[$type] ?? ucfirst($type); ?></span>
                                            <div class="breakdown-bar">
                                                <div class="breakdown-fill type-<?php echo $type; ?>" style="width: <?php echo $percentage; ?>%"></div>
                                            </div>
                                            <span class="breakdown-amount">$<?php echo number_format($amount, 2); ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </section>

                            <section class="add-expense card">
                                <h3><?php echo $t['add_expense']; ?></h3>
                                <form method="POST" class="expense-form">
                                    <input type="hidden" name="action" value="add_expense">
                                    <div class="form-group">
                                        <label for="description"><?php echo $t['description']; ?></label>
                                        <input type="text" id="description" name="description" required>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="amount"><?php echo $t['amount']; ?></label>
                                            <input type="number" id="amount" name="amount" step="0.01" min="0" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="expense_type"><?php echo $t['expense_type']; ?></label>
                                            <select id="expense_type" name="expense_type">
                                                <option value="food"><?php echo $t['food']; ?></option>
                                                <option value="transport"><?php echo $t['transport']; ?></option>
                                                <option value="entertainment"><?php echo $t['entertainment']; ?></option>
                                                <option value="utilities"><?php echo $t['utilities']; ?></option>
                                                <option value="other"><?php echo $t['other']; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><?php echo $t['add_expense']; ?></button>
                                </form>

                                <h3 style="margin-top: 2rem;"><?php echo $t['recent_expenses']; ?></h3>
                                <ul class="expense-list">
                                    <?php 
                                    $recentExpenses = array_slice(array_reverse($_SESSION['expenses']), 0, 10);
                                    if (empty($recentExpenses)): ?>
                                        <li class="empty-state"><?php echo $t['no_expenses']; ?></li>
                                    <?php else:
                                        foreach ($recentExpenses as $expense): ?>
                                        <li class="expense-item type-<?php echo $expense['type']; ?>">
                                            <div class="expense-info">
                                                <span class="expense-desc"><?php echo $expense['description']; ?></span>
                                                <span class="expense-type"><?php echo $t[$expense['type']] ?? ucfirst($expense['type']); ?></span>
                                            </div>
                                            <div class="expense-actions">
                                                <span class="expense-amount">-$<?php echo number_format($expense['amount'], 2); ?></span>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="delete_expense">
                                                    <input type="hidden" name="expense_id" value="<?php echo $expense['id']; ?>">
                                                    <button type="submit" class="btn-icon delete">×</button>
                                                </form>
                                            </div>
                                        </li>
                                    <?php endforeach; endif; ?>
                                </ul>
                            </section>
                        </div>
                        <?php
                        break;

                    case 'progress':
                        $achievementNames = [
                            'first_task' => $t['achievement_first_task'],
                            'flashcard_master' => $t['achievement_flashcard_master'],
                            'budget_hero' => $t['achievement_budget_hero'],
                            'chat_starter' => $t['achievement_chat_starter'],
                        ];
                        ?>
                        <div class="page-header">
                            <h1><?php echo $t['your_progress']; ?></h1>
                        </div>

                        <div class="progress-layout">
                            <section class="level-section card">
                                <div class="level-hero">
                                    <div class="level-circle">
                                        <svg viewBox="0 0 100 100">
                                            <circle cx="50" cy="50" r="45" fill="none" stroke="var(--border-color)" stroke-width="8"/>
                                            <circle cx="50" cy="50" r="45" fill="none" stroke="var(--primary-color)" stroke-width="8"
                                                    stroke-dasharray="<?php echo $xpProgress * 2.83; ?> 283"
                                                    transform="rotate(-90 50 50)"/>
                                        </svg>
                                        <span class="level-number"><?php echo $_SESSION['level']; ?></span>
                                    </div>
                                    <div class="level-info">
                                        <h2><?php echo $t['level']; ?> <?php echo $_SESSION['level']; ?></h2>
                                        <p><?php echo $_SESSION['xp']; ?> / <?php echo $_SESSION['level'] * 100; ?> XP</p>
                                        <p class="next-level">Next level: <?php echo ($_SESSION['level'] * 100) - $_SESSION['xp']; ?> XP needed</p>
                                    </div>
                                </div>
                            </section>

                            <section class="stats-section card">
                                <h3><?php echo $t['quick_stats']; ?></h3>
                                <div class="stats-list">
                                    <div class="stat-row">
                                        <span><?php echo $t['tasks_today']; ?></span>
                                        <span class="stat-value"><?php echo $completedTasks; ?> / <?php echo $totalTasks; ?></span>
                                    </div>
                                    <div class="stat-row">
                                        <span><?php echo $t['flashcards']; ?></span>
                                        <span class="stat-value"><?php echo $totalCards; ?></span>
                                    </div>
                                    <div class="stat-row">
                                        <span><?php echo $t['total_spent']; ?></span>
                                        <span class="stat-value">$<?php echo number_format($totalExpenses, 2); ?></span>
                                    </div>
                                </div>
                            </section>

                            <section class="achievements-section card">
                                <h3><?php echo $t['recent_achievements']; ?></h3>
                                <div class="achievements-grid">
                                    <?php 
                                    $allAchievements = ['first_task', 'flashcard_master', 'budget_hero', 'chat_starter'];
                                    foreach ($allAchievements as $key): 
                                        $unlocked = in_array($key, $_SESSION['achievements']);
                                    ?>
                                    <div class="achievement <?php echo $unlocked ? 'unlocked' : 'locked'; ?>">
                                        <div class="achievement-icon">
                                            <?php if ($key === 'first_task'): ?>
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M9 11l3 3L22 4"/>
                                                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                                                </svg>
                                            <?php elseif ($key === 'flashcard_master'): ?>
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="2" y="6" width="16" height="12" rx="2"/>
                                                    <rect x="6" y="4" width="16" height="12" rx="2"/>
                                                </svg>
                                            <?php elseif ($key === 'budget_hero'): ?>
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <line x1="12" y1="1" x2="12" y2="23"/>
                                                    <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                                                </svg>
                                            <?php else: ?>
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                                                </svg>
                                            <?php endif; ?>
                                        </div>
                                        <span class="achievement-name"><?php echo $achievementNames[$key]; ?></span>
                                        <?php if (!$unlocked): ?>
                                            <span class="locked-badge">🔒</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        </div>
                        <?php
                        break;

                    case 'settings':
                        ?>
                        <div class="page-header">
                            <h1><?php echo $t['app_settings']; ?></h1>
                        </div>

                        <section class="settings-section card">
                            <form method="POST" class="settings-form">
                                <input type="hidden" name="action" value="update_settings">
                                
                                <div class="form-group">
                                    <label for="settings_language"><?php echo $t['language']; ?></label>
                                    <select id="settings_language" name="language">
                                        <option value="en" <?php echo $lang === 'en' ? 'selected' : ''; ?>>English</option>
                                        <option value="ms" <?php echo $lang === 'ms' ? 'selected' : ''; ?>>Bahasa Malaysia</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="settings_theme"><?php echo $t['theme_color']; ?></label>
                                    <div class="color-picker-row">
                                        <input type="color" id="settings_theme" name="theme_color" value="<?php echo $themeColor; ?>">
                                        <span class="color-value"><?php echo $themeColor; ?></span>
                                    </div>
                                </div>

                                <div class="preset-colors">
                                    <span>Quick Colors:</span>
                                    <button type="button" class="color-preset" style="background: #6366f1" onclick="setPresetColor('#6366f1')"></button>
                                    <button type="button" class="color-preset" style="background: #10b981" onclick="setPresetColor('#10b981')"></button>
                                    <button type="button" class="color-preset" style="background: #f59e0b" onclick="setPresetColor('#f59e0b')"></button>
                                    <button type="button" class="color-preset" style="background: #ef4444" onclick="setPresetColor('#ef4444')"></button>
                                    <button type="button" class="color-preset" style="background: #8b5cf6" onclick="setPresetColor('#8b5cf6')"></button>
                                    <button type="button" class="color-preset" style="background: #ec4899" onclick="setPresetColor('#ec4899')"></button>
                                </div>

                                <button type="submit" class="btn btn-primary"><?php echo $t['save_settings']; ?></button>
                            </form>
                        </section>
                        <?php
                        break;

                    default:
                        header('Location: dashboard.php?page=dashboard');
                        exit;
                }
                ?>
            </main>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>© 2026 OptiPlan. All rights reserved.</p>
        </footer>
    </div>

    <!-- Floating AI Chat Button (on non-chat pages) -->
    <?php if ($page !== 'chat'): ?>
    <div class="floating-chat" id="floatingChat">
        <button class="chat-toggle" onclick="toggleFloatingChat()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
            </svg>
        </button>
        <div class="floating-chat-window" id="floatingChatWindow">
            <div class="floating-chat-header">
                <span><?php echo $t['ai_chat']; ?></span>
                <button onclick="toggleFloatingChat()">×</button>
            </div>
            <div class="floating-chat-messages">
                <?php foreach (array_slice($_SESSION['chat_history'], -5) as $msg): ?>
                <div class="chat-message <?php echo $msg['role']; ?>">
                    <div class="message-content"><?php echo $msg['content']; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <form method="POST" action="dashboard.php?page=<?php echo $page; ?>" class="floating-chat-input">
                <input type="hidden" name="action" value="send_chat">
                <input type="text" name="message" placeholder="<?php echo $t['type_message']; ?>" autocomplete="off">
                <button type="submit"><?php echo $t['send']; ?></button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script src="dashboard.js"></script>
</body>
</html>