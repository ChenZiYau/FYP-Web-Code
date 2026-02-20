/* ==========================================
   OPTIPLAN FINANCE TRACKER - JAVASCRIPT
   (Database-backed via finance_api.php)
   ========================================== */

document.addEventListener('DOMContentLoaded', function () {

    const API_URL = '../php/finance_api.php';

    // ==========================================
    // EXPENSE DATA MANAGEMENT
    // ==========================================
    class ExpenseManager {
        constructor() {
            this.expenses = [];
            this.budget = {
                total: 0, food: 0, transport: 0, shopping: 0,
                entertainment: 0, education: 0, health: 0, bills: 0, other: 0
            };
        }

        async loadExpenses() {
            try {
                const res = await fetch(API_URL + '?action=get_expenses');
                const data = await res.json();
                if (data.success) {
                    this.expenses = data.expenses.map(e => ({
                        id: parseInt(e.id),
                        amount: e.amount,
                        category: e.category,
                        description: e.description,
                        date: e.expense_date,
                        timestamp: e.created_at
                    }));
                }
            } catch (err) {
                console.error('Failed to load expenses:', err);
            }
        }

        async loadBudget() {
            try {
                const res = await fetch(API_URL + '?action=get_budget');
                const data = await res.json();
                if (data.success && data.budget) {
                    const b = data.budget;
                    this.budget = {
                        total: parseFloat(b.total_budget) || 0,
                        food: parseFloat(b.food_budget) || 0,
                        transport: parseFloat(b.transport_budget) || 0,
                        shopping: parseFloat(b.shopping_budget) || 0,
                        entertainment: parseFloat(b.entertainment_budget) || 0,
                        education: parseFloat(b.education_budget) || 0,
                        health: parseFloat(b.health_budget) || 0,
                        bills: parseFloat(b.bills_budget) || 0,
                        other: parseFloat(b.other_budget) || 0
                    };
                }
            } catch (err) {
                console.error('Failed to load budget:', err);
            }
        }

        async addExpense(expense) {
            try {
                const formData = new FormData();
                formData.append('action', 'add_expense');
                formData.append('amount', expense.amount);
                formData.append('category', expense.category);
                formData.append('description', expense.description || '');
                formData.append('date', expense.date);

                const res = await fetch(API_URL, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    await this.loadExpenses();
                    return true;
                }
                return false;
            } catch (err) {
                console.error('Failed to add expense:', err);
                return false;
            }
        }

        async deleteExpense(id) {
            try {
                const formData = new FormData();
                formData.append('action', 'delete_expense');
                formData.append('id', id);

                const res = await fetch(API_URL, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    await this.loadExpenses();
                    return true;
                }
                return false;
            } catch (err) {
                console.error('Failed to delete expense:', err);
                return false;
            }
        }

        async updateBudget(budgetData) {
            try {
                this.budget = { ...this.budget, ...budgetData };
                const formData = new FormData();
                formData.append('action', 'save_budget');
                formData.append('total', this.budget.total);
                formData.append('food', this.budget.food);
                formData.append('transport', this.budget.transport);
                formData.append('shopping', this.budget.shopping);
                formData.append('entertainment', this.budget.entertainment);
                formData.append('education', this.budget.education);
                formData.append('health', this.budget.health);
                formData.append('bills', this.budget.bills);
                formData.append('other', this.budget.other);

                const res = await fetch(API_URL, { method: 'POST', body: formData });
                const data = await res.json();
                return data.success;
            } catch (err) {
                console.error('Failed to save budget:', err);
                return false;
            }
        }

        getExpensesByPeriod(period) {
            const now = new Date();
            let startDate;

            switch (period) {
                case 'today':
                    startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    break;
                case 'week':
                    startDate = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
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
                if (!totals[exp.category]) totals[exp.category] = 0;
                totals[exp.category] += parseFloat(exp.amount);
            });
            return totals;
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
        const todayTotal = expenseManager.getTotalByPeriod('today');
        document.getElementById('todayTotal').textContent = `$${todayTotal.toFixed(2)}`;

        const weekTotal = expenseManager.getTotalByPeriod('week');
        document.getElementById('weekTotal').textContent = `$${weekTotal.toFixed(2)}`;

        const monthTotal = expenseManager.getTotalByPeriod('month');
        document.getElementById('monthTotal').textContent = `$${monthTotal.toFixed(2)}`;

        const budgetRemaining = expenseManager.budget.total - monthTotal;
        const remainingEl = document.getElementById('budgetRemaining');
        remainingEl.textContent = `$${budgetRemaining.toFixed(2)}`;

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

            if (limit === 0) return '';

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

        if (expenseChart) expenseChart.destroy();

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
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1625',
                        titleColor: '#e5dff7',
                        bodyColor: '#d1c7e8',
                        borderColor: '#a78bfa',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function (context) {
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

        updateChartLegend(labels, data, colors);
    }

    function updateChartLegend(labels, data, colors) {
        const legendContainer = document.getElementById('chartLegend');
        if (!legendContainer) return;

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

        const last7Days = [];
        const dailyTotals = [];
        const now = new Date();

        for (let i = 6; i >= 0; i--) {
            const date = new Date(now);
            date.setDate(date.getDate() - i);
            const dateStr = date.toISOString().split('T')[0];

            last7Days.push(date.toLocaleDateString('en-US', { weekday: 'short' }));

            const dayExpenses = expenseManager.expenses.filter(exp => exp.date === dateStr);
            const dayTotal = dayExpenses.reduce((sum, exp) => sum + parseFloat(exp.amount), 0);
            dailyTotals.push(dayTotal);
        }

        if (trendsChart) trendsChart.destroy();

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
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1625',
                        titleColor: '#e5dff7',
                        bodyColor: '#d1c7e8',
                        borderColor: '#a78bfa',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function (context) {
                                return `Spent: $${context.parsed.y.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(167, 139, 250, 0.1)' },
                        ticks: { color: '#d1c7e8', callback: function (value) { return '$' + value; } }
                    },
                    x: {
                        grid: { color: 'rgba(167, 139, 250, 0.1)' },
                        ticks: { color: '#d1c7e8' }
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

    if (createBtn) {
        createBtn.addEventListener('click', () => {
            expenseModal.classList.add('active');
            document.getElementById('expenseDate').valueAsDate = new Date();
        });
    }

    [closeExpenseModal, cancelExpense].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => {
                expenseModal.classList.remove('active');
                document.getElementById('expenseForm').reset();
            });
        }
    });

    if (editBudget) {
        editBudget.addEventListener('click', () => {
            budgetModal.classList.add('active');
            document.getElementById('totalBudget').value = expenseManager.budget.total;
            Object.keys(CATEGORIES).forEach(cat => {
                const input = document.getElementById(`budget${cat.charAt(0).toUpperCase() + cat.slice(1)}`);
                if (input) input.value = expenseManager.budget[cat] || '';
            });
        });
    }

    [closeBudgetModal, cancelBudget].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => {
                budgetModal.classList.remove('active');
                document.getElementById('budgetForm').reset();
            });
        }
    });

    [expenseModal, budgetModal].forEach(modal => {
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.classList.remove('active');
            });
        }
    });

    // ==========================================
    // FORM SUBMISSIONS
    // ==========================================
    const expenseForm = document.getElementById('expenseForm');
    if (expenseForm) {
        expenseForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(expenseForm);
            const expense = {
                amount: formData.get('amount'),
                category: formData.get('category'),
                description: formData.get('description'),
                date: formData.get('date')
            };

            const success = await expenseManager.addExpense(expense);

            if (success) {
                updateSummaryCards();
                renderExpensesList();
                createExpenseChart(currentPeriod);
                createTrendsChart();
                renderBudgetCategories();

                expenseModal.classList.remove('active');
                expenseForm.reset();
                showNotification('Expense added successfully!', 'success');
            } else {
                showNotification('Failed to add expense', 'error');
            }
        });
    }

    const budgetForm = document.getElementById('budgetForm');
    if (budgetForm) {
        budgetForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(budgetForm);
            const budgetData = {
                total: parseFloat(formData.get('totalBudget')) || 0
            };

            Object.keys(CATEGORIES).forEach(cat => {
                const catName = cat.charAt(0).toUpperCase() + cat.slice(1);
                const value = formData.get(`budget${catName}`);
                budgetData[cat] = value ? parseFloat(value) : 0;
            });

            const success = await expenseManager.updateBudget(budgetData);

            if (success) {
                updateSummaryCards();
                renderBudgetCategories();
                budgetModal.classList.remove('active');
                budgetForm.reset();
                showNotification('Budget updated successfully!', 'success');
            } else {
                showNotification('Failed to save budget', 'error');
            }
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
    window.deleteExpense = async function (id) {
        if (confirm('Are you sure you want to delete this expense?')) {
            const success = await expenseManager.deleteExpense(id);
            if (success) {
                updateSummaryCards();
                renderExpensesList();
                createExpenseChart(currentPeriod);
                createTrendsChart();
                renderBudgetCategories();
                showNotification('Expense deleted', 'success');
            }
        }
    };

    // ==========================================
    // NOTIFICATIONS
    // ==========================================
    function showNotification(message, type = 'info') {
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
        document.body.appendChild(notification);
        setTimeout(() => notification.classList.add('show'), 10);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    const notificationStyles = document.createElement('style');
    notificationStyles.textContent = `
        .notification {
            position: fixed; top: 100px; right: 2rem;
            background: var(--dark-surface);
            border: 1px solid rgba(167, 139, 250, 0.3);
            border-radius: var(--radius-md);
            padding: 1rem 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            z-index: 10000; opacity: 0;
            transform: translateX(100px);
            transition: all 0.3s ease;
        }
        .notification.show { opacity: 1; transform: translateX(0); }
        .notification-content { display: flex; align-items: center; gap: 0.75rem; }
        .notification-icon { width: 24px; height: 24px; color: #10b981; }
        .notification-success { border-left: 4px solid #10b981; }
    `;
    document.head.appendChild(notificationStyles);

    // ==========================================
    // BUDGET REMAINING CARD — INLINE EDIT
    // ==========================================
    const budgetCardEditBtn = document.getElementById('budgetCardEditBtn');
    if (budgetCardEditBtn) {
        budgetCardEditBtn.addEventListener('click', function () {
            const summaryContent = document.getElementById('budgetCardContent');
            if (!summaryContent) return;

            const currentTotal = expenseManager.budget.total;
            const originalHTML = summaryContent.innerHTML;

            summaryContent.innerHTML = `
                <p class="summary-label">Set Total Budget ($)</p>
                <div class="budget-inline-edit">
                    <input type="number" id="budgetInlineInput" class="budget-inline-input"
                           value="${currentTotal}" step="0.01" min="0" placeholder="0.00">
                    <div class="budget-inline-actions">
                        <button id="budgetInlineSave" class="budget-inline-save">&#10003; Save</button>
                        <button id="budgetInlineCancel" class="budget-inline-cancel">&#10005;</button>
                    </div>
                </div>
            `;

            budgetCardEditBtn.style.opacity = '0';
            budgetCardEditBtn.style.pointerEvents = 'none';

            const input = document.getElementById('budgetInlineInput');
            input.focus();
            input.select();

            async function saveEdit() {
                const newTotal = parseFloat(input.value);
                restore();
                if (!isNaN(newTotal) && newTotal >= 0) {
                    await expenseManager.updateBudget({ total: newTotal });
                    updateSummaryCards();
                    renderBudgetCategories();
                    showNotification('Budget updated successfully!', 'success');
                }
            }

            function restore() {
                summaryContent.innerHTML = originalHTML;
                budgetCardEditBtn.style.opacity = '';
                budgetCardEditBtn.style.pointerEvents = '';
            }

            document.getElementById('budgetInlineSave').addEventListener('click', saveEdit);
            document.getElementById('budgetInlineCancel').addEventListener('click', restore);
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') saveEdit();
                if (e.key === 'Escape') restore();
            });
        });
    }

    // ==========================================
    // INITIALIZE
    // ==========================================
    async function init() {
        await Promise.all([
            expenseManager.loadExpenses(),
            expenseManager.loadBudget()
        ]);
        updateSummaryCards();
        renderExpensesList();
        createExpenseChart(currentPeriod);
        createTrendsChart();
        renderBudgetCategories();
    }

    init();
    console.log('Finance tracker initialized (database mode)');
});
