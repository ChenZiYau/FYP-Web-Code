<?php
// --- TOP OF FILE: Logic Header ---
require_once __DIR__ . '/../../includes/security.php';
configure_secure_session();
require_once __DIR__ . '/../../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Rate limit: 5 signups per 15 minutes per IP
    enforce_rate_limit($pdo, 'signup', 5, 900);

    // CSRF validation
    csrf_enforce();

    // Only accept expected fields
    $input = filter_input_fields(['first_name', 'last_name', 'email', 'password', 'confirm_password', 'csrf_token', 'agree_terms']);

    // 1. Validate all inputs with strict type/length checks
    $firstName = validate_string($input['first_name'] ?? '', 1, 50);
    $lastName  = validate_string($input['last_name'] ?? '', 0, 50); // Last name optional
    $email     = validate_email($input['email'] ?? '');
    $password  = $input['password'] ?? '';
    $confirm   = $input['confirm_password'] ?? '';

    if ($firstName === false) {
        echo json_encode(['success' => false, 'message' => 'First name is required (max 50 characters).']);
        exit;
    }
    if ($lastName === false) $lastName = '';

    if ($email === false) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }

    $pwCheck = validate_password($password);
    if ($pwCheck !== true) {
        echo json_encode(['success' => false, 'message' => $pwCheck]);
        exit;
    }

    if ($password !== $confirm) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit;
    }

    // 2. Database Operation
    try {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $email, $hash]);

        $userId = $pdo->lastInsertId();

        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = $userId;
        $_SESSION['role'] = 'user';
        $_SESSION['name'] = $firstName;

        echo json_encode([
            'success' => true,
            'message' => 'Account created! Redirecting to dashboard...',
            'redirect' => '../dashboard/dashboard.php'
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'Email already registered.']);
        } else {
            error_log('Signup error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiPlan - Sign Up</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="auth.css">
</head>
<body>
    <!-- Logo Header -->
    <div class="logo-header">
        <a href="../landing/index.php" class="logo-link">
            <svg class="logo-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span>OptiPlan</span>
        </a>
    </div>

    <!-- Auth Container -->
    <div class="auth-container">
        <!-- Left Side Content -->
        <div class="auth-content">
            <h1 class="auth-content-title">One Dashboard. <span class="gradient-text">Everything Organized.</span></h1>
            <p class="auth-content-description">Stop switching between apps. OptiPlan unifies your schedule, studies, and budget into one intelligent platform designed for students and young professionals.</p>
        </div>

        <!-- Right Side Form Wrapper -->
        <div class="auth-form-wrapper">
            <div class="auth-card">
                <div class="form-header">
                    <h1 class="form-title">Create Account</h1>
                    <p class="form-subtitle">Already have an account? <a href="login.php">Sign in</a></p>
                </div>

                <div class="error-message" id="errorMessage"></div>
                <div class="success-message" id="successMessage"></div>

                <form class="auth-form" method="POST" action="signup.php" id="signupForm">
                    <?php echo csrf_field(); ?>
                    <div class="form-group row">
                        <div>
                            <label class="form-label" for="firstName">First name</label>
                            <input
                                type="text"
                                id="firstName"
                                name="first_name"
                                class="form-input"
                                placeholder="John"
                                required
                            />
                        </div>
                        <div>
                            <label class="form-label" for="lastName">Last name</label>
                            <input
                                type="text"
                                id="lastName"
                                name="last_name"
                                class="form-input"
                                placeholder="Doe"
                                required
                            />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="you@example.com"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="password-input-wrapper">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="Create a strong password"
                                required
                                minlength="8"
                            />
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <svg id="eyeIcon-password" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirmPassword">Confirm password</label>
                        <div class="password-input-wrapper">
                            <input
                                type="password"
                                id="confirmPassword"
                                name="confirm_password"
                                class="form-input"
                                placeholder="Re-enter your password"
                                required
                                minlength="8"
                            />
                            <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
                                <svg id="eyeIcon-confirmPassword" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="agreeTerms" name="agree_terms" required />
                        <label for="agreeTerms">I agree to the Terms of Service and Privacy Policy</label>
                    </div>

                    <button type="submit" class="btn-submit">Create Account</button>
                </form>

                <div class="divider">
                    <span>Or continue with</span>
                </div>

                <div class="social-buttons">
                    <button class="btn-social" onclick="handleSocialLogin('google')">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Continue with Google
                    </button>
                    <button class="btn-social" onclick="handleSocialLogin('github')">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                        Continue with GitHub
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('eyeIcon-' + inputId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        // Show error message
        function showError(message) {
            const errorElement = document.getElementById('errorMessage');
            errorElement.textContent = message;
            errorElement.classList.add('show');
            setTimeout(() => {
                errorElement.classList.remove('show');
            }, 5000);
        }

        // Show success message
        function showSuccess(message) {
            const successElement = document.getElementById('successMessage');
            successElement.textContent = message;
            successElement.classList.add('show');
            setTimeout(() => {
                successElement.classList.remove('show');
            }, 5000);
        }

        // Client-side validation
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const agreeTerms = document.getElementById('agreeTerms').checked;

            // Validate passwords match
            if (password !== confirmPassword) {
                showError('Passwords do not match');
                return;
            }

            // Validate password strength
            if (password.length < 8) {
                showError('Password must be at least 8 characters long');
                return;
            }

            // Validate terms acceptance
            if (!agreeTerms) {
                showError('You must agree to the Terms of Service');
                return;
            }

            const formData = new FormData(this);

            // Send AJAX request to signup.php
            fetch('signup.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message || 'Account created successfully! Redirecting...');
                    setTimeout(() => {
                        window.location.href = data.redirect || '../dashboard/dashboard.php';
                    }, 1500);
                } else {
                    showError(data.message || 'Registration failed. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('An error occurred. Please try again.');
            });
        });

        // Handle Social Login
        function handleSocialLogin(provider) {
            console.log('Signing up with:', provider);
            alert(`Redirecting to ${provider} authentication...`);
            // Implement actual OAuth flow here
            // window.location.href = 'auth/' + provider;
        }
    </script>
</body>
</html>
