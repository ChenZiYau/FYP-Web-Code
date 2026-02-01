<?php
session_start();

// Initialize session variables
if (!isset($_SESSION['language'])) $_SESSION['language'] = 'en';
if (!isset($_SESSION['theme_color'])) $_SESSION['theme_color'] = '#7c5cfc';
if (!isset($_SESSION['todos'])) $_SESSION['todos'] = [
    ['id' => '1', 'title' => 'Discussion Algorithm', 'time' => '08:00 AM - 12:00 PM', 'category' => 'A', 'completed' => false],
    ['id' => '2', 'title' => 'Fundamental Math', 'time' => '12:00 PM - 15:00 PM', 'category' => 'M', 'completed' => false],
    ['id' => '3', 'title' => 'DNA Modifications in Humans', 'time' => 'Ongoing', 'category' => 'H', 'completed' => false],
];
if (!isset($_SESSION['flashcards'])) $_SESSION['flashcards'] = [];
if (!isset($_SESSION['expenses'])) $_SESSION['expenses'] = [];
if (!isset($_SESSION['budget'])) $_SESSION['budget'] = 1000;
if (!isset($_SESSION['xp'])) $_SESSION['xp'] = 60;
if (!isset($_SESSION['level'])) $_SESSION['level'] = 1;
if (!isset($_SESSION['achievements'])) $_SESSION['achievements'] = [];
if (!isset($_SESSION['chat_history'])) $_SESSION['chat_history'] = [];
if (!isset($_SESSION['google_token'])) $_SESSION['google_token'] = null;
if (!isset($_SESSION['projects'])) $_SESSION['projects'] = [
    ['id' => '1', 'name' => 'Project Frog Surgery', 'subject' => 'Biology', 'progress' => 85, 'deadline' => '12 August 2020', 'time' => '10:45'],
    ['id' => '2', 'name' => 'Project Earth Quantum', 'subject' => 'Science', 'progress' => 60, 'deadline' => '12 Sep 2020', 'time' => '10:45'],
];
if (!isset($_SESSION['inbox'])) $_SESSION['inbox'] = [
    ['id' => '1', 'name' => 'Michael Wong', 'message' => "Don't forget to work on assignment page 36 in...", 'time' => '09:32', 'attachment' => 'Exam-Science.xls'],
    ['id' => '2', 'name' => 'Cindy Chen', 'message' => 'Have you made history assignments?', 'time' => '11:32', 'attachment' => 'Algebra.pdf'],
];
if (!isset($_SESSION['exams'])) $_SESSION['exams'] = [
    ['subject' => 'Physics', 'class' => 'Class 3', 'day' => 'Tuesday', 'date' => '3 Dec 2020', 'time' => '08:30 AM', 'highlighted' => false],
    ['subject' => 'Biology', 'class' => 'Lab 1', 'day' => 'Monday', 'date' => '04 Dec 2020', 'time' => '08:30 AM', 'highlighted' => true],
    ['subject' => 'Math', 'class' => 'Practice 3', 'day' => 'Thursday', 'date' => '05 Dec 2020', 'time' => '08:30 AM', 'highlighted' => false],
];
if (!isset($_SESSION['friends'])) $_SESSION['friends'] = [
    ['name' => 'Francis Tran', 'status' => 'Health is not good.', 'time' => '05 Minutes Ago'],
    ['name' => 'Elliana Palacios', 'status' => 'Health is not good.', 'time' => '23 Minutes Ago'],
    ['name' => 'Katherine Webster', 'status' => 'Going on trip with my fam...', 'time' => '10 Minutes Ago'],
    ['name' => 'Avalon Carey', 'status' => 'Going on trip with my fam...', 'time' => '10 Minutes Ago'],
];
if (!isset($_SESSION['user_name'])) $_SESSION['user_name'] = 'Diane';
if (!isset($_SESSION['attendance'])) $_SESSION['attendance'] = ['present' => 359, 'late' => 12, 'absent' => 4];

$translations = [
    'en' => [
        'dashboard' => 'Dashboard', 'schedule' => 'Schedule', 'flashcards' => 'Flashcards',
        'ai_chat' => 'AI Chat', 'finance' => 'Finance', 'progress' => 'Progress', 'settings' => 'Settings',
        'students' => 'Students', 'exam' => 'Exam', 'projects' => 'Projects', 'policies' => 'Policies',
        'my_folder' => 'My Folder', 'payrolls' => 'Payrolls', 'reports' => 'Reports',
        'welcome_back' => 'WELCOME BACK!', 'goal_message' => 'You have completed 60% of your goal this week! start a new goal and improve your result',
        'learn_more' => 'Learn More', 'attendance' => 'Attendance', 'late' => 'Late', 'absent' => 'Absent',
        'friends_online' => 'Friends Online', 'new_chat' => 'New Chat', 'project_statistic' => 'Project Statistic',
        'inbox' => 'Inbox', 'exam_schedule' => 'Exam Schedule', 'upcoming_task' => 'Upcoming Task',
        'deadline' => 'Deadline', 'add_task' => 'Add Task', 'task_title' => 'Task Title',
        'calendar_view' => 'Calendar View', 'todo_list' => 'To-Do List', 'create_card' => 'Create Flashcard',
        'question' => 'Question', 'answer' => 'Answer', 'category' => 'Category',
        'upload_notes' => 'Upload Notes/PDF', 'share_deck' => 'Share Deck', 'my_cards' => 'My Flashcards',
        'flip_card' => 'Flip Card', 'chat_with_ai' => 'Chat with AI', 'type_message' => 'Type your message...',
        'send' => 'Send', 'finance_manager' => 'Finance Manager', 'monthly_budget' => 'Monthly Budget',
        'add_expense' => 'Add Expense', 'description' => 'Description', 'amount' => 'Amount', 'expense_type' => 'Type',
        'food' => 'Food', 'transport' => 'Transport', 'entertainment' => 'Entertainment', 'utilities' => 'Utilities', 'other' => 'Other',
        'recent_expenses' => 'Recent Expenses', 'total_spent' => 'Total Spent', 'budget_remaining' => 'Budget Remaining',
        'your_progress' => 'Your Progress', 'level' => 'Level', 'recent_achievements' => 'Recent Achievements',
        'achievement_first_task' => 'First Task!', 'achievement_flashcard_master' => 'Flashcard Master',
        'achievement_budget_hero' => 'Budget Hero', 'achievement_chat_starter' => 'Chat Starter',
        'google_calendar' => 'Google Calendar', 'connect_google' => 'Connect Google', 'connected' => 'Connected', 'not_connected' => 'Not Connected',
        'app_settings' => 'Settings', 'theme_color' => 'Theme Color', 'language' => 'Language', 'save_settings' => 'Save Settings',
        'no_tasks' => 'No tasks yet.', 'no_cards' => 'No flashcards yet.', 'no_expenses' => 'No expenses yet.',
    ],
    'ms' => [
        'dashboard' => 'Papan Pemuka', 'schedule' => 'Jadual', 'flashcards' => 'Kad Imbas',
        'ai_chat' => 'Sembang AI', 'finance' => 'Kewangan', 'progress' => 'Kemajuan', 'settings' => 'Tetapan',
        'students' => 'Pelajar', 'exam' => 'Peperiksaan', 'projects' => 'Projek', 'policies' => 'Polisi',
        'my_folder' => 'Folder Saya', 'payrolls' => 'Gaji', 'reports' => 'Laporan',
        'welcome_back' => 'SELAMAT KEMBALI!', 'goal_message' => 'Anda telah menyelesaikan 60% matlamat minggu ini!',
        'learn_more' => 'Ketahui Lebih', 'attendance' => 'Kehadiran', 'late' => 'Lewat', 'absent' => 'Tidak Hadir',
        'friends_online' => 'Rakan Dalam Talian', 'new_chat' => 'Sembang Baru', 'project_statistic' => 'Statistik Projek',
        'inbox' => 'Peti Masuk', 'exam_schedule' => 'Jadual Peperiksaan', 'upcoming_task' => 'Tugasan',
        'deadline' => 'Tarikh Akhir', 'add_task' => 'Tambah Tugasan', 'task_title' => 'Tajuk',
        'calendar_view' => 'Kalendar', 'todo_list' => 'Senarai Tugasan', 'create_card' => 'Cipta Kad',
        'question' => 'Soalan', 'answer' => 'Jawapan', 'category' => 'Kategori',
        'upload_notes' => 'Muat Naik', 'share_deck' => 'Kongsi', 'my_cards' => 'Kad Saya',
        'flip_card' => 'Balik', 'chat_with_ai' => 'Sembang AI', 'type_message' => 'Taip mesej...',
        'send' => 'Hantar', 'finance_manager' => 'Kewangan', 'monthly_budget' => 'Bajet Bulanan',
        'add_expense' => 'Tambah', 'description' => 'Keterangan', 'amount' => 'Jumlah', 'expense_type' => 'Jenis',
        'food' => 'Makanan', 'transport' => 'Pengangkutan', 'entertainment' => 'Hiburan', 'utilities' => 'Utiliti', 'other' => 'Lain',
        'recent_expenses' => 'Perbelanjaan', 'total_spent' => 'Jumlah', 'budget_remaining' => 'Baki',
        'your_progress' => 'Kemajuan', 'level' => 'Tahap', 'recent_achievements' => 'Pencapaian',
        'achievement_first_task' => 'Tugasan Pertama!', 'achievement_flashcard_master' => 'Master Kad',
        'achievement_budget_hero' => 'Wira Bajet', 'achievement_chat_starter' => 'Pemula Sembang',
        'google_calendar' => 'Google Calendar', 'connect_google' => 'Sambung', 'connected' => 'Bersambung', 'not_connected' => 'Tidak',
        'app_settings' => 'Tetapan', 'theme_color' => 'Warna', 'language' => 'Bahasa', 'save_settings' => 'Simpan',
        'no_tasks' => 'Tiada tugasan.', 'no_cards' => 'Tiada kad.', 'no_expenses' => 'Tiada perbelanjaan.',
    ]
];

$lang = $_SESSION['language'];
$t = $translations[$lang];

function addXP($amt) { $_SESSION['xp'] += $amt; if ($_SESSION['xp'] >= 100) { $_SESSION['xp'] -= 100; $_SESSION['level']++; } }
function addAchievement($k) { if (!in_array($k, $_SESSION['achievements'])) { $_SESSION['achievements'][] = $k; addXP(50); } }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'add_todo':
            $_SESSION['todos'][] = ['id' => uniqid(), 'title' => htmlspecialchars($_POST['title'] ?? ''), 'time' => $_POST['time'] ?? '', 'category' => strtoupper(substr($_POST['title'] ?? 'T', 0, 1)), 'completed' => false];
            addXP(10); break;
        case 'complete_todo':
            foreach ($_SESSION['todos'] as &$todo) if ($todo['id'] === ($_POST['todo_id'] ?? '')) { $todo['completed'] = true; addXP(20); } break;
        case 'delete_todo':
            $_SESSION['todos'] = array_values(array_filter($_SESSION['todos'], fn($t) => $t['id'] !== ($_POST['todo_id'] ?? ''))); break;
        case 'add_flashcard':
            $_SESSION['flashcards'][] = ['id' => uniqid(), 'question' => htmlspecialchars($_POST['question'] ?? ''), 'answer' => htmlspecialchars($_POST['answer'] ?? ''), 'category' => htmlspecialchars($_POST['category'] ?? 'General'), 'shared' => isset($_POST['shared'])];
            addXP(15); break;
        case 'delete_flashcard':
            $_SESSION['flashcards'] = array_filter($_SESSION['flashcards'], fn($c) => $c['id'] !== ($_POST['card_id'] ?? '')); break;
        case 'add_expense':
            $_SESSION['expenses'][] = ['id' => uniqid(), 'description' => htmlspecialchars($_POST['description'] ?? ''), 'amount' => floatval($_POST['amount'] ?? 0), 'type' => $_POST['expense_type'] ?? 'other']; break;
        case 'delete_expense':
            $_SESSION['expenses'] = array_filter($_SESSION['expenses'], fn($e) => $e['id'] !== ($_POST['expense_id'] ?? '')); break;
        case 'update_budget':
            $_SESSION['budget'] = floatval($_POST['budget'] ?? 1000); break;
        case 'send_chat':
            $msg = htmlspecialchars($_POST['message'] ?? '');
            if ($msg) { $_SESSION['chat_history'][] = ['role' => 'user', 'content' => $msg, 'time' => date('H:i')];
            $_SESSION['chat_history'][] = ['role' => 'ai', 'content' => ["Great question!", "I understand!", "Let me help!", "Interesting!"][rand(0,3)], 'time' => date('H:i')]; } break;
        case 'update_settings':
            if (isset($_POST['language'])) $_SESSION['language'] = $_POST['language'];
            if (isset($_POST['theme_color'])) $_SESSION['theme_color'] = $_POST['theme_color']; break;
    }
    header("Location: dashboard.php?page=" . urlencode($_GET['page'] ?? 'dashboard')); exit;
}

$page = $_GET['page'] ?? 'dashboard';
$themeColor = $_SESSION['theme_color'];
$totalExpenses = array_sum(array_column($_SESSION['expenses'], 'amount'));
$budgetRemaining = $_SESSION['budget'] - $totalExpenses;
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiPlan - <?php echo $t[$page] ?? 'Dashboard'; ?></title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>:root{--primary-color:<?php echo $themeColor; ?>;--primary-light:<?php echo $themeColor; ?>15;--primary-dark:<?php echo $themeColor; ?>dd;}</style>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="logo">
            <div class="logo-icon"><svg width="28" height="28" viewBox="0 0 32 32"><path d="M6 8C6 6.9 6.9 6 8 6H24C25.1 6 26 6.9 26 8V10L16 16L6 10V8Z" fill="var(--primary-color)"/><path d="M6 12L16 18L26 12V24C26 25.1 25.1 26 24 26H8C6.9 26 6 25.1 6 24V12Z" fill="var(--primary-color)" opacity="0.5"/></svg></div>
            <div class="logo-text"><span class="logo-title">OPTI</span><span class="logo-subtitle">PLAN</span></div>
        </div>
        <nav class="sidebar-nav">
            <a href="?page=dashboard" class="nav-item <?php echo $page==='dashboard'?'active':''; ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg><span><?php echo $t['dashboard']; ?></span></a>
            <a href="?page=schedule" class="nav-item <?php echo $page==='schedule'?'active':''; ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg><span><?php echo $t['students']; ?></span><span class="nav-badge"></span></a>
            <a href="?page=exam" class="nav-item <?php echo $page==='exam'?'active':''; ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg><span><?php echo $t['exam']; ?></span></a>
            <a href="?page=flashcards" class="nav-item <?php echo $page==='flashcards'?'active':''; ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15 8 22 9 17 14 18 21 12 18 6 21 7 14 2 9 9 8"/></svg><span><?php echo $t['projects']; ?></span></a>
            <a href="?page=finance" class="nav-item <?php echo $page==='finance'?'active':''; ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg><span><?php echo $t['policies']; ?></span></a>
            <a href="?page=progress" class="nav-item <?php echo $page==='progress'?'active':''; ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg><span><?php echo $t['my_folder']; ?></span><span class="nav-badge"></span></a>
            <a href="?page=chat" class="nav-item <?php echo $page==='chat'?'active':''; ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg><span><?php echo $t['payrolls']; ?></span></a>
            <a href="#" class="nav-item"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg><span><?php echo $t['reports']; ?></span></a>
        </nav>
        <a href="?page=settings" class="nav-item settings-link <?php echo $page==='settings'?'active':''; ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg><span><?php echo $t['settings']; ?></span></a>
    </aside>
    <div class="main-area">
        <header class="header">
            <h1 class="page-title"><?php echo $t[$page] ?? 'Dashboard'; ?></h1>
            <div class="header-right">
                <div class="lang-toggle"><button class="lang-btn <?php echo $lang==='en'?'active':''; ?>" onclick="setLanguage('en')">EN</button><button class="lang-btn <?php echo $lang==='ms'?'active':''; ?>" onclick="setLanguage('ms')">MS</button></div>
                <div class="theme-picker"><input type="color" id="themeColor" value="<?php echo $themeColor; ?>"></div>
                <div class="profile-dropdown">
                    <div class="profile-info"><img src="https://ui-avatars.com/api/?name=Annisa+F&background=7c5cfc&color=fff&size=40" class="profile-avatar" alt=""><span class="profile-name">Annisa F</span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></div>
                    <div class="dropdown-menu"><a href="?page=settings"><?php echo $t['settings']; ?></a></div>
                </div>
            </div>
        </header>
        <main class="main-content">
<?php if ($page === 'dashboard'): ?>
<div class="dashboard-layout">
    <div class="dashboard-left">
        <div class="welcome-banner">
            <div class="welcome-content">
                <h2><?php echo $t['welcome_back']; ?> <?php echo strtoupper($_SESSION['user_name']); ?></h2>
                <p><?php echo $t['goal_message']; ?></p>
                <button class="btn btn-white"><?php echo $t['learn_more']; ?></button>
            </div>
            <div class="welcome-illustration"><img src="https://illustrations.popsy.co/violet/student-going-to-school.svg" alt="Student"></div>
        </div>
        <div class="content-row">
            <div class="card project-stats">
                <h3><?php echo $t['project_statistic']; ?></h3>
                <?php foreach ($_SESSION['projects'] as $p): ?>
                <div class="project-item">
                    <div class="project-progress-ring"><svg viewBox="0 0 36 36"><path class="ring-bg" d="M18 2.0845a15.9155 15.9155 0 010 31.831 15.9155 15.9155 0 010-31.831"/><path class="ring-fill" stroke-dasharray="<?php echo $p['progress']; ?>,100" d="M18 2.0845a15.9155 15.9155 0 010 31.831 15.9155 15.9155 0 010-31.831"/></svg><span class="progress-text"><?php echo $p['progress']; ?></span></div>
                    <div class="project-info"><h4><?php echo $p['name']; ?></h4><span class="project-subject"><?php echo $p['subject']; ?></span><span class="project-deadline"><?php echo $t['deadline']; ?> <?php echo $p['deadline']; ?> ( <?php echo $p['time']; ?> )</span></div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="card inbox-card">
                <h3><?php echo $t['inbox']; ?></h3>
                <?php foreach ($_SESSION['inbox'] as $m): ?>
                <div class="inbox-item">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($m['name']); ?>&background=random&size=40" class="inbox-avatar" alt="">
                    <div class="inbox-content">
                        <div class="inbox-header"><span class="inbox-name"><?php echo $m['name']; ?></span><span class="inbox-time"><?php echo $m['time']; ?></span></div>
                        <p class="inbox-message"><?php echo $m['message']; ?></p>
                        <?php if ($m['attachment']): ?><span class="inbox-attachment"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/></svg><?php echo $m['attachment']; ?></span><?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="card exam-schedule">
            <h3><?php echo $t['exam_schedule']; ?></h3>
            <table class="schedule-table"><tbody>
                <?php foreach ($_SESSION['exams'] as $e): ?>
                <tr class="<?php echo ($e['highlighted']??false)?'highlighted':''; ?>">
                    <td class="subject-cell"><?php echo $e['subject']; ?></td><td><?php echo $e['class']; ?></td><td><?php echo $e['day']; ?></td><td><?php echo $e['date']; ?></td><td><?php echo $e['time']; ?></td>
                    <td><button class="btn-dots"><svg width="16" height="16" fill="currentColor"><circle cx="8" cy="3" r="1.5"/><circle cx="8" cy="8" r="1.5"/><circle cx="8" cy="13" r="1.5"/></svg></button></td>
                </tr>
                <?php endforeach; ?>
            </tbody></table>
        </div>
    </div>
    <div class="dashboard-right">
        <div class="card attendance-card">
            <div class="attendance-stats">
                <div class="stat-item"><span class="stat-label"><?php echo $t['attendance']; ?></span><span class="stat-value primary"><?php echo $_SESSION['attendance']['present']; ?></span></div>
                <div class="stat-item"><span class="stat-label"><?php echo $t['late']; ?></span><span class="stat-value warning"><?php echo $_SESSION['attendance']['late']; ?></span></div>
                <div class="stat-item"><span class="stat-label"><?php echo $t['absent']; ?></span><span class="stat-value danger"><?php echo $_SESSION['attendance']['absent']; ?></span></div>
            </div>
        </div>
        <div class="card friends-card">
            <h3><?php echo $t['friends_online']; ?></h3>
            <div class="friends-list">
                <?php foreach ($_SESSION['friends'] as $f): ?>
                <div class="friend-item">
                    <div class="friend-avatar"><img src="https://ui-avatars.com/api/?name=<?php echo urlencode($f['name']); ?>&background=random&size=40" alt=""><span class="online-dot"></span></div>
                    <div class="friend-info"><span class="friend-name"><?php echo $f['name']; ?></span><span class="friend-status"><?php echo $f['status']; ?></span></div>
                    <span class="friend-time"><?php echo $f['time']; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="btn btn-primary btn-full"><?php echo $t['new_chat']; ?></button>
        </div>
        <div class="card tasks-card">
            <h3><?php echo $t['upcoming_task']; ?></h3>
            <div class="tasks-list">
                <?php foreach ($_SESSION['todos'] as $todo): $colors=['A'=>'#fff3e6','M'=>'#fce4ec','H'=>'#e8f5e9','D'=>'#e3f2fd']; $tcolors=['A'=>'#ff9800','M'=>'#e91e63','H'=>'#4caf50','D'=>'#2196f3']; $cat=$todo['category']??'A'; ?>
                <div class="task-item <?php echo ($todo['completed']??false)?'completed':''; ?>">
                    <div class="task-badge" style="background:<?php echo $colors[$cat]??'#f5f5f5'; ?>;color:<?php echo $tcolors[$cat]??'#666'; ?>"><?php echo $cat; ?></div>
                    <div class="task-info"><span class="task-title"><?php echo $todo['title']; ?></span><span class="task-time"><?php echo $todo['time']??''; ?></span></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php elseif ($page === 'schedule' || $page === 'exam'): $month=date('n'); $year=date('Y'); $days=date('t'); $first=date('w',strtotime("$year-$month-01")); ?>
<div class="two-col-layout">
    <div class="col-left">
        <div class="card"><h3><?php echo $t['add_task']; ?></h3>
            <form method="POST"><input type="hidden" name="action" value="add_todo">
                <div class="form-group"><label><?php echo $t['task_title']; ?></label><input type="text" name="title" required></div>
                <div class="form-group"><label>Time</label><input type="text" name="time" placeholder="08:00 AM - 12:00 PM"></div>
                <button type="submit" class="btn btn-primary"><?php echo $t['add_task']; ?></button>
            </form>
        </div>
        <div class="card"><h3><?php echo $t['todo_list']; ?></h3>
            <div class="tasks-list-full">
                <?php if (empty($_SESSION['todos'])): ?><p class="empty-state"><?php echo $t['no_tasks']; ?></p>
                <?php else: foreach ($_SESSION['todos'] as $todo): ?>
                <div class="task-item-full <?php echo ($todo['completed']??false)?'completed':''; ?>">
                    <div class="task-badge"><?php echo $todo['category']??'T'; ?></div>
                    <div class="task-info"><span class="task-title"><?php echo $todo['title']; ?></span><span class="task-time"><?php echo $todo['time']??''; ?></span></div>
                    <div class="task-actions">
                        <?php if (!($todo['completed']??false)): ?><form method="POST" style="display:inline"><input type="hidden" name="action" value="complete_todo"><input type="hidden" name="todo_id" value="<?php echo $todo['id']; ?>"><button class="btn-icon success">✓</button></form><?php endif; ?>
                        <form method="POST" style="display:inline"><input type="hidden" name="action" value="delete_todo"><input type="hidden" name="todo_id" value="<?php echo $todo['id']; ?>"><button class="btn-icon danger">×</button></form>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
    <div class="col-right">
        <div class="card"><h3><?php echo $t['calendar_view']; ?></h3>
            <div class="calendar-header"><span class="month-name"><?php echo date('F Y'); ?></span></div>
            <div class="calendar-grid">
                <div class="calendar-day-header">Sun</div><div class="calendar-day-header">Mon</div><div class="calendar-day-header">Tue</div><div class="calendar-day-header">Wed</div><div class="calendar-day-header">Thu</div><div class="calendar-day-header">Fri</div><div class="calendar-day-header">Sat</div>
                <?php for($i=0;$i<$first;$i++) echo '<div class="calendar-day empty"></div>'; for($d=1;$d<=$days;$d++): $today=$d==date('j'); ?><div class="calendar-day <?php echo $today?'today':''; ?>"><span><?php echo $d; ?></span></div><?php endfor; ?>
            </div>
        </div>
        <div class="card"><h3><?php echo $t['google_calendar']; ?></h3>
            <div class="sync-status"><span class="status-indicator <?php echo $_SESSION['google_token']?'connected':''; ?>"></span><span><?php echo $_SESSION['google_token']?$t['connected']:$t['not_connected']; ?></span></div>
            <button class="btn btn-secondary"><?php echo $t['connect_google']; ?></button>
        </div>
    </div>
</div>
<?php elseif ($page === 'flashcards'): ?>
<div class="two-col-layout">
    <div class="col-left">
        <div class="card"><h3><?php echo $t['create_card']; ?></h3>
            <form method="POST"><input type="hidden" name="action" value="add_flashcard">
                <div class="form-group"><label><?php echo $t['question']; ?></label><textarea name="question" rows="3" required></textarea></div>
                <div class="form-group"><label><?php echo $t['answer']; ?></label><textarea name="answer" rows="3" required></textarea></div>
                <div class="form-row"><div class="form-group"><label><?php echo $t['category']; ?></label><input type="text" name="category" placeholder="General"></div>
                <div class="form-group checkbox-group"><label><input type="checkbox" name="shared"> <?php echo $t['share_deck']; ?></label></div></div>
                <button type="submit" class="btn btn-primary"><?php echo $t['create_card']; ?></button>
            </form>
            <div class="upload-section"><h4><?php echo $t['upload_notes']; ?></h4>
                <div class="upload-area" id="uploadArea"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg><p>Drag & drop files here</p></div>
            </div>
        </div>
    </div>
    <div class="col-right">
        <div class="card"><h3><?php echo $t['my_cards']; ?></h3>
            <div class="flashcards-grid">
                <?php if (empty($_SESSION['flashcards'])): ?><p class="empty-state"><?php echo $t['no_cards']; ?></p>
                <?php else: foreach ($_SESSION['flashcards'] as $card): ?>
                <div class="flashcard" onclick="flipCard(this)">
                    <div class="flashcard-inner">
                        <div class="flashcard-front"><span class="card-category"><?php echo $card['category']; ?></span><p><?php echo $card['question']; ?></p><span class="flip-hint"><?php echo $t['flip_card']; ?></span></div>
                        <div class="flashcard-back"><p><?php echo $card['answer']; ?></p></div>
                    </div>
                    <form method="POST" class="card-delete" onclick="event.stopPropagation()"><input type="hidden" name="action" value="delete_flashcard"><input type="hidden" name="card_id" value="<?php echo $card['id']; ?>"><button class="btn-icon danger">×</button></form>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>
<?php elseif ($page === 'chat'): ?>
<div class="chat-layout">
    <div class="card chat-container">
        <div class="chat-messages" id="chatMessages">
            <?php if (empty($_SESSION['chat_history'])): ?>
            <div class="chat-welcome"><svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="1.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg><h3><?php echo $t['chat_with_ai']; ?></h3><p>Ask me anything!</p></div>
            <?php else: foreach ($_SESSION['chat_history'] as $msg): ?>
            <div class="chat-message <?php echo $msg['role']; ?>"><div class="message-content"><?php echo $msg['content']; ?></div><span class="message-time"><?php echo $msg['time']; ?></span></div>
            <?php endforeach; endif; ?>
        </div>
        <form method="POST" class="chat-input-form"><input type="hidden" name="action" value="send_chat"><input type="text" name="message" placeholder="<?php echo $t['type_message']; ?>" autocomplete="off" required><button type="submit" class="btn btn-primary"><?php echo $t['send']; ?></button></form>
    </div>
</div>
<?php elseif ($page === 'finance'): ?>
<div class="two-col-layout">
    <div class="col-left">
        <div class="card"><h3><?php echo $t['monthly_budget']; ?></h3>
            <form method="POST" class="budget-form"><input type="hidden" name="action" value="update_budget">
                <div class="budget-display"><span class="currency">$</span><input type="number" name="budget" value="<?php echo $_SESSION['budget']; ?>" class="budget-input"></div>
                <button type="submit" class="btn btn-secondary">Update</button>
            </form>
            <div class="budget-progress"><div class="progress-bar large"><div class="progress-fill <?php echo $totalExpenses>$_SESSION['budget']?'over':''; ?>" style="width:<?php echo min(100,($totalExpenses/$_SESSION['budget'])*100); ?>%"></div></div>
            <div class="budget-stats"><span><?php echo $t['total_spent']; ?>: $<?php echo number_format($totalExpenses,2); ?></span><span><?php echo $t['budget_remaining']; ?>: $<?php echo number_format($budgetRemaining,2); ?></span></div></div>
        </div>
        <div class="card"><h3><?php echo $t['add_expense']; ?></h3>
            <form method="POST"><input type="hidden" name="action" value="add_expense">
                <div class="form-group"><label><?php echo $t['description']; ?></label><input type="text" name="description" required></div>
                <div class="form-row"><div class="form-group"><label><?php echo $t['amount']; ?></label><input type="number" name="amount" step="0.01" min="0" required></div>
                <div class="form-group"><label><?php echo $t['expense_type']; ?></label><select name="expense_type"><option value="food"><?php echo $t['food']; ?></option><option value="transport"><?php echo $t['transport']; ?></option><option value="entertainment"><?php echo $t['entertainment']; ?></option><option value="utilities"><?php echo $t['utilities']; ?></option><option value="other"><?php echo $t['other']; ?></option></select></div></div>
                <button type="submit" class="btn btn-primary"><?php echo $t['add_expense']; ?></button>
            </form>
        </div>
    </div>
    <div class="col-right">
        <div class="card"><h3><?php echo $t['recent_expenses']; ?></h3>
            <div class="expense-list">
                <?php if (empty($_SESSION['expenses'])): ?><p class="empty-state"><?php echo $t['no_expenses']; ?></p>
                <?php else: foreach (array_reverse($_SESSION['expenses']) as $exp): ?>
                <div class="expense-item">
                    <div class="expense-icon type-<?php echo $exp['type']; ?>"><?php echo strtoupper(substr($exp['type'],0,1)); ?></div>
                    <div class="expense-info"><span class="expense-desc"><?php echo $exp['description']; ?></span><span class="expense-type"><?php echo $t[$exp['type']]??$exp['type']; ?></span></div>
                    <div class="expense-actions"><span class="expense-amount">-$<?php echo number_format($exp['amount'],2); ?></span>
                    <form method="POST" style="display:inline"><input type="hidden" name="action" value="delete_expense"><input type="hidden" name="expense_id" value="<?php echo $exp['id']; ?>"><button class="btn-icon danger">×</button></form></div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>
<?php elseif ($page === 'progress'): $achievements=['first_task'=>$t['achievement_first_task'],'flashcard_master'=>$t['achievement_flashcard_master'],'budget_hero'=>$t['achievement_budget_hero'],'chat_starter'=>$t['achievement_chat_starter']]; ?>
<div class="progress-layout">
    <div class="card level-card">
        <div class="level-display">
            <div class="level-circle large"><svg viewBox="0 0 36 36"><path class="ring-bg" d="M18 2.0845a15.9155 15.9155 0 010 31.831 15.9155 15.9155 0 010-31.831"/><path class="ring-fill" stroke-dasharray="<?php echo $_SESSION['xp']; ?>,100" d="M18 2.0845a15.9155 15.9155 0 010 31.831 15.9155 15.9155 0 010-31.831"/></svg><span class="level-number"><?php echo $_SESSION['level']; ?></span></div>
            <div class="level-info"><h2><?php echo $t['level']; ?> <?php echo $_SESSION['level']; ?></h2><p><?php echo $_SESSION['xp']; ?> / 100 XP</p><div class="xp-bar"><div class="xp-fill" style="width:<?php echo $_SESSION['xp']; ?>%"></div></div></div>
        </div>
    </div>
    <div class="card"><h3><?php echo $t['recent_achievements']; ?></h3>
        <div class="achievements-grid">
            <?php foreach (['first_task','flashcard_master','budget_hero','chat_starter'] as $k): $unlocked=in_array($k,$_SESSION['achievements']); ?>
            <div class="achievement <?php echo $unlocked?'unlocked':'locked'; ?>">
                <div class="achievement-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15 8 22 9 17 14 18 21 12 18 6 21 7 14 2 9 9 8"/></svg></div>
                <span class="achievement-name"><?php echo $achievements[$k]; ?></span>
                <?php if (!$unlocked): ?><span class="locked-badge">🔒</span><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php elseif ($page === 'settings'): ?>
<div class="settings-layout">
    <div class="card settings-card"><h3><?php echo $t['app_settings']; ?></h3>
        <form method="POST" class="settings-form"><input type="hidden" name="action" value="update_settings">
            <div class="form-group"><label><?php echo $t['language']; ?></label><select name="language"><option value="en" <?php echo $lang==='en'?'selected':''; ?>>English</option><option value="ms" <?php echo $lang==='ms'?'selected':''; ?>>Bahasa Malaysia</option></select></div>
            <div class="form-group"><label><?php echo $t['theme_color']; ?></label><div class="color-picker-row"><input type="color" id="settings_theme" name="theme_color" value="<?php echo $themeColor; ?>"><span class="color-value"><?php echo $themeColor; ?></span></div></div>
            <div class="preset-colors"><span>Quick Colors:</span><?php foreach(['#7c5cfc','#10b981','#f59e0b','#ef4444','#3b82f6','#ec4899'] as $c): ?><button type="button" class="color-preset" style="background:<?php echo $c; ?>" onclick="setPresetColor('<?php echo $c; ?>')"></button><?php endforeach; ?></div>
            <button type="submit" class="btn btn-primary"><?php echo $t['save_settings']; ?></button>
        </form>
    </div>
</div>
<?php endif; ?>
        </main>
        <footer class="footer"><p>© 2026 OptiPlan. All rights reserved.</p></footer>
    </div>
</div>
<script src="dashboard.js"></script>
</body>
</html>