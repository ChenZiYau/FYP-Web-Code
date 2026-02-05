<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['name'] ?? 'User';
$user_id = $_SESSION['user_id'];
$user_level = 19;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Tracker - OptiPlan</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/finance.css">
    <!-- Chart.js CDN - MUST BE IN HEAD -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        // Debug: Check if Chart.js loaded
        window.addEventListener('DOMContentLoaded', function() {
            console.log('🔍 Chart.js loaded:', typeof Chart !== 'undefined' ? '✅ YES' : '❌ NO');
            console.log('🔍 Canvas element found:', document.getElementById('expenseChart') ? '✅ YES' : '❌ NO');
        });
    </script>
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
                <span class="nav-text">Add Expense</span>
            </button>
            
            <a href="dashboard.php" class="nav-item">
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
            
            <a href="finance.php" class="nav-item active">
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
                    <h1 class="welcome-text">Finance Tracker</h1>
                    <p class="subtitle-text">Manage your spending and stay on budget</p>
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
                </button>
            </div>
        </header>
        
        <!-- Finance Dashboard Content -->
        <div class="finance-container">
            
            <!-- Summary Cards Row -->
            <section class="finance-summary">
                <div class="summary-card total-spent">
                    <div class="summary-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="summary-content">
                        <p class="summary-label">Today's Spending</p>
                        <h2 class="summary-amount" id="todayTotal">$0.00</h2>
                    </div>
                </div>
                
                <div class="summary-card weekly-spent">
                    <div class="summary-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="summary-content">
                        <p class="summary-label">This Week</p>
                        <h2 class="summary-amount" id="weekTotal">$0.00</h2>
                    </div>
                </div>
                
                <div class="summary-card monthly-spent">
                    <div class="summary-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="summary-content">
                        <p class="summary-label">This Month</p>
                        <h2 class="summary-amount" id="monthTotal">$0.00</h2>
                    </div>
                </div>
                
                <div class="summary-card budget-remaining">
                    <div class="summary-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="summary-content">
                        <p class="summary-label">Budget Remaining</p>
                        <h2 class="summary-amount" id="budgetRemaining">$1000.00</h2>
                    </div>
                </div>
            </section>
            
            <!-- Charts and Expenses Row -->
            <div class="finance-grid">
                
                <!-- Expense Breakdown Chart -->
                <section class="chart-section">
                    <div class="section-header">
                        <h2 class="section-title">Spending by Category</h2>
                        <div class="time-filter">
                            <button class="filter-btn active" data-period="week">Week</button>
                            <button class="filter-btn" data-period="month">Month</button>
                            <button class="filter-btn" data-period="year">Year</button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="expenseChart"></canvas>
                    </div>
                    <div class="chart-legend" id="chartLegend">
                        <!-- Legend will be generated dynamically -->
                    </div>
                </section>
                
                <!-- Recent Expenses List -->
                <section class="expenses-section">
                    <div class="section-header">
                        <h2 class="section-title">Recent Expenses</h2>
                        <button class="view-all-btn" id="viewAllExpenses">View All →</button>
                    </div>
                    <div class="expenses-list" id="expensesList">
                        <!-- Expenses will be loaded here -->
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p>No expenses yet</p>
                            <button class="add-expense-btn" onclick="document.getElementById('createBtn').click()">Add Your First Expense</button>
                        </div>
                    </div>
                </section>
                
                <!-- Spending Trends Chart -->
                <section class="trends-section">
                    <div class="section-header">
                        <h2 class="section-title">Spending Trends</h2>
                    </div>
                    <div class="chart-container">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </section>
                
                <!-- Budget Overview -->
                <section class="budget-section">
                    <div class="section-header">
                        <h2 class="section-title">Budget Overview</h2>
                        <button class="edit-budget-btn" id="editBudget">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                    </div>
                    <div class="budget-categories" id="budgetCategories">
                        <!-- Budget items will be generated here -->
                    </div>
                </section>
                
            </div>
            
        </div>
        
    </main>
    
    <!-- Add Expense Modal -->
    <div class="modal" id="expenseModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add Expense</h2>
                <button class="modal-close" id="closeExpenseModal">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form class="expense-form" id="expenseForm">
                <div class="form-group">
                    <label for="expenseAmount">Amount ($)</label>
                    <input type="number" id="expenseAmount" name="amount" step="0.01" min="0" placeholder="0.00" required>
                </div>
                
                <div class="form-group">
                    <label for="expenseCategory">Category</label>
                    <div class="category-grid">
                        <label class="category-option">
                            <input type="radio" name="category" value="food" required>
                            <span class="category-card">
                                <span class="category-icon">🍔</span>
                                <span class="category-name">Food</span>
                            </span>
                        </label>
                        <label class="category-option">
                            <input type="radio" name="category" value="transport" required>
                            <span class="category-card">
                                <span class="category-icon">🚗</span>
                                <span class="category-name">Transport</span>
                            </span>
                        </label>
                        <label class="category-option">
                            <input type="radio" name="category" value="shopping" required>
                            <span class="category-card">
                                <span class="category-icon">🛍️</span>
                                <span class="category-name">Shopping</span>
                            </span>
                        </label>
                        <label class="category-option">
                            <input type="radio" name="category" value="entertainment" required>
                            <span class="category-card">
                                <span class="category-icon">🎮</span>
                                <span class="category-name">Entertainment</span>
                            </span>
                        </label>
                        <label class="category-option">
                            <input type="radio" name="category" value="education" required>
                            <span class="category-card">
                                <span class="category-icon">📚</span>
                                <span class="category-name">Education</span>
                            </span>
                        </label>
                        <label class="category-option">
                            <input type="radio" name="category" value="health" required>
                            <span class="category-card">
                                <span class="category-icon">⚕️</span>
                                <span class="category-name">Health</span>
                            </span>
                        </label>
                        <label class="category-option">
                            <input type="radio" name="category" value="bills" required>
                            <span class="category-card">
                                <span class="category-icon">💳</span>
                                <span class="category-name">Bills</span>
                            </span>
                        </label>
                        <label class="category-option">
                            <input type="radio" name="category" value="other" required>
                            <span class="category-card">
                                <span class="category-icon">📦</span>
                                <span class="category-name">Other</span>
                            </span>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="expenseDescription">Description (Optional)</label>
                    <input type="text" id="expenseDescription" name="description" placeholder="What did you spend on?">
                </div>
                
                <div class="form-group">
                    <label for="expenseDate">Date</label>
                    <input type="date" id="expenseDate" name="date" required>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelExpense">Cancel</button>
                    <button type="submit" class="btn-primary">Add Expense</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Budget Edit Modal -->
    <div class="modal" id="budgetModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Set Monthly Budget</h2>
                <button class="modal-close" id="closeBudgetModal">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form class="budget-form" id="budgetForm">
                <div class="form-group">
                    <label for="totalBudget">Total Monthly Budget ($)</label>
                    <input type="number" id="totalBudget" name="totalBudget" step="0.01" min="0" placeholder="0.00" required>
                </div>
                
                <p class="form-hint">Set budget limits for each category (optional)</p>
                
                <div class="budget-inputs">
                    <div class="form-group">
                        <label for="budgetFood">🍔 Food</label>
                        <input type="number" id="budgetFood" name="budgetFood" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="budgetTransport">🚗 Transport</label>
                        <input type="number" id="budgetTransport" name="budgetTransport" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="budgetShopping">🛍️ Shopping</label>
                        <input type="number" id="budgetShopping" name="budgetShopping" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="budgetEntertainment">🎮 Entertainment</label>
                        <input type="number" id="budgetEntertainment" name="budgetEntertainment" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="budgetEducation">📚 Education</label>
                        <input type="number" id="budgetEducation" name="budgetEducation" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="budgetHealth">⚕️ Health</label>
                        <input type="number" id="budgetHealth" name="budgetHealth" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="budgetBills">💳 Bills</label>
                        <input type="number" id="budgetBills" name="budgetBills" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="budgetOther">📦 Other</label>
                        <input type="number" id="budgetOther" name="budgetOther" step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelBudget">Cancel</button>
                    <button type="submit" class="btn-primary">Save Budget</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Scripts at bottom - IMPORTANT ORDER -->
    <script src="../JavaScript/dashboard.js"></script>
    <script src="../JavaScript/finance.js"></script>
</body>
</html>