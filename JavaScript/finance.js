/* ==========================================
   OPTIPLAN FINANCE TRACKER - JAVASCRIPT
   ========================================== */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // STORAGE KEYS
    // ==========================================
    const STORAGE_KEYS = {
        EXPENSES: 'optiplan_expenses',
        BUDGET: 'optiplan_budget'
    };

    // ==========================================
    // EXPENSE DATA MANAGEMENT
    // ==========================================
    class ExpenseManager {
        constructor() {
            this.expenses = this.loadExpenses();
            this.budget = this.loadBudget();
        }

        loadExpenses() {
            const stored = localStorage.getItem(STORAGE_KEYS.EXPENSES);
            return stored ? JSON.parse(stored) : [];
        }

        saveExpenses() {
            localStorage.setItem(STORAGE_KEYS.EXPENSES, JSON.stringify(this.expenses));
        }

        loadBudget() {
            const stored = localStorage.getItem(STORAGE_KEYS.BUDGET);
            return stored ? JSON.parse(stored) : {
                total: 1000,
                food: 300,
                transport: 150,
                shopping: 200,
                entertainment: 100,
                education: 150,
                health: 100,
                bills: 0,
                other: 0
            };
        }

        saveBudget() {
            localStorage.setItem(STORAGE_KEYS.BUDGET, JSON.stringify(this.budget));
        }

        addExpense(expense) {
            const newExpense = {
                id: Date.now(),
                ...expense,
                timestamp: new Date().toISOString()
            };
            this.expenses.unshift(newExpense);
            this.saveExpenses();
            return newExpense;
        }

        deleteExpense(id) {
            this.expenses = this.expenses.filter(exp => exp.id !== id);
            this.saveExpenses();
        }

        getExpensesByPeriod(period) {
            const now = new Date();
            let startDate;

            switch (period) {
                case 'today':
                    startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    break;
                case 'week':
                    startDate = new Date(now.setDate(now.getDate() - 7));
                    break;
                case 'month':
                    startDate = new Date(now.getFullYear(), now.getMonth(), 1);
                    break;
                case 'year':
                    startDate = new Date(now.getFullYear(), 0, 1);
                    break;
                default:
                    startDate = new Date(0);
            }

            return this.expenses.filter(exp => new Date(exp.date) >= startDate);
        }

        getTotalByPeriod(period) {
            const expenses = this.getExpensesByPeriod(period);
            return expenses.reduce((sum, exp) => sum + parseFloat(exp.amount), 0);
        }

        getCategoryTotals(period = 'month') {
            const expenses = this.getExpensesByPeriod(period);
            const totals = {};

            expenses.forEach(exp => {
                if (!totals[exp.category]) {
                    totals[exp.category] = 0;
                }
                totals[exp.category] += parseFloat(exp.amount);
            });

            return totals;
        }

        updateBudget(budgetData) {
            this.budget = { ...this.budget, ...budgetData };
            this.saveBudget();
        }
    }

    const expenseManager = new ExpenseManager();

    // ==========================================
    // CATEGORY CONFIGURATION
    // ==========================================
    const CATEGORIES = {
        food: { icon: '🍔', name: 'Food', color: '#ef4444' },
        transport: { icon: '🚗', name: 'Transport', color: '#f59e0b' },
        shopping: { icon: '🛍️', name: 'Shopping', color: '#ec4899' },
        entertainment: { icon: '🎮', name: 'Entertainment', color: '#8b5cf6' },
        education: { icon: '📚', name: 'Education', color: '#3b82f6' },
        health: { icon: '⚕️', name: 'Health', color: '#10b981' },
        bills: { icon: '💳', name: 'Bills', color: '#6366f1' },
        other: { icon: '📦', name: 'Other', color: '#64748b' }
    };

    // ==========================================
    // UI UPDATES
    // ==========================================
    function updateSummaryCards() {
        // Today's total
        const todayTotal = expenseManager.getTotalByPeriod('today');
        document.getElementById('todayTotal').textContent = `$${todayTotal.toFixed(2)}`;

        // Week total
        const weekTotal = expenseManager.getTotalByPeriod('week');
        document.getElementById('weekTotal').textContent = `$${weekTotal.toFixed(2)}`;

        // Month total
        const monthTotal = expenseManager.getTotalByPeriod('month');
        document.getElementById('monthTotal').textContent = `$${monthTotal.toFixed(2)}`;

        // Budget remaining
        const budgetRemaining = expenseManager.budget.total - monthTotal;
        const remainingEl = document.getElementById('budgetRemaining');
        remainingEl.textContent = `$${budgetRemaining.toFixed(2)}`;
        
        // Change color based on budget status
        if (budgetRemaining < 0) {
            remainingEl.style.color = '#ef4444';
        } else if (budgetRemaining < expenseManager.budget.total * 0.2) {
            remainingEl.style.color = '#f59e0b';
        } else {
            remainingEl.style.color = '#10b981';
        }
    }

    function renderExpensesList() {
        const expensesList = document.getElementById('expensesList');
        const recentExpenses = expenseManager.expenses.slice(0, 10);

        if (recentExpenses.length === 0) {
            expensesList.innerHTML = `
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p>No expenses yet</p>
                    <button class="add-expense-btn" onclick="document.getElementById('createBtn').click()">Add Your First Expense</button>
                </div>
            `;
            return;
        }

        expensesList.innerHTML = recentExpenses.map(expense => {
            const category = CATEGORIES[expense.category] || CATEGORIES.other;
            const expenseDate = new Date(expense.date);
            const formattedDate = expenseDate.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric' 
            });

            return `
                <div class="expense-item">
                    <div class="expense-category-icon">${category.icon}</div>
                    <div class="expense-details">
                        <div class="expense-description">${expense.description || category.name}</div>
                        <div class="expense-meta">
                            <span class="expense-date">${formattedDate}</span>
                            <span class="expense-category-name">${category.name}</span>
                        </div>
                    </div>
                    <div class="expense-amount">$${parseFloat(expense.amount).toFixed(2)}</div>
                    <button class="expense-delete" onclick="deleteExpense(${expense.id})">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            `;
        }).join('');
    }

    function renderBudgetCategories() {
        const budgetContainer = document.getElementById('budgetCategories');
        const categoryTotals = expenseManager.getCategoryTotals('month');

        budgetContainer.innerHTML = Object.keys(CATEGORIES).map(categoryKey => {
            const category = CATEGORIES[categoryKey];
            const spent = categoryTotals[categoryKey] || 0;
            const limit = expenseManager.budget[categoryKey] || 0;
            
            if (limit === 0) return ''; // Don't show categories with no budget

            const percentage = limit > 0 ? (spent / limit) * 100 : 0;
            const status = percentage >= 100 ? 'danger' : percentage >= 80 ? 'warning' : 'safe';
            const statusText = percentage >= 100 ? 'Over Budget' : `${(100 - percentage).toFixed(0)}% left`;

            return `
                <div class="budget-category-item">
                    <div class="budget-category-header">
                        <div class="budget-category-info">
                            <span class="budget-category-icon">${category.icon}</span>
                            <span class="budget-category-name">${category.name}</span>
                        </div>
                        <div class="budget-category-amounts">
                            <span class="budget-spent">$${spent.toFixed(2)}</span>
                            <span class="budget-limit">of $${limit.toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="budget-progress">
                        <div class="budget-progress-bar ${status}" style="width: ${Math.min(percentage, 100)}%"></div>
                    </div>
                    <div class="budget-status ${status}">${statusText}</div>
                </div>
            `;
        }).join('');
    }

    // ==========================================
    // CHARTS
    // ==========================================
    let expenseChart = null;
    let trendsChart = null;

    function createExpenseChart(period = 'week') {
        const ctx = document.getElementById('expenseChart');
        if (!ctx) return;

        const categoryTotals = expenseManager.getCategoryTotals(period);
        const labels = [];
        const data = [];
        const colors = [];

        Object.keys(categoryTotals).forEach(categoryKey => {
            const category = CATEGORIES[categoryKey] || CATEGORIES.other;
            labels.push(category.name);
            data.push(categoryTotals[categoryKey]);
            colors.push(category.color);
        });

        if (expenseChart) {
            expenseChart.destroy();
        }

        expenseChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderColor: '#1a1625',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1a1625',
                        titleColor: '#e5dff7',
                        bodyColor: '#d1c7e8',
                        borderColor: '#a78bfa',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: $${value.toFixed(2)} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });

        // Update legend
        updateChartLegend(labels, data, colors);
    }

    function updateChartLegend(labels, data, colors) {
        const legendContainer = document.getElementById('chartLegend');
        if (!legendContainer) return;

        const total = data.reduce((a, b) => a + b, 0);

        legendContainer.innerHTML = labels.map((label, index) => `
            <div class="legend-item">
                <div class="legend-color" style="background: ${colors[index]}"></div>
                <div class="legend-info">
                    <span class="legend-label">${label}</span>
                    <span class="legend-amount">$${data[index].toFixed(2)}</span>
                </div>
            </div>
        `).join('');
    }

    function createTrendsChart() {
        const ctx = document.getElementById('trendsChart');
        if (!ctx) return;

        // Get last 7 days of data
        const last7Days = [];
        const dailyTotals = [];
        const now = new Date();

        for (let i = 6; i >= 0; i--) {
            const date = new Date(now);
            date.setDate(date.getDate() - i);
            const dateStr = date.toISOString().split('T')[0];
            
            last7Days.push(date.toLocaleDateString('en-US', { weekday: 'short' }));
            
            const dayExpenses = expenseManager.expenses.filter(exp => 
                exp.date === dateStr
            );
            const dayTotal = dayExpenses.reduce((sum, exp) => sum + parseFloat(exp.amount), 0);
            dailyTotals.push(dayTotal);
        }

        if (trendsChart) {
            trendsChart.destroy();
        }

        trendsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: last7Days,
                datasets: [{
                    label: 'Daily Spending',
                    data: dailyTotals,
                    borderColor: '#a78bfa',
                    backgroundColor: 'rgba(167, 139, 250, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#a78bfa',
                    pointBorderColor: '#1a1625',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1a1625',
                        titleColor: '#e5dff7',
                        bodyColor: '#d1c7e8',
                        borderColor: '#a78bfa',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return `Spent: $${context.parsed.y.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(167, 139, 250, 0.1)'
                        },
                        ticks: {
                            color: '#d1c7e8',
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(167, 139, 250, 0.1)'
                        },
                        ticks: {
                            color: '#d1c7e8'
                        }
                    }
                }
            }
        });
    }

    // ==========================================
    // MODAL CONTROLS
    // ==========================================
    const expenseModal = document.getElementById('expenseModal');
    const budgetModal = document.getElementById('budgetModal');
    const createBtn = document.getElementById('createBtn');
    const closeExpenseModal = document.getElementById('closeExpenseModal');
    const cancelExpense = document.getElementById('cancelExpense');
    const editBudget = document.getElementById('editBudget');
    const closeBudgetModal = document.getElementById('closeBudgetModal');
    const cancelBudget = document.getElementById('cancelBudget');

    // Open expense modal
    if (createBtn) {
        createBtn.addEventListener('click', () => {
            expenseModal.classList.add('active');
            // Set today's date as default
            document.getElementById('expenseDate').valueAsDate = new Date();
        });
    }

    // Close expense modal
    [closeExpenseModal, cancelExpense].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => {
                expenseModal.classList.remove('active');
                document.getElementById('expenseForm').reset();
            });
        }
    });

    // Open budget modal
    if (editBudget) {
        editBudget.addEventListener('click', () => {
            budgetModal.classList.add('active');
            // Populate current budget values
            document.getElementById('totalBudget').value = expenseManager.budget.total;
            Object.keys(CATEGORIES).forEach(cat => {
                const input = document.getElementById(`budget${cat.charAt(0).toUpperCase() + cat.slice(1)}`);
                if (input) {
                    input.value = expenseManager.budget[cat] || '';
                }
            });
        });
    }

    // Close budget modal
    [closeBudgetModal, cancelBudget].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => {
                budgetModal.classList.remove('active');
                document.getElementById('budgetForm').reset();
            });
        }
    });

    // Close modals on outside click
    [expenseModal, budgetModal].forEach(modal => {
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        }
    });

    // ==========================================
    // FORM SUBMISSIONS
    // ==========================================
    const expenseForm = document.getElementById('expenseForm');
    if (expenseForm) {
        expenseForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(expenseForm);
            const expense = {
                amount: formData.get('amount'),
                category: formData.get('category'),
                description: formData.get('description'),
                date: formData.get('date')
            };

            expenseManager.addExpense(expense);
            
            // Update UI
            updateSummaryCards();
            renderExpensesList();
            createExpenseChart(currentPeriod);
            createTrendsChart();
            renderBudgetCategories();
            
            // Close modal and reset form
            expenseModal.classList.remove('active');
            expenseForm.reset();
            
            // Show success notification
            showNotification('Expense added successfully!', 'success');
        });
    }

    const budgetForm = document.getElementById('budgetForm');
    if (budgetForm) {
        budgetForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(budgetForm);
            const budgetData = {
                total: parseFloat(formData.get('totalBudget')) || 0
            };

            // Get category budgets
            Object.keys(CATEGORIES).forEach(cat => {
                const catName = cat.charAt(0).toUpperCase() + cat.slice(1);
                const value = formData.get(`budget${catName}`);
                budgetData[cat] = value ? parseFloat(value) : 0;
            });

            expenseManager.updateBudget(budgetData);
            
            // Update UI
            updateSummaryCards();
            renderBudgetCategories();
            
            // Close modal and reset form
            budgetModal.classList.remove('active');
            budgetForm.reset();
            
            // Show success notification
            showNotification('Budget updated successfully!', 'success');
        });
    }

    // ==========================================
    // TIME FILTER BUTTONS
    // ==========================================
    let currentPeriod = 'week';
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentPeriod = btn.dataset.period;
            createExpenseChart(currentPeriod);
        });
    });

    // ==========================================
    // DELETE EXPENSE
    // ==========================================
    window.deleteExpense = function(id) {
        if (confirm('Are you sure you want to delete this expense?')) {
            expenseManager.deleteExpense(id);
            updateSummaryCards();
            renderExpensesList();
            createExpenseChart(currentPeriod);
            createTrendsChart();
            renderBudgetCategories();
            showNotification('Expense deleted', 'success');
        }
    };

    // ==========================================
    // NOTIFICATIONS
    // ==========================================
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <svg class="notification-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>${message}</span>
            </div>
        `;

        // Add to page
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => notification.classList.add('show'), 10);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Add notification styles dynamically
    const notificationStyles = document.createElement('style');
    notificationStyles.textContent = `
        .notification {
            position: fixed;
            top: 100px;
            right: 2rem;
            background: var(--dark-surface);
            border: 1px solid rgba(167, 139, 250, 0.3);
            border-radius: var(--radius-md);
            padding: 1rem 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            z-index: 10000;
            opacity: 0;
            transform: translateX(100px);
            transition: all 0.3s ease;
        }
        .notification.show {
            opacity: 1;
            transform: translateX(0);
        }
        .notification-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .notification-icon {
            width: 24px;
            height: 24px;
            color: #10b981;
        }
        .notification-success {
            border-left: 4px solid #10b981;
        }
    `;
    document.head.appendChild(notificationStyles);

    // ==========================================
    // INITIALIZE
    // ==========================================
    function init() {
        updateSummaryCards();
        renderExpensesList();
        createExpenseChart(currentPeriod);
        createTrendsChart();
        renderBudgetCategories();
    }

    // Run initialization
    init();

    // Log for debugging
    console.log('Finance tracker initialized');
});