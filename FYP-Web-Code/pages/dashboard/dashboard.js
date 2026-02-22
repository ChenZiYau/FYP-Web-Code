document.addEventListener('DOMContentLoaded', function() {
    console.log("Dashboard.js has loaded successfully!");

    // --- 1. CONFIGURATION ---
    // API key moved to server-side proxy (calendar_proxy.php) for security.
    // No secrets are exposed to the client.

    // --- 2. SIDEBAR & MODAL LOGIC ---
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileToggle = document.getElementById('mobileSidebarToggle');
    const modal = document.getElementById('createModal');
    const createBtn = document.getElementById('createBtn');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');

    if (sidebarToggle) sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('collapsed'));
    if (mobileToggle) mobileToggle.addEventListener('click', () => sidebar.classList.toggle('mobile-active'));
    if (closeModal) closeModal.addEventListener('click', () => modal.classList.remove('active'));
    if (cancelBtn) cancelBtn.addEventListener('click', () => modal.classList.remove('active'));

    // --- DATE CHIP QUICK-PICK LOGIC ---
    const dateInput = document.getElementById('itemDate');
    const dateChips = document.querySelectorAll('.date-chip');

    function getISODate(date) {
        return date.toISOString().split('T')[0];
    }

    function setDateFromChip(chip) {
        dateChips.forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        const val = chip.dataset.date;
        if (val === 'today') {
            dateInput.value = getISODate(new Date());
            dateInput.classList.remove('visible');
        } else if (val === 'tomorrow') {
            const d = new Date();
            d.setDate(d.getDate() + 1);
            dateInput.value = getISODate(d);
            dateInput.classList.remove('visible');
        } else {
            dateInput.classList.add('visible');
            dateInput.focus();
        }
    }

    dateChips.forEach(chip => {
        chip.addEventListener('click', () => setDateFromChip(chip));
    });

    // --- TAB SWITCHING LOGIC ---
    const modalTabs = document.querySelectorAll('.modal-tab');
    const itemTypeInput = document.getElementById('itemType');
    const eventTimeGroup = document.getElementById('eventTimeGroup');
    const dateGroup = document.getElementById('dateGroup');
    const priorityGroup = document.getElementById('priorityGroup');
    const submitBtn = document.getElementById('createSubmitBtn');
    const descriptionField = document.getElementById('itemDescription');

    function switchTab(tabName) {
        modalTabs.forEach(t => t.classList.toggle('active', t.dataset.tab === tabName));
        itemTypeInput.value = tabName;

        // Show/hide fields based on type
        eventTimeGroup.style.display = tabName === 'event' ? '' : 'none';
        dateGroup.style.display = tabName === 'note' ? 'none' : '';
        priorityGroup.style.display = tabName === 'note' ? 'none' : '';

        // Update date required attribute
        dateInput.required = tabName !== 'note';

        // Update submit button text
        const labels = { task: 'Create Task', event: 'Create Event', note: 'Save Note' };
        submitBtn.textContent = labels[tabName];

        // Update placeholder
        const placeholders = { task: 'Add details...', event: 'Event details...', note: 'Write your note...' };
        descriptionField.placeholder = placeholders[tabName];
    }

    modalTabs.forEach(tab => {
        tab.addEventListener('click', () => switchTab(tab.dataset.tab));
    });

    // Open modal with defaults
    if (createBtn) {
        createBtn.addEventListener('click', () => {
            if (modal) modal.classList.add('active');
            switchTab('task');
            dateInput.value = getISODate(new Date());
            dateChips.forEach(c => c.classList.remove('active'));
            const todayChip = document.querySelector('.date-chip[data-date="today"]');
            if (todayChip) todayChip.classList.add('active');
            dateInput.classList.remove('visible');
        });
    }

// --- PRIORITY SLIDER LOGIC ---
    const prioritySlider = document.getElementById('prioritySlider');
    const priorityText = document.getElementById('priorityText');
    const realPriorityInput = document.getElementById('realPriorityInput');

    const priorityMap = {
        '1': { label: 'Low',    color: '#10b981', textClass: 'priority-low',    cssClass: 'low' },
        '2': { label: 'Medium', color: '#f59e0b', textClass: 'priority-medium', cssClass: 'medium' },
        '3': { label: 'High',   color: '#ef4444', textClass: 'priority-high',   cssClass: 'high' }
    };

    function updatePriority(value) {
            const priority = priorityMap[value];
            if (!priority) return;

            // 1. Update Text
            if (priorityText) {
                priorityText.textContent = priority.label;
                priorityText.className = `priority-display ${priority.textClass}`;
            }

            // 2. Update Hidden Input
            if (realPriorityInput) {
                realPriorityInput.value = priority.label.toLowerCase();
            }

            // 3. Update Slider Classes (for thumb color)
            prioritySlider.className = `priority-range ${priority.cssClass}`;

            // 4. Update Slider Background Gradient (The Fill Effect)
            // 1=0%, 2=50%, 3=100%
            const percentage = ((Number(value) - 1) / 2) * 100;
            
            // Paint: Color on the left, Dark Gray on the right
            prioritySlider.style.background = `linear-gradient(to right, ${priority.color} 0%, ${priority.color} ${percentage}%, #374151 ${percentage}%, #374151 100%)`;
        }

        // Event Listeners
        prioritySlider.addEventListener('input', function() { updatePriority(this.value); });
        
        // Initialize
        updatePriority(prioritySlider.value);

    // --- 3. CREATE TASK FORM HANDLING ---
    const createForm = document.getElementById('createForm');
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(createForm);
            // Include CSRF token for security
            if (window.CSRF_TOKEN) formData.append('csrf_token', window.CSRF_TOKEN);

            fetch('../../api/save_task.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    modal.classList.remove('active');
                    createForm.reset();
                    // Reset slider back to medium after form reset
                    if (prioritySlider) {
                        prioritySlider.value = 2;
                        updatePriority('2');
                    }
                    // Reset tab to task
                    switchTab('task');
                    window.location.reload();
                } else {
                    alert('Error saving task: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // --- 4. CALENDAR LOGIC ---
    const calendarGrid = document.getElementById('calendarGrid');
    const calendarMonth = document.getElementById('calendarMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    
    if (calendarGrid) {
        let currentDate = new Date(); 
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        const dayNames = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

        async function fetchAllEvents() {
            if(calendarMonth) calendarMonth.textContent = `${monthNames[currentMonth]} ${currentYear}`;
            
            const timeMin = new Date(currentYear, currentMonth, 1).toISOString();
            const timeMax = new Date(currentYear, currentMonth + 1, 0).toISOString();
            // Google Calendar requests go through server-side proxy (API key stays on server)
            const googleUrl = `../../api/calendar_proxy.php?timeMin=${encodeURIComponent(timeMin)}&timeMax=${encodeURIComponent(timeMax)}`;

            const localUrl = `../../api/get_events.php?month=${currentMonth + 1}&year=${currentYear}`;

            try {
                const [googleRes, localRes] = await Promise.allSettled([fetch(googleUrl), fetch(localUrl)]);
                let combinedEvents = [];

                if (googleRes.status === 'fulfilled' && googleRes.value.ok) {
                    const googleData = await googleRes.value.json();
                    if (googleData.items) combinedEvents = combinedEvents.concat(googleData.items.map(item => ({
                        title: item.summary,
                        date: item.start.date || (item.start.dateTime ? item.start.dateTime.split('T')[0] : ''),
                        type: 'event',
                        source: 'google'
                    })));
                }

                if (localRes.status === 'fulfilled' && localRes.value.ok) {
                    try {
                        const localData = await localRes.value.json();
                        if (Array.isArray(localData)) combinedEvents = combinedEvents.concat(localData.map(item => ({ ...item, source: 'local', type: item.type || 'task' })));
                    } catch(e) {}
                }
                renderCalendar(currentMonth, currentYear, combinedEvents);
            } catch (error) { renderCalendar(currentMonth, currentYear, []); }
        }

        function renderCalendar(month, year, eventsList) {
            calendarGrid.innerHTML = '';
            dayNames.forEach(day => {
                const el = document.createElement('div');
                el.className = 'calendar-day-header';
                el.textContent = day;
                calendarGrid.appendChild(el);
            });
            
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();
            
            for (let i = firstDay - 1; i >= 0; i--) calendarGrid.appendChild(createDayCell(daysInPrevMonth - i, true, []));
            
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                calendarGrid.appendChild(createDayCell(day, false, eventsList.filter(e => e.date === dateStr)));
            }
        }

        function createDayCell(day, isOtherMonth, dayEvents) {
            const el = document.createElement('div');
            el.className = 'calendar-day';
            if (isOtherMonth) el.classList.add('other-month');

            // Accessibility: make focusable for keyboard users
            if (!isOtherMonth) {
                el.setAttribute('tabindex', '0');
                el.setAttribute('role', 'button');
            }

            const dayNum = document.createElement('span');
            dayNum.className = 'calendar-day-number';
            dayNum.textContent = day;
            el.appendChild(dayNum);

            const today = new Date();
            const isToday = !isOtherMonth && day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear();
            if (isToday) el.classList.add('today');

            // Build aria-label for screen readers
            const dateObj = new Date(currentYear, currentMonth, day);
            const dateLabel = dateObj.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });

            if (dayEvents.length > 0) {
                el.classList.add('has-event');
                if (dayEvents.some(e => e.source === 'google')) el.classList.add('google-event');
                if (dayEvents.some(e => e.source === 'local')) el.classList.add('local-event');

                // Screen reader summary
                const taskSummary = dayEvents.map(ev => {
                    let s = ev.title;
                    if (ev.priority) s += ', ' + ev.priority + ' priority';
                    return s;
                }).join('. ');
                el.setAttribute('aria-label', `${dateLabel}, ${dayEvents.length} item${dayEvents.length > 1 ? 's' : ''}: ${taskSummary}`);

                // Build hover tooltip
                const tooltip = document.createElement('div');
                tooltip.className = 'calendar-tooltip';
                tooltip.setAttribute('role', 'tooltip');
                tooltip.setAttribute('aria-hidden', 'true');

                const tooltipHeader = document.createElement('div');
                tooltipHeader.className = 'tooltip-header';
                tooltipHeader.textContent = dateObj.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                tooltip.appendChild(tooltipHeader);

                const tooltipCount = document.createElement('div');
                tooltipCount.className = 'tooltip-count';
                tooltipCount.textContent = dayEvents.length + ' item' + (dayEvents.length > 1 ? 's' : '');
                tooltip.appendChild(tooltipCount);

                const tooltipList = document.createElement('div');
                tooltipList.className = 'tooltip-list';

                const typeIcons = { task: '📋', event: '📅', note: '📝' };
                const typeLabels = { task: 'Task', event: 'Event', note: 'Note' };

                dayEvents.forEach(ev => {
                    const item = document.createElement('div');
                    item.className = 'tooltip-item';

                    // Row 1: Type
                    const typeBadge = document.createElement('div');
                    typeBadge.className = 'tooltip-type-badge';
                    typeBadge.textContent = (typeIcons[ev.type] || '📋') + ' ' + (typeLabels[ev.type] || 'Task');
                    item.appendChild(typeBadge);

                    // Row 2: Title
                    const title = document.createElement('div');
                    title.className = 'tooltip-title';
                    title.textContent = ev.title;
                    item.appendChild(title);

                    // Row 3: Priority
                    if (ev.priority) {
                        const badge = document.createElement('div');
                        badge.className = 'tooltip-priority-badge priority-' + ev.priority;
                        badge.textContent = ev.priority.charAt(0).toUpperCase() + ev.priority.slice(1);
                        item.appendChild(badge);
                    }

                    // Row 4: Time
                    if (ev.event_time_start) {
                        const formatTime = t => {
                            const [h, m] = t.split(':');
                            const hr = parseInt(h);
                            return `${hr > 12 ? hr - 12 : hr || 12}:${m}${hr >= 12 ? 'pm' : 'am'}`;
                        };
                        let timeStr = formatTime(ev.event_time_start);
                        if (ev.event_time_end) timeStr += ' – ' + formatTime(ev.event_time_end);
                        const timeDiv = document.createElement('div');
                        timeDiv.className = 'tooltip-time';
                        timeDiv.textContent = '🕐 ' + timeStr;
                        item.appendChild(timeDiv);
                    }

                    // Row 5: Description
                    if (ev.description) {
                        const desc = document.createElement('div');
                        desc.className = 'tooltip-desc';
                        desc.textContent = ev.description.length > 80 ? ev.description.substring(0, 80) + '…' : ev.description;
                        item.appendChild(desc);
                    }

                    tooltipList.appendChild(item);
                });

                tooltip.appendChild(tooltipList);

                if (dayEvents.length > 3) {
                    const more = document.createElement('div');
                    more.className = 'tooltip-more';
                    more.textContent = `${dayEvents.length} items total`;
                    tooltip.appendChild(more);
                }

                el.appendChild(tooltip);

                // Click to pin tooltip open
                el.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const wasPinned = el.classList.contains('pinned');
                    // Unpin all other days first
                    document.querySelectorAll('.calendar-day.pinned').forEach(d => d.classList.remove('pinned'));
                    if (!wasPinned) {
                        el.classList.add('pinned');
                    }
                });
            } else {
                el.setAttribute('aria-label', isToday ? dateLabel + ', today' : dateLabel);
            }
            return el;
        }

        if (prevMonthBtn) prevMonthBtn.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) { currentMonth = 11; currentYear--; }
            fetchAllEvents();
        });
        
        if (nextMonthBtn) nextMonthBtn.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) { currentMonth = 0; currentYear++; }
            fetchAllEvents();
        });

        fetchAllEvents();

        // Click outside to dismiss pinned tooltip
        document.addEventListener('click', function() {
            document.querySelectorAll('.calendar-day.pinned').forEach(d => d.classList.remove('pinned'));
        });
    }

    // --- 5. FINANCE SECTION LOGIC ---
    (function() {
        const EXPENSES_KEY  = 'optiplan_expenses';
        const BUDGET_KEY    = 'optiplan_budget';
        const SETTINGS_KEY  = 'optiplan_finance_settings';
        const CAT_ICONS     = {
            food: '🍔', transport: '🚗', shopping: '🛍️',
            entertainment: '🎮', education: '📚', health: '⚕️',
            bills: '💳', other: '📦'
        };

        function loadExpenses() {
            const s = localStorage.getItem(EXPENSES_KEY);
            return s ? JSON.parse(s) : [];
        }

        function loadBudget() {
            const s = localStorage.getItem(BUDGET_KEY);
            return s ? JSON.parse(s) : { total: 0 };
        }

        function loadFinanceSettings() {
            const s = localStorage.getItem(SETTINGS_KEY);
            return s ? JSON.parse(s) : { startingBalance: 0, monthlyIncome: 0, sideIncome: 0 };
        }

        function syncBudgetTotal(settings) {
            const total  = settings.startingBalance + settings.monthlyIncome + settings.sideIncome;
            const budget = loadBudget();
            budget.total = total > 0 ? total : budget.total;
            localStorage.setItem(BUDGET_KEY, JSON.stringify(budget));
        }

        function getPeriodTotal(expenses, period) {
            const now = new Date();
            const start = period === 'today'
                ? new Date(now.getFullYear(), now.getMonth(), now.getDate())
                : new Date(now.getFullYear(), now.getMonth(), 1);
            return expenses
                .filter(e => new Date(e.date) >= start)
                .reduce((sum, e) => sum + parseFloat(e.amount), 0);
        }

        function updateFinanceModalSummary() {
            const sbEl = document.getElementById('fsStartingBalance');
            const miEl = document.getElementById('fsMonthlyIncome');
            const siEl = document.getElementById('fsSideIncome');
            if (!sbEl || !miEl || !siEl) return;

            const sb = parseFloat(sbEl.value) || 0;
            const mi = parseFloat(miEl.value) || 0;
            const si = parseFloat(siEl.value) || 0;
            const total      = sb + mi + si;
            const monthSpent = getPeriodTotal(loadExpenses(), 'month');
            const remaining  = total - monthSpent;

            const totalEl  = document.getElementById('fmsTotal');
            const remainEl = document.getElementById('fmsRemaining');
            if (totalEl)  totalEl.textContent  = `$${total.toFixed(2)}`;
            if (remainEl) {
                remainEl.textContent = `$${remaining.toFixed(2)}`;
                remainEl.style.color = remaining < 0
                    ? '#ef4444'
                    : (total > 0 && remaining < total * 0.2) ? '#f59e0b' : '#10b981';
            }
        }

        function updateFinanceStats() {
            const expenses = loadExpenses();
            const budget   = loadBudget();
            const month    = getPeriodTotal(expenses, 'month');
            const today    = getPeriodTotal(expenses, 'today');
            const remaining = budget.total - month;

            const remEl = document.getElementById('dashBudgetRemaining');
            if (remEl) {
                remEl.textContent = `$${remaining.toFixed(2)}`;
                remEl.style.color = remaining < 0
                    ? '#ef4444'
                    : remaining < budget.total * 0.2 ? '#f59e0b' : '#10b981';
            }
            const mEl = document.getElementById('dashMonthTotal');
            if (mEl) mEl.textContent = `$${month.toFixed(2)}`;
            const tEl = document.getElementById('dashTodayTotal');
            if (tEl) tEl.textContent = `$${today.toFixed(2)}`;
        }

        function renderRecentExpenses() {
            const container = document.getElementById('dashRecentExpenses');
            if (!container) return;
            const recent = loadExpenses().slice(0, 3);
            if (recent.length === 0) {
                container.innerHTML = '<p class="dash-no-expenses">No expenses yet.</p>';
                return;
            }
            container.innerHTML = recent.map(exp => {
                const icon = CAT_ICONS[exp.category] || '📦';
                const dateStr = new Date(exp.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                return `
                    <div class="dash-expense-item">
                        <span class="dash-exp-icon">${icon}</span>
                        <div class="dash-exp-details">
                            <span class="dash-exp-desc">${exp.description || exp.category}</span>
                            <span class="dash-exp-date">${dateStr}</span>
                        </div>
                        <span class="dash-exp-amount">$${parseFloat(exp.amount).toFixed(2)}</span>
                    </div>`;
            }).join('');
        }

        // --- Finance Settings Modal ---
        const dashManageBtn         = document.getElementById('dashAddExpenseBtn');
        const financeSettingsModal  = document.getElementById('financeSettingsModal');
        const closeFinanceSettings  = document.getElementById('closeFinanceSettings');
        const cancelFinanceSettings = document.getElementById('cancelFinanceSettings');
        const financeSettingsForm   = document.getElementById('financeSettingsForm');

        function openFinanceSettingsModal() {
            if (!financeSettingsModal) return;
            const s = loadFinanceSettings();
            const sb = document.getElementById('fsStartingBalance');
            const mi = document.getElementById('fsMonthlyIncome');
            const si = document.getElementById('fsSideIncome');
            if (sb) sb.value = s.startingBalance || '';
            if (mi) mi.value = s.monthlyIncome   || '';
            if (si) si.value = s.sideIncome       || '';
            financeSettingsModal.classList.add('active');
            updateFinanceModalSummary();
        }

        function closeFinanceSettingsModal() {
            if (!financeSettingsModal) return;
            financeSettingsModal.classList.remove('active');
            if (financeSettingsForm) financeSettingsForm.reset();
        }

        if (dashManageBtn)         dashManageBtn.addEventListener('click', openFinanceSettingsModal);
        if (closeFinanceSettings)  closeFinanceSettings.addEventListener('click', closeFinanceSettingsModal);
        if (cancelFinanceSettings) cancelFinanceSettings.addEventListener('click', closeFinanceSettingsModal);

        // Live calculation: update summary as user types
        ['fsStartingBalance', 'fsMonthlyIncome', 'fsSideIncome'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('input', updateFinanceModalSummary);
        });

        if (financeSettingsModal) {
            financeSettingsModal.addEventListener('click', e => {
                if (e.target === financeSettingsModal) closeFinanceSettingsModal();
            });
        }

        if (financeSettingsForm) {
            financeSettingsForm.addEventListener('submit', e => {
                e.preventDefault();
                const settings = {
                    startingBalance: parseFloat(document.getElementById('fsStartingBalance').value) || 0,
                    monthlyIncome:   parseFloat(document.getElementById('fsMonthlyIncome').value)   || 0,
                    sideIncome:      parseFloat(document.getElementById('fsSideIncome').value)       || 0
                };
                localStorage.setItem(SETTINGS_KEY, JSON.stringify(settings));
                syncBudgetTotal(settings);
                updateFinanceStats();
                closeFinanceSettingsModal();

                // Sync budget total to server so finance page can read it
                const totalBudget = settings.startingBalance + settings.monthlyIncome + settings.sideIncome;
                fetch('../finance/finance_api.php?action=get_budget')
                    .then(r => r.json())
                    .then(data => {
                        const existing = (data.success && data.budget) ? data.budget : {};
                        const budgetPayload = new FormData();
                        budgetPayload.append('action', 'save_budget');
                        budgetPayload.append('total', totalBudget);
                        budgetPayload.append('food', existing.food_budget || 0);
                        budgetPayload.append('transport', existing.transport_budget || 0);
                        budgetPayload.append('shopping', existing.shopping_budget || 0);
                        budgetPayload.append('entertainment', existing.entertainment_budget || 0);
                        budgetPayload.append('education', existing.education_budget || 0);
                        budgetPayload.append('health', existing.health_budget || 0);
                        budgetPayload.append('bills', existing.bills_budget || 0);
                        budgetPayload.append('other', existing.other_budget || 0);
                        if (window.CSRF_TOKEN) budgetPayload.append('csrf_token', window.CSRF_TOKEN);
                        return fetch('../finance/finance_api.php', { method: 'POST', body: budgetPayload });
                    })
                    .catch(err => console.error('Budget sync failed:', err));
            });
        }

        // Init
        updateFinanceStats();
        renderRecentExpenses();
    })();
});

// --- 5. COMPLETE TASK LOGIC (Global) ---
window.completeTask = function(taskId) {
    fetch('../../api/update_task.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: taskId, status: 'completed', csrf_token: window.CSRF_TOKEN || '' })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const checkbox = document.getElementById('task_' + taskId);
            if(checkbox) checkbox.closest('.task-item').style.opacity = '0.5';
            setTimeout(() => window.location.reload(), 300);
        }
    })
    .catch(error => console.error('Error:', error));
};