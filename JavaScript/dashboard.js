/**
 * OptiPlan Dashboard JavaScript
 * Handles theme switching, language toggling, and dynamic UI updates
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initThemePicker();
    initLanguageToggle();
    initFlashcards();
    initUploadArea();
    initFloatingChat();
    initChatScroll();
    initSettingsColorPicker();
});

/**
 * Theme Picker Functionality
 * Dynamically updates CSS variables when color is changed
 */
function initThemePicker() {
    const themeColorInput = document.getElementById('themeColor');
    
    if (themeColorInput) {
        themeColorInput.addEventListener('input', function(e) {
            updateThemeColor(e.target.value);
        });
        
        themeColorInput.addEventListener('change', function(e) {
            // Save to server via form submission
            saveThemeColor(e.target.value);
        });
    }
}

/**
 * Update theme color CSS variables
 */
function updateThemeColor(color) {
    document.documentElement.style.setProperty('--primary-color', color);
    document.documentElement.style.setProperty('--primary-light', color + '20');
    document.documentElement.style.setProperty('--primary-dark', color + 'dd');
    
    // Update color value display if exists
    const colorValue = document.querySelector('.color-value');
    if (colorValue) {
        colorValue.textContent = color;
    }
}

/**
 * Save theme color to server
 */
function saveThemeColor(color) {
    // Create a hidden form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    const actionInput = document.createElement('input');
    actionInput.name = 'action';
    actionInput.value = 'update_settings';
    form.appendChild(actionInput);
    
    const colorInput = document.createElement('input');
    colorInput.name = 'theme_color';
    colorInput.value = color;
    form.appendChild(colorInput);
    
    document.body.appendChild(form);
    form.submit();
}

/**
 * Language Toggle Functionality
 */
function initLanguageToggle() {
    // Language buttons are handled by onclick in HTML
}

/**
 * Set language and reload page
 */
function setLanguage(lang) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    const actionInput = document.createElement('input');
    actionInput.name = 'action';
    actionInput.value = 'update_settings';
    form.appendChild(actionInput);
    
    const langInput = document.createElement('input');
    langInput.name = 'language';
    langInput.value = lang;
    form.appendChild(langInput);
    
    document.body.appendChild(form);
    form.submit();
}

/**
 * Flashcard Flip Functionality
 */
function initFlashcards() {
    // Flashcards are handled by onclick in HTML
}

/**
 * Flip a flashcard
 */
function flipCard(element) {
    element.classList.toggle('flipped');
}

/**
 * Upload Area Functionality
 */
function initUploadArea() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileUpload');
    
    if (uploadArea && fileInput) {
        // Click to upload
        uploadArea.addEventListener('click', function() {
            fileInput.click();
        });
        
        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = 'var(--primary-color)';
            uploadArea.style.background = 'var(--primary-light)';
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = '';
            uploadArea.style.background = '';
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = '';
            uploadArea.style.background = '';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileUpload(files[0]);
            }
        });
        
        // File input change
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleFileUpload(e.target.files[0]);
            }
        });
    }
}

/**
 * Handle file upload
 */
function handleFileUpload(file) {
    const allowedTypes = ['application/pdf', 'text/plain', 'application/msword', 
                          'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    
    if (!allowedTypes.includes(file.type)) {
        alert('Please upload a PDF, TXT, or Word document.');
        return;
    }
    
    // Show upload feedback
    const uploadArea = document.getElementById('uploadArea');
    uploadArea.innerHTML = `
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--success-color)" stroke-width="2">
            <path d="M9 11l3 3L22 4"/>
            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
        </svg>
        <p>File uploaded: ${file.name}</p>
        <p style="font-size: 0.75rem; color: var(--text-muted);">Processing... (Demo only)</p>
    `;
    
    // In a real application, you would upload the file to the server here
    // and process it to extract flashcard content
    setTimeout(() => {
        uploadArea.innerHTML = `
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            <p>Drag & drop files here or click to browse</p>
        `;
    }, 3000);
}

/**
 * Floating Chat Functionality
 */
function initFloatingChat() {
    // Floating chat is handled by onclick in HTML
}

/**
 * Toggle floating chat window
 */
function toggleFloatingChat() {
    const floatingChat = document.getElementById('floatingChat');
    if (floatingChat) {
        floatingChat.classList.toggle('open');
        
        // Scroll to bottom of messages
        const messagesContainer = floatingChat.querySelector('.floating-chat-messages');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }
}

/**
 * Auto-scroll chat to bottom
 */
function initChatScroll() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

/**
 * Settings Color Picker
 */
function initSettingsColorPicker() {
    const settingsTheme = document.getElementById('settings_theme');
    
    if (settingsTheme) {
        settingsTheme.addEventListener('input', function(e) {
            updateThemeColor(e.target.value);
            
            // Update color value display
            const colorValue = document.querySelector('.color-value');
            if (colorValue) {
                colorValue.textContent = e.target.value;
            }
        });
    }
}

/**
 * Set preset color from settings page
 */
function setPresetColor(color) {
    const settingsTheme = document.getElementById('settings_theme');
    const headerTheme = document.getElementById('themeColor');
    
    if (settingsTheme) {
        settingsTheme.value = color;
    }
    
    if (headerTheme) {
        headerTheme.value = color;
    }
    
    updateThemeColor(color);
    
    // Update color value display
    const colorValue = document.querySelector('.color-value');
    if (colorValue) {
        colorValue.textContent = color;
    }
}

/**
 * Confirm action before delete
 */
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

/**
 * Format currency
 */
function formatCurrency(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

/**
 * Format date
 */
function formatDate(dateString, locale = 'en-US') {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat(locale, {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    }).format(date);
}

/**
 * Animate progress bar
 */
function animateProgressBar(element, targetWidth) {
    element.style.transition = 'width 1s ease-out';
    element.style.width = targetWidth + '%';
}

/**
 * Show notification toast
 */
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        padding: 1rem 2rem;
        background: ${type === 'success' ? 'var(--success-color)' : 
                      type === 'error' ? 'var(--danger-color)' : 
                      'var(--primary-color)'};
        color: white;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        z-index: 10000;
        animation: slideUp 0.3s ease-out;
    `;
    
    document.body.appendChild(toast);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideDown 0.3s ease-out forwards';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Debounce function for performance
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
 * Local storage helpers
 */
const storage = {
    get: function(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (e) {
            return defaultValue;
        }
    },
    
    set: function(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
            return true;
        } catch (e) {
            return false;
        }
    },
    
    remove: function(key) {
        try {
            localStorage.removeItem(key);
            return true;
        } catch (e) {
            return false;
        }
    }
};

/**
 * Keyboard shortcuts
 */
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K: Focus search or quick action
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        // Could open a quick action modal
    }
    
    // Escape: Close floating chat
    if (e.key === 'Escape') {
        const floatingChat = document.getElementById('floatingChat');
        if (floatingChat && floatingChat.classList.contains('open')) {
            floatingChat.classList.remove('open');
        }
    }
});

/**
 * Add CSS animation keyframes
 */
const style = document.createElement('style');
style.textContent = `
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translate(-50%, 20px);
        }
        to {
            opacity: 1;
            transform: translate(-50%, 0);
        }
    }
    
    @keyframes slideDown {
        from {
            opacity: 1;
            transform: translate(-50%, 0);
        }
        to {
            opacity: 0;
            transform: translate(-50%, 20px);
        }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
`;
document.head.appendChild(style);

/**
 * Initialize tooltips
 */
function initTooltips() {
    const elementsWithTitle = document.querySelectorAll('[title]');
    
    elementsWithTitle.forEach(element => {
        const title = element.getAttribute('title');
        element.removeAttribute('title');
        element.setAttribute('data-tooltip', title);
        
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = e.target.getAttribute('data-tooltip');
    tooltip.style.cssText = `
        position: fixed;
        background: var(--text-primary);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        z-index: 10000;
        pointer-events: none;
    `;
    
    document.body.appendChild(tooltip);
    
    const rect = e.target.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
    
    e.target._tooltip = tooltip;
}

function hideTooltip(e) {
    if (e.target._tooltip) {
        e.target._tooltip.remove();
        delete e.target._tooltip;
    }
}

/**
 * Service Worker Registration (for PWA support)
 */
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        // Service worker would be registered here for PWA functionality
        // navigator.serviceWorker.register('/sw.js');
    });
}

/**
 * Export functions for global access
 */
window.OptiPlan = {
    setLanguage,
    flipCard,
    toggleFloatingChat,
    setPresetColor,
    showToast,
    formatCurrency,
    formatDate,
    storage
};