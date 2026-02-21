<?php
require_once __DIR__ . '/../../includes/security.php';
configure_secure_session();
session_start();
require_once __DIR__ . '/../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch current user data
$stmt = $pdo->prepare("SELECT first_name, last_name, email, username, pfp_path FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$currentUsername = $user['username'] ?? '';
$currentPfp = $user['pfp_path'] ?? '';
$fullName = trim($user['first_name'] . ' ' . $user['last_name']);
$email = $user['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - OptiPlan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../dashboard/dashboard.css">
    <link rel="stylesheet" href="settings.css">
</head>
<body class="dashboard-body">

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="../dashboard/dashboard.php" class="sidebar-logo">
                <svg class="sidebar-logo-icon" viewBox="0 0 40 40" fill="none">
                    <path d="M20 5L35 12.5V27.5L20 35L5 27.5V12.5L20 5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                    <circle cx="20" cy="20" r="6" fill="currentColor" />
                </svg>
                <span class="sidebar-logo-text">OptiPlan</span>
            </a>
            <div class="sidebar-toggle" id="sidebarToggle">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="../dashboard/dashboard.php" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="nav-text">Dashboard</span>
            </a>

            <a href="settings.php" class="nav-item active">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="nav-text">Settings</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="../auth/logout.php" class="nav-item logout-btn">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="nav-text">Logout</span>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="dashboard-header">
            <div class="header-left">
                <button class="mobile-menu-toggle" id="mobileSidebarToggle">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="welcome-section">
                    <h1 class="welcome-text">Settings</h1>
                </div>
            </div>
        </header>

        <div class="settings-container">
            <!-- Profile Picture Section -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <h2 class="settings-card-title">Profile Picture</h2>
                    <p class="settings-card-desc">Upload a profile picture. JPG or PNG, max 2MB.</p>
                </div>
                <div class="settings-card-body">
                    <div class="pfp-upload-area">
                        <div class="pfp-preview" id="pfpPreview">
                            <?php if ($currentPfp && file_exists('../../' . $currentPfp)): ?>
                                <img src="../../<?php echo htmlspecialchars($currentPfp); ?>" alt="Profile" id="pfpImage">
                            <?php else: ?>
                                <span class="pfp-initials" id="pfpInitials"><?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="pfp-actions">
                            <label class="btn-settings-secondary" for="pfpInput">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Choose Image
                            </label>
                            <input type="file" id="pfpInput" accept=".jpg,.jpeg,.png" hidden>
                            <button class="btn-settings-primary" id="uploadPfpBtn" disabled>
                                <span class="btn-text">Upload</span>
                                <span class="btn-spinner" hidden>
                                    <svg class="spinner-svg" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4 31.4" stroke-linecap="round" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <p class="pfp-file-name" id="pfpFileName"></p>
                        <p class="settings-message" id="pfpMessage"></p>
                    </div>
                </div>
            </div>

            <!-- Username Section -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <h2 class="settings-card-title">Username</h2>
                    <p class="settings-card-desc">Choose a unique username. This will be displayed across OptiPlan.</p>
                </div>
                <div class="settings-card-body">
                    <div class="settings-form-group">
                        <label class="settings-label" for="usernameInput">Username</label>
                        <input type="text" id="usernameInput" class="settings-input" value="<?php echo htmlspecialchars($currentUsername); ?>" placeholder="Enter a username" maxlength="50" autocomplete="off">
                    </div>
                    <p class="settings-message" id="usernameMessage"></p>
                    <button class="btn-settings-primary" id="saveUsernameBtn">
                        <span class="btn-text">Save Changes</span>
                        <span class="btn-spinner" hidden>
                            <svg class="spinner-svg" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4 31.4" stroke-linecap="round" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Account Info (read-only) -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <h2 class="settings-card-title">Account Information</h2>
                    <p class="settings-card-desc">Your account details. These cannot be changed here.</p>
                </div>
                <div class="settings-card-body">
                    <div class="settings-form-group">
                        <label class="settings-label">Full Name</label>
                        <input type="text" class="settings-input" value="<?php echo htmlspecialchars($fullName); ?>" readonly>
                    </div>
                    <div class="settings-form-group">
                        <label class="settings-label">Email</label>
                        <input type="text" class="settings-input" value="<?php echo htmlspecialchars($email); ?>" readonly>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
    // Sidebar toggle
    (function() {
        var toggle = document.getElementById('sidebarToggle');
        var mobileTgl = document.getElementById('mobileSidebarToggle');
        var sidebar = document.getElementById('sidebar');
        if (toggle) toggle.addEventListener('click', function() { sidebar.classList.toggle('collapsed'); });
        if (mobileTgl) mobileTgl.addEventListener('click', function() { sidebar.classList.toggle('mobile-open'); });
    })();

    // PFP Upload
    (function() {
        var fileInput = document.getElementById('pfpInput');
        var uploadBtn = document.getElementById('uploadPfpBtn');
        var preview = document.getElementById('pfpPreview');
        var fileNameEl = document.getElementById('pfpFileName');
        var msgEl = document.getElementById('pfpMessage');

        fileInput.addEventListener('change', function() {
            var file = this.files[0];
            if (!file) return;

            // Validate
            var allowed = ['image/jpeg', 'image/png'];
            if (allowed.indexOf(file.type) === -1) {
                showMsg(msgEl, 'Only JPG and PNG files are allowed.', 'error');
                this.value = '';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                showMsg(msgEl, 'File size must be under 2MB.', 'error');
                this.value = '';
                return;
            }

            fileNameEl.textContent = file.name;
            uploadBtn.disabled = false;
            showMsg(msgEl, '', '');

            // Preview
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" id="pfpImage">';
            };
            reader.readAsDataURL(file);
        });

        uploadBtn.addEventListener('click', function() {
            var file = fileInput.files[0];
            if (!file) return;

            setBtnLoading(uploadBtn, true);
            var fd = new FormData();
            fd.append('pfp', file);
            fd.append('csrf_token', <?php echo json_encode(csrf_token()); ?>);

            fetch('api_settings.php?action=upload_pfp', { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                setBtnLoading(uploadBtn, false);
                if (data.success) {
                    showMsg(msgEl, data.message, 'success');
                    uploadBtn.disabled = true;
                    fileInput.value = '';
                    fileNameEl.textContent = '';
                    // Update preview with server path
                    var img = document.getElementById('pfpImage');
                    if (img) img.src = '../../' + data.pfp_path + '?t=' + Date.now();
                } else {
                    showMsg(msgEl, data.message, 'error');
                }
            })
            .catch(function() {
                setBtnLoading(uploadBtn, false);
                showMsg(msgEl, 'Network error. Please try again.', 'error');
            });
        });
    })();

    // Username Save
    (function() {
        var btn = document.getElementById('saveUsernameBtn');
        var input = document.getElementById('usernameInput');
        var msgEl = document.getElementById('usernameMessage');

        btn.addEventListener('click', function() {
            var username = input.value.trim();
            if (username === '') {
                showMsg(msgEl, 'Username cannot be empty.', 'error');
                return;
            }
            if (!/^[a-zA-Z0-9_]{3,50}$/.test(username)) {
                showMsg(msgEl, 'Username must be 3-50 characters: letters, numbers, or underscores.', 'error');
                return;
            }

            setBtnLoading(btn, true);
            var fd = new FormData();
            fd.append('username', username);
            fd.append('csrf_token', <?php echo json_encode(csrf_token()); ?>);

            fetch('api_settings.php?action=update_username', { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                setBtnLoading(btn, false);
                showMsg(msgEl, data.message, data.success ? 'success' : 'error');
            })
            .catch(function() {
                setBtnLoading(btn, false);
                showMsg(msgEl, 'Network error. Please try again.', 'error');
            });
        });
    })();

    // Helpers
    function showMsg(el, msg, type) {
        el.textContent = msg;
        el.className = 'settings-message' + (type ? ' settings-message--' + type : '');
    }

    function setBtnLoading(btn, loading) {
        var textEl = btn.querySelector('.btn-text');
        var spinnerEl = btn.querySelector('.btn-spinner');
        if (loading) {
            btn.disabled = true;
            if (textEl) textEl.hidden = true;
            if (spinnerEl) spinnerEl.hidden = false;
        } else {
            btn.disabled = false;
            if (textEl) textEl.hidden = false;
            if (spinnerEl) spinnerEl.hidden = true;
        }
    }
    </script>
</body>
</html>
