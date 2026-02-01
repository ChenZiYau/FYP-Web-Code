/* ==========================================
   OPTIPLAN DASHBOARD JAVASCRIPT
   ========================================== */

document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // SIDEBAR FUNCTIONALITY
    // ==========================================
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    
    // Desktop sidebar toggle
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }
    
    // Mobile sidebar toggle
    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-active');
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !mobileSidebarToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-active');
            }
        }
    });
    
    // Restore sidebar state from localStorage
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }
    
    // Active navigation item
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Don't add active class to create button or logout button
            if (!this.classList.contains('create-btn') && !this.classList.contains('logout-btn')) {
                navItems.forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });
    
    
    // ==========================================
    // CALENDAR FUNCTIONALITY
    // ==========================================
    const calendarGrid = document.getElementById('calendarGrid');
    const calendarMonth = document.getElementById('calendarMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    
    const dayNames = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
    
    // Sample events data (this would come from your database)
    const events = [
        { date: '2024-11-19', title: 'Math Exam' },
        { date: '2024-11-27', title: 'Group Project' },
        { date: '2024-11-20', title: 'Chemistry Lab' },
        { date: '2024-11-21', title: 'Essay Due' }
    ];
    
    function renderCalendar(month, year) {
        calendarGrid.innerHTML = '';
        
        // Update month display
        calendarMonth.textContent = `${monthNames[month]} ${year}`;
        
        // Render day headers
        dayNames.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'calendar-day-header';
            dayHeader.textContent = day;
            calendarGrid.appendChild(dayHeader);
        });
        
        // Get first day of month and number of days
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();
        
        // Render previous month's trailing days
        for (let i = firstDay - 1; i >= 0; i--) {
            const dayCell = createDayCell(daysInPrevMonth - i, month - 1, year, true);
            calendarGrid.appendChild(dayCell);
        }
        
        // Render current month's days
        for (let day = 1; day <= daysInMonth; day++) {
            const dayCell = createDayCell(day, month, year, false);
            calendarGrid.appendChild(dayCell);
        }
        
        // Render next month's leading days
        const totalCells = firstDay + daysInMonth;
        const remainingCells = 42 - totalCells; // 6 rows * 7 days
        for (let day = 1; day <= remainingCells; day++) {
            const dayCell = createDayCell(day, month + 1, year, true);
            calendarGrid.appendChild(dayCell);
        }
    }
    
    function createDayCell(day, month, year, isOtherMonth) {
        const dayCell = document.createElement('div');
        dayCell.className = 'calendar-day';
        dayCell.textContent = day;
        
        if (isOtherMonth) {
            dayCell.classList.add('other-month');
        }
        
        // Check if it's today
        const today = new Date();
        if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
            dayCell.classList.add('today');
        }
        
        // Check if day has events
        const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const hasEvent = events.some(event => event.date === dateString);
        if (hasEvent) {
            dayCell.classList.add('has-event');
        }
        
        // Add click handler
        dayCell.addEventListener('click', function() {
            document.querySelectorAll('.calendar-day').forEach(cell => {
                cell.classList.remove('selected');
            });
            this.classList.add('selected');
            
            // You can add functionality here to show events for the selected day
            console.log(`Selected date: ${dateString}`);
        });
        
        return dayCell;
    }
    
    // Calendar navigation
    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar(currentMonth, currentYear);
        });
    }
    
    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar(currentMonth, currentYear);
        });
    }
    
    // Initial calendar render
    renderCalendar(currentMonth, currentYear);
    
    
    // ==========================================
    // CREATE MODAL FUNCTIONALITY
    // ==========================================
    const createBtn = document.getElementById('createBtn');
    const createModal = document.getElementById('createModal');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const modalTabs = document.querySelectorAll('.modal-tab');
    const createForm = document.getElementById('createForm');
    
    // Open modal
    if (createBtn) {
        createBtn.addEventListener('click', function() {
            createModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close modal
    function closeModal() {
        createModal.classList.remove('active');
        document.body.style.overflow = '';
        createForm.reset();
    }
    
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }
    
    // Close modal when clicking outside
    createModal.addEventListener('click', function(e) {
        if (e.target === createModal) {
            closeModal();
        }
    });
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && createModal.classList.contains('active')) {
            closeModal();
        }
    });
    
    // Modal tabs
    modalTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            modalTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // You can add different form fields based on the selected tab
            const tabType = this.dataset.tab;
            console.log('Selected tab:', tabType);
        });
    });
    
    // Form submission
    createForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
            title: formData.get('title'),
            description: formData.get('description'),
            date: formData.get('date'),
            priority: formData.get('priority'),
            type: document.querySelector('.modal-tab.active').dataset.tab
        };
        
        console.log('Creating new item:', data);
        
        // Here you would send the data to your backend
        // For now, we'll just close the modal
        closeModal();
        
        // Show success message (you can implement a toast notification)
        showNotification('Item created successfully!', 'success');
    });
    
    
    // ==========================================
    // TASK CHECKBOX FUNCTIONALITY
    // ==========================================
    const taskCheckboxes = document.querySelectorAll('.task-checkbox input[type="checkbox"]');
    
    taskCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const taskItem = this.closest('.task-item');
            
            if (this.checked) {
                taskItem.style.opacity = '0.6';
                taskItem.style.textDecoration = 'line-through';
                
                // You would update the database here
                console.log('Task completed:', this.id);
                
                // Show success animation
                showNotification('Task completed! +10 XP', 'success');
            } else {
                taskItem.style.opacity = '1';
                taskItem.style.textDecoration = 'none';
            }
        });
    });
    
    
    // ==========================================
    // SETTINGS BUTTON
    // ==========================================
    const settingsBtn = document.getElementById('settingsBtn');
    
    if (settingsBtn) {
        settingsBtn.addEventListener('click', function() {
            // Navigate to settings page or open settings modal
            console.log('Opening settings...');
            // window.location.href = 'settings.php';
        });
    }
    
    
    // ==========================================
    // PROGRESS BAR ANIMATION
    // ==========================================
    const progressFill = document.querySelector('.progress-fill');
    
    if (progressFill) {
        // Animate progress bar on load
        const targetWidth = progressFill.style.width;
        progressFill.style.width = '0%';
        
        setTimeout(() => {
            progressFill.style.width = targetWidth;
        }, 500);
    }
    
    
    // ==========================================
    // NOTIFICATION SYSTEM
    // ==========================================
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: ${type === 'success' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #3b82f6, #2563eb)'};
            color: white;
            border-radius: 0.75rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            z-index: 3000;
            font-weight: 600;
            animation: slideInRight 0.3s ease;
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Add notification animations to document
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100px);
            }
        }
    `;
    document.head.appendChild(style);
    
    
    // ==========================================
    // SMOOTH SCROLL FOR NAVIGATION
    // ==========================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#top') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    
    // ==========================================
    // AUTO-UPDATE TIME-BASED ELEMENTS
    // ==========================================
    function updateTimeBasedElements() {
        // Update "Today's Events" count based on current date
        // This would typically fetch from your database
        console.log('Updating time-based elements...');
    }
    
    // Update every minute
    setInterval(updateTimeBasedElements, 60000);
    
    
    // ==========================================
    // KEYBOARD SHORTCUTS
    // ==========================================
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to open create modal
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            createBtn.click();
        }
        
        // Ctrl/Cmd + B to toggle sidebar
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            sidebarToggle.click();
        }
    });
    
    
    // ==========================================
    // RESPONSIVE BEHAVIOR
    // ==========================================
    function handleResize() {
        const width = window.innerWidth;
        
        // Close mobile sidebar when resizing to desktop
        if (width > 768) {
            sidebar.classList.remove('mobile-active');
        }
    }
    
    window.addEventListener('resize', debounce(handleResize, 250));
    
    
    // ==========================================
    // LOADING STATES
    // ==========================================
    function showLoading(element) {
        element.style.opacity = '0.6';
        element.style.pointerEvents = 'none';
    }
    
    function hideLoading(element) {
        element.style.opacity = '1';
        element.style.pointerEvents = 'auto';
    }
    
    
    // ==========================================
    // CONSOLE LOG - REMOVE IN PRODUCTION
    // ==========================================
    console.log('OptiPlan Dashboard - JavaScript Loaded Successfully');
    console.log('Current Date:', currentDate);
    console.log('User Level: 19'); // This would come from PHP
    
});


/* ==========================================
   UTILITY FUNCTIONS
   ========================================== */

/**
 * Debounce function to limit how often a function can run
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Format date to readable string
 */
function formatDate(date) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(date).toLocaleDateString('en-US', options);
}

/**
 * Calculate days until a date
 */
function daysUntil(targetDate) {
    const today = new Date();
    const target = new Date(targetDate);
    const diffTime = target - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

/**
 * Generate random color for avatar
 */
function getRandomColor() {
    const colors = [
        '#8b5cf6', '#a78bfa', '#e879f9', '#c4b5fd',
        '#10b981', '#3b82f6', '#f59e0b', '#ef4444'
    ];
    return colors[Math.floor(Math.random() * colors.length)];
}

/**
 * Get initials from name
 */
function getInitials(name) {
    return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}