<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['name'] ?? '';
$userRole = $_SESSION['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="OptiPlan - AI-powered productivity dashboard for students and young professionals. Manage schedules, studies, and budgets in one place.">
    <title>OptiPlan - Your All-in-One Productivity Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
        <style>
            /* Force dropdown icons to 18px regardless of external files */
            .user-dropdown .dropdown-item svg {
                width: 18px !important;
                height: 18px !important;
                min-width: 18px !important;
                max-width: 18px !important;
                display: inline-block !important;
            }

            .user-dropdown .dropdown-item {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                gap: 10px !important;
                padding: 10px 15px !important;
                text-decoration: none !important;
                color: white !important;
            }

            /* This overrides all external CSS files */
            #userMenu { position: relative; }
            #userDropdown { 
                display: none; 
                position: absolute; 
                right: 0; 
                background: #1a1a2e; 
                border: 1px solid #333;
                z-index: 10000;
            }
            #userMenu.active #userDropdown { display: block !important; }
            .dropdown-item svg { width: 18px !important; height: 18px !important; }
        </style>
<body>
    <!-- Fixed Header Navigation -->
    <header class="header" id="header">
        <nav class="nav-container">
            <!-- Logo Section -->
            <div class="logo">
                <a href="#top" class="logo-link">
                    <svg class="logo-icon" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 5L35 12.5V27.5L20 35L5 27.5V12.5L20 5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <circle cx="20" cy="20" r="6" fill="currentColor"/>
                    </svg>
                    <span class="logo-text">OptiPlan</span>
                </a>
            </div>

            <!-- Center Navigation Links -->
            <ul class="nav-links">
                <li class="nav-item dropdown">
                    <button class="nav-link dropdown-trigger" aria-expanded="false" aria-haspopup="true">
                        About
                        <svg class="dropdown-icon" width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#about-me" class="dropdown-link">About Me</a></li>
                        <li><a href="#about-optiplan" class="dropdown-link">About OptiPlan</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#problem" class="nav-link">The Problems</a>
                </li>
                <li class="nav-item">
                    <a href="#features" class="nav-link">Features</a>
                </li>
                <li class="nav-item">
                    <a href="#tutorial" class="nav-link">Tutorial Video</a>
                </li>
                <li class="nav-item">
                    <a href="#faq" class="nav-link">FAQ</a>
                </li>
                <li class="nav-item">
                    <a href="#form" class="nav-link">Feedback Form</a>
                </li>
            </ul>

            <!-- Right Action Buttons -->
            <div class="nav-actions">
                <?php if ($isLoggedIn): ?>
                <!-- Logged In: User Menu with Dropdown -->
                <div class="user-menu" id="userMenu">
                <button class="user-menu-trigger" id="userMenuTrigger" type="button">
                    <svg class="user-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    
                    <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                    
                    <svg class="chevron-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>

                <div class="user-dropdown" id="userDropdown">
                    <a href="<?php echo ($userRole === 'admin') ? 'admin.php' : 'dashboard.php'; ?>" class="dropdown-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Dashboard</span> </a>
                    <a href="<?php echo ($userRole === 'admin') ? 'admin.php' : 'dashboard.php'; ?>#settings" class="dropdown-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        <span>Settings</span> </a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php" class="dropdown-item logout">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Log Out</span> </a>
                </div>
                </div>
                <?php else: ?>
                <!-- Not Logged In: Login/Signup Buttons -->
                <a href="login.php" class="btn-link">Log In</a>
                <a href="signup.php" class="btn-primary">Try Now</a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" aria-label="Toggle menu">
                <span class="hamburger"></span>
            </button>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero" id="top">
            <div class="hero-content">
                <div class="hero-text">
                    <span class="hero-badge">AI-Powered Productivity</span>
                    <h1 class="hero-title">
                        One Dashboard.<br>
                        <span class="gradient-text">Everything Organized.</span>
                    </h1>
                    <p class="hero-description">
                        Stop switching between apps. OptiPlan unifies your schedule, studies, and budget 
                        into one intelligent platform designed for students and young professionals.
                    </p>
                    <div class="hero-actions">
                        <?php if ($isLoggedIn): ?>
                        <a href="<?php echo ($userRole === 'admin') ? 'admin.php' : 'dashboard.php'; ?>" class="btn-hero-primary">Go to Dashboard</a>
                        <?php else: ?>
                        <a href="signup.php" class="btn-hero-primary">Get Started Free</a>
                        <?php endif; ?>
                        <a href="#tutorial" class="btn-hero-secondary">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <circle cx="10" cy="10" r="9" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M8 6L14 10L8 14V6Z" fill="currentColor"/>
                            </svg>
                            Watch Demo
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number">75%+</span>
                            <span class="stat-label">Students use 3+ apps daily</span>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat-item">
                            <span class="stat-number">1</span>
                            <span class="stat-label">Platform solves it all</span>
                        </div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="dashboard-preview">
                        <div class="preview-card card-1">
                            <div class="card-icon">📅</div>
                            <div class="card-content">
                                <h3>Smart Scheduling</h3>
                                <p>AI-optimized calendar</p>
                            </div>
                        </div>
                        <div class="preview-card card-2">
                            <div class="card-icon">📚</div>
                            <div class="card-content">
                                <h3>Study Support</h3>
                                <p>Personalized learning</p>
                            </div>
                        </div>
                        <div class="preview-card card-3">
                            <div class="card-icon">💰</div>
                            <div class="card-content">
                                <h3>Budget Tracking</h3>
                                <p>Financial awareness</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

                <!-- About OptiPlan Section -->
        <section class="section about-section" id="about-me">
            <div class="container">
                <div class="about-me-content">
                    <span class="section-label">About the Creator</span>
                    <h2 class="section-title">Built by a Student, For Students</h2>
                    <p class="about-me-text">
                        Hi! I'm a student who experienced firsthand the frustration of juggling multiple apps 
                        for scheduling, studying, and budgeting. After missing too many deadlines and feeling 
                        overwhelmed by app-switching, I decided to build the solution I wished existed.
                    </p>
                    <p class="about-me-text">
                        OptiPlan is my final year project and a passion project aimed at making student life 
                        more manageable. I hope it helps you as much as it's helped me.
                    </p>
                </div>
            </div>
        </section>

        <!-- About Me Section -->
        <section class="section about-me-section" id="about-optiplan">
            <div class="container">
                <div class="about-layout">
                    <div class="about-visual">
                        <div class="about-card">
                            <div class="about-stat">
                                <span class="about-stat-number">3-in-1</span>
                                <span class="about-stat-label">Integrated Platform</span>
                            </div>
                        </div>
                    </div>
                    <div class="about-content">
                        <span class="section-label">About OptiPlan</span>
                        <h2 class="section-title">Built for Students Who Want More</h2>
                        <p class="about-text">
                            OptiPlan was created to solve a universal problem: the chaos of managing multiple apps 
                            for different aspects of student life. We believe productivity shouldn't be complicated.
                        </p>
                        <p class="about-text">
                            Our AI-powered platform learns from your behavior, adapts to your schedule, and helps 
                            you make smarter decisions about your time and money. Whether you're juggling assignments, 
                            part-time work, or extracurriculars, OptiPlan keeps everything in sync.
                        </p>
                        <div class="about-features">
                            <div class="about-feature-item">
                                <svg class="check-icon" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <path d="M8 12L11 15L16 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>AI-Powered Intelligence</span>
                            </div>
                            <div class="about-feature-item">
                                <svg class="check-icon" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <path d="M8 12L11 15L16 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Unified Dashboard</span>
                            </div>
                            <div class="about-feature-item">
                                <svg class="check-icon" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <path d="M8 12L11 15L16 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Student-First Design</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Problem Statement Section -->
        <section class="section problem-section" id="problem">
            <div class="container">
                <div class="section-header">
                    <span class="section-label">The Problem</span>
                    <h2 class="section-title">Fragmented Productivity is Killing Your Time</h2>
                </div>
                <div class="problem-grid">
                    <div class="problem-card">
                        <div class="problem-number">01</div>
                        <h3>App Overload</h3>
                        <p>Constantly switching between calendar apps, study tools, and budget trackers wastes valuable time and mental energy.</p>
                    </div>
                    <div class="problem-card">
                        <div class="problem-number">02</div>
                        <h3>Missed Tasks</h3>
                        <p>Important deadlines and activities fall through the cracks when scattered across multiple disconnected platforms.</p>
                    </div>
                    <div class="problem-card">
                        <div class="problem-number">03</div>
                        <h3>No Integration</h3>
                        <p>Your schedule doesn't talk to your budget. Your study plan doesn't sync with your calendar. Everything exists in silos.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
<section class="section features-section" id="features">
    <div class="container">
        <div class="section-header centered">
            <span class="section-label">Features</span>
            <h2 class="section-title">Three Tools. One Platform. Zero Hassle.</h2>
            <p class="section-description">Everything you need to stay organized, productive, and financially aware.</p>
        </div>
        <div class="features-grid">
            <!-- Feature Card 1 -->
            <div class="feature-card flip-card">
                <div class="flip-card-inner">
                    <!-- Front -->
                    <div class="flip-card-front">
                        <div class="feature-icon">
                            <svg viewBox="0 0 48 48" fill="none">
                                <rect x="8" y="12" width="32" height="28" rx="2" stroke="currentColor" stroke-width="2"/>
                                <path d="M8 18H40" stroke="currentColor" stroke-width="2"/>
                                <circle cx="18" cy="26" r="2" fill="currentColor"/>
                                <circle cx="24" cy="26" r="2" fill="currentColor"/>
                                <circle cx="30" cy="26" r="2" fill="currentColor"/>
                            </svg>
                        </div>
                        <h3 class="feature-title">Smart Scheduling</h3>
                        <p class="feature-description">AI-powered calendar that learns your habits and suggests optimal time slots for tasks, classes, and breaks.</p>
                        <ul class="feature-list">
                            <li>Automatic conflict detection</li>
                            <li>Smart time blocking</li>
                            <li>Integration with university schedules</li>
                        </ul>
                    </div>
                    <!-- Back -->
                    <div class="flip-card-back">
                        <h3 class="feature-title">Smart Scheduling Plus</h3>
                        <p class="feature-description">Advanced analytics and suggestions for peak productivity and time optimization.</p>
                        <ul class="feature-list">
                            <li>Priority task recommendations</li>
                            <li>AI-generated daily agenda</li>
                            <li>Integration with multiple calendars</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Feature Card 2 (Featured) -->
            <div class="feature-card featured flip-card">
                <div class="flip-card-inner">
                    <!-- Front -->
                    <div class="flip-card-front">
                        <div class="featured-badge">Most Popular</div>
                        <div class="feature-icon">
                            <svg viewBox="0 0 48 48" fill="none">
                                <path d="M14 8H34C36.2091 8 38 9.79086 38 12V38C38 40.2091 36.2091 42 34 42H14C11.7909 42 10 40.2091 10 38V12C10 9.79086 11.7909 8 14 8Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M18 16H30M18 24H30M18 32H26" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3 class="feature-title">Study Support</h3>
                        <p class="feature-description">Personalized study plans, progress tracking, and AI-powered insights to improve your academic performance.</p>
                        <ul class="feature-list">
                            <li>Custom study schedules</li>
                            <li>Progress analytics</li>
                            <li>Smart revision reminders</li>
                        </ul>
                    </div>
                    <!-- Back -->
                    <div class="flip-card-back">
                        <h3 class="feature-title">Study Support Plus</h3>
                        <p class="feature-description">AI tutors and collaborative study tools for maximum learning efficiency.</p>
                        <ul class="feature-list">
                            <li>AI-generated practice quizzes</li>
                            <li>Peer study group recommendations</li>
                            <li>Real-time progress feedback</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Feature Card 3 -->
            <div class="feature-card flip-card">
                <div class="flip-card-inner">
                    <!-- Front -->
                    <div class="flip-card-front">
                        <div class="feature-icon">
                            <svg viewBox="0 0 48 48" fill="none">
                                <circle cx="24" cy="24" r="16" stroke="currentColor" stroke-width="2"/>
                                <path d="M24 24L24 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M24 24L32 28" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3 class="feature-title">Budget Tracking</h3>
                        <p class="feature-description">Track expenses, set savings goals, and gain financial awareness with intuitive budgeting tools.</p>
                        <ul class="feature-list">
                            <li>Expense categorization</li>
                            <li>Visual spending insights</li>
                            <li>Savings goal tracking</li>
                        </ul>
                    </div>
                    <!-- Back -->
                    <div class="flip-card-back">
                        <h3 class="feature-title">Budget Tracking Plus</h3>
                        <p class="feature-description">Detailed financial insights and automated saving recommendations.</p>
                        <ul class="feature-list">
                            <li>Spending trend analysis</li>
                            <li>Automated savings suggestions</li>
                            <li>Custom alerts for overspending</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

        <!-- Tutorial Video Section -->
        <section class="section tutorial-section" id="tutorial">
            <div class="container">
                <div class="section-header centered">
                    <span class="section-label">Tutorial</span>
                    <h2 class="section-title">See OptiPlan in Action</h2>
                    <p class="section-description">Watch a quick walkthrough of how OptiPlan can transform your productivity.</p>
                </div>
                <div class="video-container">
                    <div class="video-wrapper">
                        <!-- Placeholder for video - replace with actual video embed -->
                        <div class="video-placeholder">
                            <svg class="play-icon" viewBox="0 0 80 80" fill="none">
                                <circle cx="40" cy="40" r="38" stroke="currentColor" stroke-width="2"/>
                                <path d="M32 26L54 40L32 54V26Z" fill="currentColor"/>
                            </svg>
                            <p>Click to play tutorial video</p>
                        </div>
                        <!-- For actual video, use:
                        <iframe src="YOUR_VIDEO_URL" frameborder="0" allowfullscreen></iframe>
                        -->
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="section faq-section" id="faq">
            <div class="container">
                <div class="section-header centered">
                    <span class="section-label">FAQ</span>
                    <h2 class="section-title">Frequently Asked Questions</h2>
                </div>
                <div class="faq-list">
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            <span>What makes OptiPlan different from other productivity apps?</span>
                            <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p>OptiPlan is the only platform that integrates scheduling, study support, and budgeting into one unified dashboard. While other apps focus on just one area, OptiPlan connects all aspects of student life with AI-powered insights.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            <span>Is OptiPlan free to use?</span>
                            <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p>Yes! OptiPlan offers a free tier with core features. Premium features like advanced AI insights and unlimited integrations are available with a student subscription.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            <span>Can I sync OptiPlan with my university calendar?</span>
                            <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p>Absolutely! OptiPlan supports calendar imports from most university systems and can automatically sync your class schedules, assignment deadlines, and exam dates.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            <span>How does the AI-powered scheduling work?</span>
                            <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p>Our AI learns from your behavior patterns and preferences to suggest optimal time slots for tasks, detect scheduling conflicts, and recommend breaks. The more you use OptiPlan, the smarter it becomes.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            <span>Is my data secure?</span>
                            <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p>Yes! We use industry-standard encryption to protect your data. Your information is private, never sold to third parties, and you can export or delete it at any time.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FeedbackForm Section -->
        <section class="section feedback-form-section" id="form">
            <div class="container">
                <div class="section-header centered">
                    <span class="section-label">Contact Us</span>
                    <h2 class="section-title">We Value Your Feedback</h2>
                    <p class="section-description">Have a suggestion or found a bug? Let us know how we can improve OptiPlan for you.</p>
                </div>
                
                <div class="form-wrapper">
                    <form class="contact-form" action="#" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name" placeholder="Your Name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" placeholder="your@email.com" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Topic</label>
                            <select id="category" name="category">
                                <option value="general">General Feedback</option>
                                <option value="bug">Report a Bug</option>
                                <option value="feature">Feature Request</option>
                                <option value="support">Help & Support</option>
                            </select>
                            <svg class="select-icon" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="5" placeholder="Tell us what you think..." required></textarea>
                        </div>

                        <button type="submit" class="btn-primary btn-submit">
                            Send Feedback
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Feedbacks Section -->
        <section class="section feedbacks-section" id="feedbacks">
            <div class="container">
                <div class="section-header centered">
                    <span class="section-label">Testimonials</span>
                    <h2 class="section-title">What Students Are Saying</h2>
                </div>
                <div class="feedback-grid">
                    <div class="feedback-card">
                        <div class="feedback-rating">⭐⭐⭐⭐⭐</div>
                        <p class="feedback-text">
                            "OptiPlan changed how I manage my time. I went from constantly stressed to actually 
                            having free time. The budget tracker alone saved me hundreds this semester!"
                        </p>
                        <div class="feedback-author">
                            <div class="author-avatar">SM</div>
                            <div class="author-info">
                                <div class="author-name">Sarah Martinez</div>
                                <div class="author-role">Computer Science, Year 3</div>
                            </div>
                        </div>
                    </div>
                    <div class="feedback-card">
                        <div class="feedback-rating">⭐⭐⭐⭐⭐</div>
                        <p class="feedback-text">
                            "Finally, an app that gets student life. The AI scheduling is scary accurate at 
                            predicting when I need breaks. My grades improved and I'm less stressed."
                        </p>
                        <div class="feedback-author">
                            <div class="author-avatar">JC</div>
                            <div class="author-info">
                                <div class="author-name">James Chen</div>
                                <div class="author-role">Business Admin, Year 2</div>
                            </div>
                        </div>
                    </div>
                    <div class="feedback-card">
                        <div class="feedback-rating">⭐⭐⭐⭐⭐</div>
                        <p class="feedback-text">
                            "I used to juggle 5 different apps. Now everything is in one place. The study 
                            planner helped me ace my finals, and the interface is actually beautiful."
                        </p>
                        <div class="feedback-author">
                            <div class="author-avatar">EP</div>
                            <div class="author-info">
                                <div class="author-name">Emma Park</div>
                                <div class="author-role">Psychology, Year 4</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-main">
                <div class="footer-brand">
                    <div class="footer-logo">
                        <svg class="logo-icon" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 5L35 12.5V27.5L20 35L5 27.5V12.5L20 5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <circle cx="20" cy="20" r="6" fill="currentColor"/>
                        </svg>
                        <span>OptiPlan</span>
                    </div>
                    <p class="footer-tagline">Your all-in-one AI-powered productivity dashboard for students and young professionals.</p>
                </div>
                <div class="footer-links">
                    <div class="footer-column">
                        <h4>Product</h4>
                        <ul>
                            <li><a href="#about-optiplan">About OptiPlan</a></li>
                            <li><a href="#tutorial">Tutorial</a></li>
                            <li><a href="#faq">FAQ</a></li>
                            <li><a href="#feedbacks">Testimonials</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Resources</h4>
                        <ul>
                            <li><a href="#about-me">About Creator</a></li>
                            <li><a href="#">Documentation</a></li>
                            <li><a href="#">Help Center</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Legal</h4>
                        <ul>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">Terms of Service</a></li>
                            <li><a href="#">Cookie Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="footer-copyright">&copy; 2026 OptiPlan. All rights reserved.</p>
                <div class="footer-social">
                    <a href="#" aria-label="Twitter">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="LinkedIn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.5 2h-17A1.5 1.5 0 002 3.5v17A1.5 1.5 0 003.5 22h17a1.5 1.5 0 001.5-1.5v-17A1.5 1.5 0 0020.5 2zM8 19H5v-9h3zM6.5 8.25A1.75 1.75 0 118.3 6.5a1.78 1.78 0 01-1.8 1.75zM19 19h-3v-4.74c0-1.42-.6-1.93-1.38-1.93A1.74 1.74 0 0013 14.19a.66.66 0 000 .14V19h-3v-9h2.9v1.3a3.11 3.11 0 012.7-1.4c1.55 0 3.36.86 3.36 3.66z"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Instagram">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script src="../../JavaScript/script.js"></script>
</body>

// Add this at the very bottom of your PHP file, right before </body>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('userMenuTrigger');
    const menu = document.getElementById('userMenu');

    if (trigger && menu) {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            menu.classList.toggle('active');
            console.log("Menu toggled! Current classes:", menu.className); // Check your console (F12)
        });
    }

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (menu && !menu.contains(e.target)) {
            menu.classList.remove('active');
        }
    });
});
</script>
</html>