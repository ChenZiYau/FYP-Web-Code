// ==========================================
// NAVIGATION & SECTION SWITCHING
// ==========================================

// Get all navigation items
const navItems = document.querySelectorAll('.nav-item');
const sections = {
    'user-database': document.getElementById('section-user-database'),
    'user-online': document.getElementById('section-user-online'),
    'feedback': document.getElementById('section-feedback')
};

// Add click event listeners to navigation items
navItems.forEach(item => {
    item.addEventListener('click', function() {
        const sectionName = this.getAttribute('data-section');
        switchSection(sectionName);
    });
});

// Function to switch between sections
function switchSection(sectionName) {
    // Remove active class from all nav items
    navItems.forEach(item => {
        item.classList.remove('active');
    });

    // Hide all sections
    Object.values(sections).forEach(section => {
        section.classList.add('hidden');
    });

    // Activate clicked nav item
    const activeNavItem = document.querySelector(`[data-section="${sectionName}"]`);
    if (activeNavItem) {
        activeNavItem.classList.add('active');
    }

    // Show selected section
    if (sections[sectionName]) {
        sections[sectionName].classList.remove('hidden');
    }

    // Update page title
    const titles = {
        'user-database': 'User Database',
        'user-online': 'Users Online',
        'feedback': 'User Feedback'
    };
    document.getElementById('pageTitle').textContent = titles[sectionName] || 'Dashboard';

    // Close mobile sidebar after selection
    if (window.innerWidth <= 1024) {
        closeSidebar();
    }
}

// ==========================================
// MOBILE SIDEBAR TOGGLE
// ==========================================

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('mobile-open');
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.remove('mobile-open');
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    
    if (window.innerWidth <= 1024 && 
        sidebar.classList.contains('mobile-open') &&
        !sidebar.contains(event.target) &&
        !mobileToggle.contains(event.target)) {
        closeSidebar();
    }
});

// ==========================================
// SEARCH FUNCTIONALITY
// ==========================================

const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        console.log('Searching for:', searchTerm);
        
        // Get current active section
        const activeSection = document.querySelector('.data-section:not(.hidden)');
        if (!activeSection) return;

        // Search in tables
        const tableRows = activeSection.querySelectorAll('.data-table tbody tr');
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Search in feedback cards
        const feedbackCards = activeSection.querySelectorAll('.feedback-card');
        feedbackCards.forEach(card => {
            const text = card.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// ==========================================
// USER MANAGEMENT FUNCTIONS
// ==========================================

function viewUser(userId) {
    console.log('Viewing user:', userId);
    alert(`Viewing details for user ID: ${userId}`);
    // TODO: Implement modal or redirect to user detail page
    // Example: window.location.href = `user-detail.php?id=${userId}`;
}

function editUser(userId) {
    console.log('Editing user:', userId);
    alert(`Edit user ID: ${userId}`);
    // TODO: Implement edit modal or redirect to edit page
    // Example: window.location.href = `edit-user.php?id=${userId}`;
}

function deleteUser(userId) {
    console.log('Deleting user:', userId);
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        // TODO: Implement actual delete functionality
        alert(`User ${userId} would be deleted (functionality not implemented)`);
        
        // Example AJAX call:
        // fetch(`delete-user.php?id=${userId}`, {
        //     method: 'DELETE'
        // })
        // .then(response => response.json())
        // .then(data => {
        //     if (data.success) {
        //         // Remove row from table or refresh page
        //         location.reload();
        //     }
        // });
    }
}

function addNewUser() {
    console.log('Adding new user');
    alert('Add new user functionality');
    // TODO: Implement add user modal or redirect to add user page
    // Example: window.location.href = 'add-user.php';
}

function sendMessage(userId) {
    console.log('Sending message to user:', userId);
    alert(`Send message to user ID: ${userId}`);
    // TODO: Implement messaging functionality
}

// ==========================================
// DATA EXPORT FUNCTIONS
// ==========================================

function exportData() {
    console.log('Exporting data');
    
    // Get current active section
    const activeNavItem = document.querySelector('.nav-item.active');
    const sectionName = activeNavItem ? activeNavItem.getAttribute('data-section') : 'user-database';
    
    alert(`Exporting ${sectionName} data to CSV...`);
    
    // TODO: Implement actual export functionality
    // Example:
    // window.location.href = `export.php?section=${sectionName}`;
}

// ==========================================
// ONLINE USERS FUNCTIONS
// ==========================================

function refreshOnlineUsers() {
    console.log('Refreshing online users');
    
    // Show loading state (optional)
    const refreshBtn = event.target.closest('.btn');
    const originalText = refreshBtn.innerHTML;
    refreshBtn.innerHTML = `
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Refreshing...
    `;
    
    // TODO: Implement actual refresh functionality
    setTimeout(() => {
        refreshBtn.innerHTML = originalText;
        alert('Online users list refreshed!');
        // Example AJAX call:
        // fetch('get-online-users.php')
        // .then(response => response.json())
        // .then(data => {
        //     // Update table with new data
        //     location.reload();
        // });
    }, 1000);
}

// Add CSS for spin animation
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);

// ==========================================
// FEEDBACK FUNCTIONS
// ==========================================

function replyFeedback(feedbackId) {
    console.log('Replying to feedback:', feedbackId);
    const reply = prompt('Enter your reply:');
    if (reply) {
        alert(`Reply sent to feedback ID ${feedbackId}: ${reply}`);
        // TODO: Implement actual reply functionality
        // Example:
        // fetch('reply-feedback.php', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ id: feedbackId, reply: reply })
        // })
        // .then(response => response.json())
        // .then(data => {
        //     if (data.success) {
        //         alert('Reply sent successfully!');
        //     }
        // });
    }
}

function deleteFeedback(feedbackId) {
    console.log('Deleting feedback:', feedbackId);
    if (confirm('Are you sure you want to delete this feedback?')) {
        alert(`Feedback ${feedbackId} would be deleted (functionality not implemented)`);
        // TODO: Implement actual delete functionality
        // Example:
        // fetch(`delete-feedback.php?id=${feedbackId}`, {
        //     method: 'DELETE'
        // })
        // .then(response => response.json())
        // .then(data => {
        //     if (data.success) {
        //         location.reload();
        //     }
        // });
    }
}

function filterFeedback() {
    console.log('Filtering feedback');
    
    const filter = prompt('Filter by rating (1-5) or leave empty to show all:');
    
    if (filter === null) return; // User cancelled
    
    const feedbackCards = document.querySelectorAll('.feedback-card');
    
    if (filter === '') {
        // Show all
        feedbackCards.forEach(card => {
            card.style.display = '';
        });
        return;
    }
    
    const ratingFilter = parseInt(filter);
    
    if (isNaN(ratingFilter) || ratingFilter < 1 || ratingFilter > 5) {
        alert('Please enter a number between 1 and 5');
        return;
    }
    
    feedbackCards.forEach(card => {
        const ratingElement = card.querySelector('.feedback-rating');
        const stars = ratingElement.textContent.trim().split('⭐').length - 1;
        
        if (stars === ratingFilter) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// ==========================================
// LOGOUT FUNCTION
// ==========================================

function handleLogout(event) {
    // If user cancels, prevent the link navigation
    if (!confirm('Are you sure you want to logout?')) {
        if (event && event.preventDefault) event.preventDefault();
        return false;
    }
    console.log('Logging out...');
    // Allow default navigation to logout.php to proceed; optionally redirect explicitly:
    // window.location.href = 'logout.php';
    return true;
}

// ==========================================
// INITIALIZE ON PAGE LOAD
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Dashboard initialized');
    
    // Set initial active section
    switchSection('user-database');
    
    // Update current time (optional feature)
    updateTime();
    setInterval(updateTime, 1000);
});

function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    // You can display this somewhere in your header if needed
    // document.getElementById('currentTime').textContent = timeString;
}

// ==========================================
// KEYBOARD SHORTCUTS (Optional)
// ==========================================

document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.focus();
        }
    }
    
    // Escape to close mobile sidebar
    if (e.key === 'Escape') {
        closeSidebar();
    }
});

// ==========================================
// RESPONSIVE TABLE HANDLER
// ==========================================

function makeTablesResponsive() {
    const tables = document.querySelectorAll('.data-table');
    
    tables.forEach(table => {
        // Add horizontal scroll on small screens
        if (window.innerWidth < 768) {
            table.style.minWidth = '600px';
        } else {
            table.style.minWidth = 'auto';
        }
    });
}

// Run on load and resize
window.addEventListener('load', makeTablesResponsive);
window.addEventListener('resize', makeTablesResponsive);

// ==========================================
// NOTIFICATION SYSTEM (Optional)
// ==========================================

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? 'rgba(34, 197, 94, 0.2)' : 'rgba(167, 139, 250, 0.2)'};
        border: 1px solid ${type === 'success' ? 'rgba(34, 197, 94, 0.5)' : 'rgba(167, 139, 250, 0.5)'};
        border-radius: 0.75rem;
        color: var(--gray-900);
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Add animation styles
const animationStyle = document.createElement('style');
animationStyle.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(animationStyle);

// Example usage:
// showNotification('User updated successfully!', 'success');
// showNotification('Data loaded', 'info');