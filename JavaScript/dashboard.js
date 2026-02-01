document.addEventListener('DOMContentLoaded', function() {
    initThemePicker();
    initChatScroll();
    initUploadArea();
});

function initThemePicker() {
    const themeColorInput = document.getElementById('themeColor');
    if (themeColorInput) {
        themeColorInput.addEventListener('input', function(e) {
            updateThemeColor(e.target.value);
        });
        themeColorInput.addEventListener('change', function(e) {
            saveThemeColor(e.target.value);
        });
    }
    const settingsTheme = document.getElementById('settings_theme');
    if (settingsTheme) {
        settingsTheme.addEventListener('input', function(e) {
            updateThemeColor(e.target.value);
            const colorValue = document.querySelector('.color-value');
            if (colorValue) colorValue.textContent = e.target.value;
        });
    }
}

function updateThemeColor(color) {
    document.documentElement.style.setProperty('--primary-color', color);
    document.documentElement.style.setProperty('--primary-light', color + '15');
    document.documentElement.style.setProperty('--primary-dark', color + 'dd');
}

function saveThemeColor(color) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    form.innerHTML = '<input name="action" value="update_settings"><input name="theme_color" value="' + color + '">';
    document.body.appendChild(form);
    form.submit();
}

function setLanguage(lang) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    form.innerHTML = '<input name="action" value="update_settings"><input name="language" value="' + lang + '">';
    document.body.appendChild(form);
    form.submit();
}

function flipCard(el) {
    el.classList.toggle('flipped');
}

function setPresetColor(color) {
    const settingsTheme = document.getElementById('settings_theme');
    const headerTheme = document.getElementById('themeColor');
    if (settingsTheme) settingsTheme.value = color;
    if (headerTheme) headerTheme.value = color;
    updateThemeColor(color);
    const colorValue = document.querySelector('.color-value');
    if (colorValue) colorValue.textContent = color;
}

function initChatScroll() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
}

function initUploadArea() {
    const uploadArea = document.getElementById('uploadArea');
    if (uploadArea) {
        uploadArea.addEventListener('click', function() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.pdf,.txt,.doc,.docx';
            input.onchange = function(e) {
                if (e.target.files.length > 0) {
                    uploadArea.innerHTML = '<p style="color:var(--success)">✓ ' + e.target.files[0].name + ' uploaded</p>';
                }
            };
            input.click();
        });
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
            if (e.dataTransfer.files.length > 0) {
                uploadArea.innerHTML = '<p style="color:var(--success)">✓ ' + e.dataTransfer.files[0].name + ' uploaded</p>';
            }
        });
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const flipped = document.querySelectorAll('.flashcard.flipped');
        flipped.forEach(card => card.classList.remove('flipped'));
    }
});