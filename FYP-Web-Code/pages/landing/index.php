<?php
require_once __DIR__ . '/../../includes/security.php';
configure_secure_session();
session_start();
require_once __DIR__ . '/../../includes/db.php';
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['name'] ?? '';
$userRole = $_SESSION['role'] ?? 'user';
$userPfp = $_SESSION['pfp_path'] ?? '';

// Load site content from DB
$_siteContent = [];
try {
    $stmt = $pdo->query("SELECT section_key, content_value FROM site_content");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_siteContent[$row['section_key']] = $row['content_value'];
    }
} catch (PDOException $e) {
    // Silently fail — defaults will be used
}

function getContent($key, $default = '') {
    global $_siteContent;
    return htmlspecialchars($_siteContent[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}

// getContentRaw: Only for emoji/special chars that are trusted defaults.
// Still escapes HTML to prevent stored XSS from CMS injection.
function getContentRaw($key, $default = '') {
    global $_siteContent;
    return htmlspecialchars($_siteContent[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiPlan — AI-Powered Productivity Dashboard for Students</title>
    <meta name="description" content="OptiPlan unifies schedule management, study tracking, and budget planning into one AI-powered dashboard built for students and young professionals.">
    <meta name="keywords" content="productivity dashboard, student planner, AI scheduling, budget tracker, study planner, OptiPlan">
    <meta name="author" content="OptiPlan">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://optiplan.com/">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="OptiPlan — AI-Powered Productivity Dashboard for Students">
    <meta property="og:description" content="Stop switching between apps. OptiPlan unifies scheduling, study tracking, and budgeting into one intelligent platform for students.">
    <meta property="og:url" content="https://optiplan.com/">
    <meta property="og:site_name" content="OptiPlan">
    <meta property="og:image" content="https://optiplan.com/assets/img/og-preview.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="en_US">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="OptiPlan — AI-Powered Productivity Dashboard for Students">
    <meta name="twitter:description" content="One dashboard for scheduling, studying, and budgeting. Built with AI for students and young professionals.">
    <meta name="twitter:image" content="https://optiplan.com/assets/img/og-preview.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

    <!-- Structured Data: WebApplication -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "OptiPlan",
        "url": "https://optiplan.com",
        "description": "AI-powered productivity dashboard that unifies scheduling, study tracking, and budget management for students.",
        "applicationCategory": "ProductivityApplication",
        "operatingSystem": "Web",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "USD"
        }
    }
    </script>

    <!-- Structured Data: FAQ -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            {
                "@type": "Question",
                "name": "What makes OptiPlan different from other productivity apps?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "OptiPlan is the only platform that integrates scheduling, study support, and budgeting into one unified dashboard. While other apps focus on just one area, OptiPlan connects all aspects of student life with AI-powered insights."
                }
            },
            {
                "@type": "Question",
                "name": "Is OptiPlan free to use?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes! OptiPlan offers a free tier with core features. Premium features like advanced AI insights and unlimited integrations are available with a student subscription."
                }
            },
            {
                "@type": "Question",
                "name": "Can I sync OptiPlan with my university calendar?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Absolutely! OptiPlan supports calendar imports from most university systems and can automatically sync your class schedules, assignment deadlines, and exam dates."
                }
            },
            {
                "@type": "Question",
                "name": "How does the AI-powered scheduling work?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Our AI learns from your behavior patterns and preferences to suggest optimal time slots for tasks, detect scheduling conflicts, and recommend breaks. The more you use OptiPlan, the smarter it becomes."
                }
            },
            {
                "@type": "Question",
                "name": "Is my data secure?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes! We use industry-standard encryption to protect your data. Your information is private, never sold to third parties, and you can export or delete it at any time."
                }
            }
        ]
    }
    </script>

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
    #userMenu {
        position: relative;
    }

    #userDropdown {
        display: none;
        position: absolute;
        right: 0;
        background: #1a1a2e;
        border: 1px solid #333;
        z-index: 10000;
    }

    #userMenu.active #userDropdown {
        display: block !important;
    }

    .dropdown-item svg {
        width: 18px !important;
        height: 18px !important;
    }
    </style>
</head>

<body>
    <!-- Fixed Header Navigation -->
    <header class="header" id="header">
        <nav class="nav-container" aria-label="Main navigation">
            <!-- Logo Section -->
            <div class="logo">
                <a href="#top" class="logo-link">
                    <svg class="logo-icon" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M20 5L35 12.5V27.5L20 35L5 27.5V12.5L20 5Z" stroke="currentColor" stroke-width="2"
                            stroke-linejoin="round" />
                        <circle cx="20" cy="20" r="6" fill="currentColor" />
                    </svg>
                    <span class="logo-text">OptiPlan</span>
                </a>
            </div>

            <!-- Center Navigation — 3 items -->
            <ul class="nav-links">
                <!-- 1. Explore dropdown -->
                <li class="nav-item dropdown">
                    <button class="nav-link dropdown-trigger" aria-expanded="false" aria-haspopup="true">
                        Explore
                        <svg class="dropdown-icon" width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#problem" class="dropdown-link">The Problems</a></li>
                        <li><a href="#features" class="dropdown-link">Features</a></li>
                        <li><a href="#roadmap" class="dropdown-link">Roadmap</a></li>
                    </ul>
                </li>

                <!-- 2. Support dropdown -->
                <li class="nav-item dropdown">
                    <button class="nav-link dropdown-trigger" aria-expanded="false" aria-haspopup="true">
                        Support
                        <svg class="dropdown-icon" width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#tutorial" class="dropdown-link">Tutorial Video</a></li>
                        <li><a href="#faq" class="dropdown-link">FAQ</a></li>
                        <li><a href="#form" class="dropdown-link">Feedback Form</a></li>
                    </ul>
                </li>

                <!-- 3. About — direct link -->
                <li class="nav-item">
                    <a href="#about-optiplan" class="nav-link">About</a>
                </li>
            </ul>

            <!-- Right Action Buttons -->
            <div class="nav-actions">
                <?php if ($isLoggedIn): ?>
                    <!-- Logged In: User Menu with Dropdown -->
                    <div class="user-menu" id="userMenu">
                        <button class="user-menu-trigger" id="userMenuTrigger" type="button">
                            <?php if ($userPfp && file_exists('../../' . $userPfp)): ?>
                                <img class="user-pfp" src="../../<?php echo htmlspecialchars($userPfp); ?>" alt="<?php echo htmlspecialchars($userName); ?>'s profile picture" width="32" height="32">
                            <?php else: ?>
                                <svg class="user-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            <?php endif; ?>

                            <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>

                            <svg class="chevron-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>

                        <div class="user-dropdown" id="userDropdown">
                            <a href="<?php echo ($userRole === 'admin') ? '../admin/admin.php' : '../dashboard/dashboard.php'; ?>"
                                class="dropdown-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                                <span>Dashboard</span> </a>
                            <a href="../settings/settings.php" class="dropdown-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path
                                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                                    </path>
                                </svg>
                                <span>Settings</span> </a>
                            <div class="dropdown-divider"></div>
                            <a href="../auth/logout.php" class="dropdown-item logout">
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
                    <a href="../auth/login.php" class="btn-link">Log In</a>
                    <a href="../auth/signup.php" class="btn-primary">Try Now</a>
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
        <section class="hero" id="top" style="position:relative;overflow:hidden;">
            <!-- CPPN Neural Network Shader Background -->
            <div class="hero-shader-bg" id="heroShaderBg" aria-hidden="true" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;overflow:hidden;"></div>
            <div class="hero-shader-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;pointer-events:none;background:linear-gradient(to top,rgba(0,0,0,0.35),transparent 50%,rgba(0,0,0,0.2));"></div>
            <div class="hero-content" style="position:relative;z-index:2;">
                <div class="hero-text">
                    <span class="hero-badge"><?php echo getContent('hero_badge', 'AI-Powered Productivity'); ?></span>
                    <h1 class="hero-title">
                        <?php echo getContent('hero_title_line1', 'One Dashboard.'); ?><br>
                        <span class="cycling-wrapper">
                            <span class="cycling-text gradient-text" id="cyclingText">Everything</span>
                        </span><br>
                        <?php echo getContent('hero_title_line2', 'Organized.'); ?>
                    </h1>
                    <p class="hero-description">
                        <?php echo getContent('hero_description', 'Fed up with app clutter? OptiPlan merges your schedules, studies, and finances into one smart AI dashboard crafted for students and young interns.'); ?>
                    </p>
                    <div class="hero-actions">
                        <?php if ($isLoggedIn): ?>
                            <a href="<?php echo ($userRole === 'admin') ? '../admin/admin.php' : '../dashboard/dashboard.php'; ?>"
                                class="btn-hero-primary">Go to Dashboard</a>
                        <?php else: ?>
                            <a href="../auth/signup.php" class="btn-hero-primary"><?php echo getContent('hero_cta_primary', 'Get Started Free'); ?></a>
                        <?php endif; ?>
                        <a href="#tutorial" class="btn-hero-secondary">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <circle cx="10" cy="10" r="9" stroke="currentColor" stroke-width="1.5" />
                                <path d="M8 6L14 10L8 14V6Z" fill="currentColor" />
                            </svg>
                            <?php echo getContent('hero_cta_secondary', 'Watch Demo'); ?>
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo getContent('hero_stat1_number', '75%+'); ?></span>
                            <span class="stat-label"><?php echo getContent('hero_stat1_label', 'Students use 3+ apps daily'); ?></span>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo getContent('hero_stat2_number', '1'); ?></span>
                            <span class="stat-label"><?php echo getContent('hero_stat2_label', 'Platform solves it all'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="dashboard-preview">
                        <article class="preview-card card-1">
                            <div class="card-icon" aria-hidden="true"><?php echo getContentRaw('hero_card1_icon', '📅'); ?></div>
                            <div class="card-content">
                                <p class="card-title"><?php echo getContent('hero_card1_title', 'Smart Scheduling'); ?></p>
                                <p><?php echo getContent('hero_card1_desc', 'AI Optimizes Your Calendar'); ?></p>
                            </div>
                        </article>
                        <article class="preview-card card-2">
                            <div class="card-icon" aria-hidden="true"><?php echo getContentRaw('hero_card2_icon', '📚'); ?></div>
                            <div class="card-content">
                                <p class="card-title"><?php echo getContent('hero_card2_title', 'Study Notes'); ?></p>
                                <p><?php echo getContent('hero_card2_desc', 'Create Personalized Flip Cards'); ?></p>
                            </div>
                        </article>
                        <article class="preview-card card-3">
                            <div class="card-icon" aria-hidden="true"><?php echo getContentRaw('hero_card3_icon', '💰'); ?></div>
                            <div class="card-content">
                                <p class="card-title"><?php echo getContent('hero_card3_title', 'Budget Tracking'); ?></p>
                                <p><?php echo getContent('hero_card3_desc', 'Gain Financial Awareness Easily'); ?></p>
                            </div>
                        </article>
                        <article class="preview-card card-4">
                            <div class="card-icon" aria-hidden="true"><?php echo getContentRaw('hero_card4_icon', '🔜'); ?></div>
                            <div class="card-content">
                                <p class="card-title"><?php echo getContent('hero_card4_title', 'Many More'); ?></p>
                                <p><?php echo getContent('hero_card4_desc', 'Discover Updates Coming Soon'); ?></p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- Problem Statement Section -->
        <section class="section problem-section" id="problem">
            <div class="container">
                <div class="section-header">
                    <span class="section-label"><?php echo getContent('problem_label', 'The Problem'); ?></span>
                    <h2 class="section-title"><?php echo getContent('problem_title', 'Fragmented Productivity is Killing Your Time'); ?></h2>
                </div>
                <div class="problem-grid">
                    <article class="problem-card">
                        <div class="problem-number" aria-hidden="true"><?php echo getContent('problem_card1_number', '01'); ?></div>
                        <h3><?php echo getContent('problem_card1_title', 'App Overload'); ?></h3>
                        <p><?php echo getContent('problem_card1_desc', 'Endless switching among calendar apps, study tools, and budget trackers drains hours from student productivity and intern focus every day.'); ?></p>
                    </article>
                    <article class="problem-card">
                        <div class="problem-number" aria-hidden="true"><?php echo getContent('problem_card2_number', '02'); ?></div>
                        <h3><?php echo getContent('problem_card2_title', 'Missed Tasks'); ?></h3>
                        <p><?php echo getContent('problem_card2_desc', 'Critical deadlines and key activities slip away when spread over disjointed platforms, hurting student success and intern performance.'); ?></p>
                    </article>
                    <article class="problem-card">
                        <div class="problem-number" aria-hidden="true"><?php echo getContent('problem_card3_number', '03'); ?></div>
                        <h3><?php echo getContent('problem_card3_title', 'No Integration'); ?></h3>
                        <p><?php echo getContent('problem_card3_desc', "Schedules fail to connect with budgets. Study plans remain out of sync with calendars. All elements operate in isolated silos, reducing overall efficiency."); ?></p>
                    </article>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="section features-section" id="features">
            <div class="container">
                <div class="section-header centered">
                    <span class="section-label"><?php echo getContent('feature_label', 'Features'); ?></span>
                    <h2 class="section-title"><?php echo getContent('feature_title', 'Three Tools. One Platform. Zero Hassle.'); ?></h2>
                    <p class="section-description"><?php echo getContent('feature_desc', 'Everything you need to stay organized, productive, and financially aware.'); ?></p>
                </div>
                <div class="features-grid">
                    <!-- Feature Card 1 -->
                    <article class="feature-card flip-card">
                        <div class="flip-card-inner">
                            <!-- Front -->
                            <div class="flip-card-front">
                                <div class="feature-icon">
                                    <svg viewBox="0 0 48 48" fill="none">
                                        <rect x="8" y="12" width="32" height="28" rx="2" stroke="currentColor"
                                            stroke-width="2" />
                                        <path d="M8 18H40" stroke="currentColor" stroke-width="2" />
                                        <circle cx="18" cy="26" r="2" fill="currentColor" />
                                        <circle cx="24" cy="26" r="2" fill="currentColor" />
                                        <circle cx="30" cy="26" r="2" fill="currentColor" />
                                    </svg>
                                </div>
                                <h3 class="feature-title">Smart Scheduling</h3>
                                <p class="feature-description">AI-powered calendar that learns your habits and suggests
                                    optimal time slots for tasks, classes, and breaks.</p>
                                <ul class="feature-list">
                                    <li>Automatic conflict detection</li>
                                    <li>Smart time blocking</li>
                                    <li>Integration with university schedules</li>
                                </ul>
                            </div>
                            <!-- Back -->
                            <div class="flip-card-back">
                                <h3 class="feature-title">Smart Scheduling Plus</h3>
                                <p class="feature-description">Advanced analytics and suggestions for peak productivity
                                    and time optimization.</p>
                                <ul class="feature-list">
                                    <li>Priority task recommendations</li>
                                    <li>AI-generated daily agenda</li>
                                    <li>Integration with multiple calendars</li>
                                </ul>
                            </div>
                        </div>
                    </article>

                    <!-- Feature Card 2 (Featured) -->
                    <article class="feature-card featured flip-card">
                        <div class="flip-card-inner">
                            <!-- Front -->
                            <div class="flip-card-front">
                                <div class="featured-badge">Most Popular</div>
                                <div class="feature-icon">
                                    <svg viewBox="0 0 48 48" fill="none">
                                        <path
                                            d="M14 8H34C36.2091 8 38 9.79086 38 12V38C38 40.2091 36.2091 42 34 42H14C11.7909 42 10 40.2091 10 38V12C10 9.79086 11.7909 8 14 8Z"
                                            stroke="currentColor" stroke-width="2" />
                                        <path d="M18 16H30M18 24H30M18 32H26" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" />
                                    </svg>
                                </div>
                                <h3 class="feature-title">Study Notes</h3>
                                <p class="feature-description">Personalized study plans, progress tracking, and
                                    AI-powered insights to improve your academic performance.</p>
                                <ul class="feature-list">
                                    <li>Custom study schedules</li>
                                    <li>Progress analytics</li>
                                    <li>Smart revision reminders</li>
                                </ul>
                            </div>
                            <!-- Back -->
                            <div class="flip-card-back">
                                <h3 class="feature-title">Study Support Plus</h3>
                                <p class="feature-description">AI tutors and collaborative study tools for maximum
                                    learning efficiency.</p>
                                <ul class="feature-list">
                                    <li>AI-generated practice quizzes</li>
                                    <li>Peer study group recommendations</li>
                                    <li>Real-time progress feedback</li>
                                </ul>
                            </div>
                        </div>
                    </article>

                    <!-- Feature Card 3 -->
                    <article class="feature-card flip-card">
                        <div class="flip-card-inner">
                            <!-- Front -->
                            <div class="flip-card-front">
                                <div class="feature-icon">
                                    <svg viewBox="0 0 48 48" fill="none">
                                        <circle cx="24" cy="24" r="16" stroke="currentColor" stroke-width="2" />
                                        <path d="M24 24L24 12" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" />
                                        <path d="M24 24L32 28" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" />
                                    </svg>
                                </div>
                                <h3 class="feature-title">Budget Tracking</h3>
                                <p class="feature-description">Track expenses, set savings goals, and gain financial
                                    awareness with intuitive budgeting tools.</p>
                                <ul class="feature-list">
                                    <li>Expense categorization</li>
                                    <li>Visual spending insights</li>
                                    <li>Savings goal tracking</li>
                                </ul>
                            </div>
                            <!-- Back -->
                            <div class="flip-card-back">
                                <h3 class="feature-title">Budget Tracking Plus</h3>
                                <p class="feature-description">Detailed financial insights and automated saving
                                    recommendations.</p>
                                <ul class="feature-list">
                                    <li>Spending trend analysis</li>
                                    <li>Automated savings suggestions</li>
                                    <li>Custom alerts for overspending</li>
                                </ul>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- Growth Roadmap Section -->
        <section class="section roadmap-section" id="roadmap">
            <div class="container">
                <div class="section-header centered">
                    <span class="section-label"><?php echo getContent('roadmap_label', 'Roadmap'); ?></span>
                    <h2 class="section-title"><?php echo getContent('roadmap_title', 'Our Growth Journey'); ?></h2>
                    <p class="section-description"><?php echo getContent('roadmap_desc', 'From concept to a fully intelligent productivity platform — hover each milestone to explore the details.'); ?></p>
                </div>

                <div class="roadmap-road-wrap">
                    <!-- Thin winding SVG line -->
                    <svg class="roadmap-svg" viewBox="0 0 1200 400" preserveAspectRatio="xMidYMid meet" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="lineGrad" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0%" stop-color="#34d399"/>
                                <stop offset="55%" stop-color="#a78bfa"/>
                                <stop offset="100%" stop-color="#3d3355"/>
                            </linearGradient>
                            <filter id="lineGlow">
                                <feGaussianBlur in="SourceGraphic" stdDeviation="4" result="glow"/>
                                <feMerge>
                                    <feMergeNode in="glow"/>
                                    <feMergeNode in="SourceGraphic"/>
                                </feMerge>
                            </filter>
                        </defs>

                        <!-- Soft glow behind the line -->
                        <path d="M-100,300 C0,300 0,100 100,100 C200,100 200,300 300,300 C400,300 400,100 500,100 C600,100 600,300 700,300 C800,300 800,100 900,100 C1000,100 1000,300 1100,300 C1200,300 1200,100 1300,100" stroke="url(#lineGrad)" stroke-width="6" stroke-linecap="round" fill="none" opacity="0.25" filter="url(#lineGlow)"/>

                        <!-- Main thin line -->
                        <path d="M-100,300 C0,300 0,100 100,100 C200,100 200,300 300,300 C400,300 400,100 500,100 C600,100 600,300 700,300 C800,300 800,100 900,100 C1000,100 1000,300 1100,300 C1200,300 1200,100 1300,100" stroke="url(#lineGrad)" stroke-width="3" stroke-linecap="round" fill="none"/>
                    </svg>

                    <!-- Icon nodes positioned ON the line -->
                    <div class="roadmap-pins">

                        <!-- Node 1 — Foundation Launch -->
                        <div class="rm-pin rm-pin-1 card-below" data-status="completed">
                            <div class="rm-card">
                                <span class="rm-badge completed">Completed</span>
                                <h3>Foundation Launch</h3>
                                <p>Core authentication, unified dashboard layout, and the design system that powers OptiPlan.</p>
                            </div>
                            <div class="rm-node">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M12 2L2 7l10 5 10-5-10-5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 17l10 5 10-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 12l10 5 10-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>

                        <!-- Node 2 — Smart Scheduler -->
                        <div class="rm-pin rm-pin-2 card-above" data-status="completed">
                            <div class="rm-card">
                                <span class="rm-badge completed">Completed</span>
                                <h3>Smart Scheduler</h3>
                                <p>Interactive calendar with task management, deadline tracking, and keyboard shortcuts for power users.</p>
                            </div>
                            <div class="rm-node">
                                <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/><line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/></svg>
                            </div>
                        </div>

                        <!-- Node 3 — Finance Tracker -->
                        <div class="rm-pin rm-pin-3 card-below" data-status="completed">
                            <div class="rm-card">
                                <span class="rm-badge completed">Completed</span>
                                <h3>Finance Tracker</h3>
                                <p>Budget management with income/expense categorization, visual breakdowns, and spending insights.</p>
                            </div>
                            <div class="rm-node">
                                <svg viewBox="0 0 24 24" fill="none"><line x1="12" y1="1" x2="12" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>

                        <!-- Node 4 — AI Chatbot -->
                        <div class="rm-pin rm-pin-4 card-above" data-status="in-dev">
                            <div class="rm-card">
                                <span class="rm-badge in-dev">In Development</span>
                                <h3>AI Chatbot Assistant</h3>
                                <p>Context-aware AI that understands your schedule, finances, and study habits to give personalized advice.</p>
                            </div>
                            <div class="rm-node">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>

                        <!-- Node 5 — Study Analytics -->
                        <div class="rm-pin rm-pin-5 card-below" data-status="future">
                            <div class="rm-card">
                                <span class="rm-badge future">Future Innovation</span>
                                <h3>Study Analytics</h3>
                                <p>Deep learning insights into study patterns, focus duration tracking, and AI-generated study plans.</p>
                            </div>
                            <div class="rm-node">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>

                        <!-- Node 6 — Cross-Platform Sync -->
                        <div class="rm-pin rm-pin-6 card-above" data-status="future">
                            <div class="rm-card">
                                <span class="rm-badge future">Future Innovation</span>
                                <h3>Cross-Platform Sync</h3>
                                <p>Seamless synchronization across devices with offline support and real-time collaboration features.</p>
                            </div>
                            <div class="rm-node">
                                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><line x1="2" y1="12" x2="22" y2="12" stroke="currentColor" stroke-width="2"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" stroke="currentColor" stroke-width="2"/></svg>
                            </div>
                        </div>

                    </div><!-- /.roadmap-pins -->
                </div><!-- /.roadmap-road-wrap -->

            </div>
        </section>

        <!-- Tutorial Video Section -->
        <section class="section tutorial-section" id="tutorial">
            <div class="container">
                <div class="section-header centered">
                    <span class="section-label"><?php echo getContent('tutorial_label', 'Tutorial'); ?></span>
                    <h2 class="section-title"><?php echo getContent('tutorial_title', 'See OptiPlan in Action'); ?></h2>
                    <p class="section-description"><?php echo getContent('tutorial_desc', 'Watch a quick walkthrough of how OptiPlan can transform your productivity.'); ?></p>
                </div>
                <div class="video-container">
                    <div class="mac-window">
                        <!-- macOS title bar -->
                        <div class="mac-titlebar">
                            <div class="mac-dots">
                                <span class="mac-dot mac-dot--close"></span>
                                <span class="mac-dot mac-dot--minimize"></span>
                                <span class="mac-dot mac-dot--maximize"></span>
                            </div>
                            <span class="mac-title">OptiPlan — Tutorial</span>
                        </div>
                        <!-- Video area -->
                        <div class="video-wrapper">
                            <div class="video-placeholder" role="button" aria-label="Play OptiPlan tutorial video" tabindex="0">
                                <svg class="play-icon" viewBox="0 0 80 80" fill="none" aria-hidden="true">
                                    <circle cx="40" cy="40" r="38" stroke="currentColor" stroke-width="2" />
                                    <path d="M32 26L54 40L32 54V26Z" fill="currentColor" />
                                </svg>
                                <p>Click to play tutorial video</p>
                            </div>
                            <!-- Replace with: <iframe src="YOUR_VIDEO_URL" frameborder="0" allowfullscreen></iframe> -->
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="section faq-section" id="faq">
            <div class="container">
                <div class="section-header centered">
                    <span class="section-label"><?php echo getContent('faq_label', 'FAQ'); ?></span>
                    <h2 class="section-title"><?php echo getContent('faq_title', 'Frequently Asked Questions'); ?></h2>
                </div>
                <div class="faq-list">
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            <span><?php echo getContent('faq1_question', 'What makes OptiPlan different from other productivity apps?'); ?></span>
                            <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo getContent('faq1_answer', 'OptiPlan is the only platform that integrates scheduling, study support, and budgeting into one unified dashboard. While other apps focus on just one area, OptiPlan connects all aspects of student life with AI-powered insights.'); ?></p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            <span><?php echo getContent('faq2_question', 'Is OptiPlan free to use?'); ?></span>
                            <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo getContent('faq2_answer', 'Yes! OptiPlan offers a free tier with core features. Premium features like advanced AI insights and unlimited integrations are available with a student subscription.'); ?></p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            <span><?php echo getContent('faq3_question', 'Can I sync OptiPlan with my university calendar?'); ?></span>
                            <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo getContent('faq3_answer', 'Absolutely! OptiPlan supports calendar imports from most university systems and can automatically sync your class schedules, assignment deadlines, and exam dates.'); ?></p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            <span><?php echo getContent('faq4_question', 'How does the AI-powered scheduling work?'); ?></span>
                            <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo getContent('faq4_answer', 'Our AI learns from your behavior patterns and preferences to suggest optimal time slots for tasks, detect scheduling conflicts, and recommend breaks. The more you use OptiPlan, the smarter it becomes.'); ?></p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            <span><?php echo getContent('faq5_question', 'Is my data secure?'); ?></span>
                            <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo getContent('faq5_answer', 'Yes! We use industry-standard encryption to protect your data. Your information is private, never sold to third parties, and you can export or delete it at any time.'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FeedbackForm Section -->
        <section class="section feedback-form-section" id="form">
            <div class="container">
                <div class="section-header centered">
                    <span class="section-label"><?php echo getContent('feedback_label', 'Contact Us'); ?></span>
                    <h2 class="section-title"><?php echo getContent('feedback_title', 'We Value Your Feedback'); ?></h2>
                    <p class="section-description"><?php echo getContent('feedback_desc', 'Have a suggestion or found a bug? Let us know how we can improve OptiPlan for you.'); ?></p>
                </div>

                <div class="form-wrapper">
                    <?php if ($isLoggedIn): ?>
                    <form class="contact-form" id="feedbackForm" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userName); ?>" readonly style="opacity:0.7;cursor:not-allowed;">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" readonly style="opacity:0.7;cursor:not-allowed;">
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
                                <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="5" placeholder="Tell us what you think..."
                                required></textarea>
                        </div>

                        <button type="submit" class="btn-primary btn-submit">
                            Send Feedback
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="feedback-login-prompt" style="text-align:center;padding:3rem 2rem;">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--primary-purple);margin-bottom:1rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <h3 style="color:var(--white);font-size:1.25rem;margin-bottom:0.5rem;">Login Required</h3>
                        <p style="color:var(--gray-400);margin-bottom:1.5rem;">You need to be logged in to submit feedback.</p>
                        <a href="../auth/login.php" class="btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 2rem;text-decoration:none;">
                            Sign In to Continue
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Feedbacks Section -->
        <section class="section feedbacks-section" id="feedbacks">
            <div class="container">
                <div class="section-header centered">
                    <span class="section-label"><?php echo getContent('testimonial_label', 'Testimonials'); ?></span>
                    <h2 class="section-title"><?php echo getContent('testimonial_title', 'What Students Are Saying'); ?></h2>
                </div>
                <div class="feedback-grid">
                    <article class="feedback-card">
                        <div class="feedback-rating">⭐⭐⭐⭐⭐</div>
                        <p class="feedback-text">
                            "<?php echo getContent('testimonial1_text', 'OptiPlan changed how I manage my time. I went from constantly stressed to actually having free time. The budget tracker alone saved me hundreds this semester!'); ?>"
                        </p>
                        <div class="feedback-author">
                            <div class="author-avatar"><?php echo getContent('testimonial1_initials', 'SM'); ?></div>
                            <div class="author-info">
                                <div class="author-name"><?php echo getContent('testimonial1_name', 'Sarah Martinez'); ?></div>
                                <div class="author-role"><?php echo getContent('testimonial1_role', 'Computer Science, Year 3'); ?></div>
                            </div>
                        </div>
                    </article>
                    <article class="feedback-card">
                        <div class="feedback-rating">⭐⭐⭐⭐⭐</div>
                        <p class="feedback-text">
                            "<?php echo getContent('testimonial2_text', "Finally, an app that gets student life. The AI scheduling is scary accurate at predicting when I need breaks. My grades improved and I'm less stressed."); ?>"
                        </p>
                        <div class="feedback-author">
                            <div class="author-avatar"><?php echo getContent('testimonial2_initials', 'JC'); ?></div>
                            <div class="author-info">
                                <div class="author-name"><?php echo getContent('testimonial2_name', 'James Chen'); ?></div>
                                <div class="author-role"><?php echo getContent('testimonial2_role', 'Business Admin, Year 2'); ?></div>
                            </div>
                        </div>
                    </article>
                    <article class="feedback-card">
                        <div class="feedback-rating">⭐⭐⭐⭐⭐</div>
                        <p class="feedback-text">
                            "<?php echo getContent('testimonial3_text', 'I used to juggle 5 different apps. Now everything is in one place. The study planner helped me ace my finals, and the interface is actually beautiful.'); ?>"
                        </p>
                        <div class="feedback-author">
                            <div class="author-avatar"><?php echo getContent('testimonial3_initials', 'EP'); ?></div>
                            <div class="author-info">
                                <div class="author-name"><?php echo getContent('testimonial3_name', 'Emma Park'); ?></div>
                                <div class="author-role"><?php echo getContent('testimonial3_role', 'Psychology, Year 4'); ?></div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- About OptiPlan Section -->
        <section class="section about-section" id="about-me">
            <div class="container">
                <div class="about-me-content">
                    <span class="section-label"><?php echo getContent('about_creator_label', 'About the Creator'); ?></span>
                    <h2 class="section-title"><?php echo getContent('about_creator_title', 'Built by a Student, For Students'); ?></h2>
                    <p class="about-me-text">
                        <?php echo getContent('about_creator_p1', "Hi! I am a student who had experienced firsthand the frustration of juggling multiple apps just to organize scheduling, studying and budeeting. After almost missing too many assignments and project deadlines, I realized I needed a better solution. That's when OptiPlan was born."); ?>
                    </p>
                    <p class="about-me-text">
                        <?php echo getContent('about_creator_p2', "OptiPlan is my final year project and a passion project aimed at making student's life more manageable. I hope it helps you as much as it's helpde me."); ?>
                    </p>
                </div>
            </div>
        </section>

        <!-- About OptiPlan Section -->
        <section class="section about-me-section" id="about-optiplan">
            <div class="container">
                <div class="about-layout">
                    <div class="about-visual">
                        <div class="about-card">
                            <div class="about-stat">
                                <span class="about-stat-number"><?php echo getContent('about_optiplan_stat_number', '3-in-1'); ?></span>
                                <span class="about-stat-label"><?php echo getContent('about_optiplan_stat_label', 'Integrated Platform'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="about-content">
                        <span class="section-label"><?php echo getContent('about_optiplan_label', 'About OptiPlan'); ?></span>
                        <h2 class="section-title"><?php echo getContent('about_optiplan_title', 'Built for Students Who Want More'); ?></h2>
                        <p class="about-text">
                            <?php echo getContent('about_optiplan_p1', "Managing student life shouldn't require a dozen different tools. OptiPlan unifies the essential pillars of your day—scheduling, study management, and financial health—into one streamlined interface."); ?>
                        </p>
                        <p class="about-text">
                            <?php echo getContent('about_optiplan_p2', 'Our technology is designed to be unobtrusive yet impactful, adapting to the nuances of your schedule to provide actionable insights. From deadline management to budget tracking, OptiPlan ensures your most important data is always in sync, allowing you to focus on what truly matters.'); ?>
                        </p>
                        <div class="about-features">
                            <div class="about-feature-item">
                                <svg class="check-icon" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                                    <path d="M8 12L11 15L16 9" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>AI-Powered Intelligence</span>
                            </div>
                            <div class="about-feature-item">
                                <svg class="check-icon" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                                    <path d="M8 12L11 15L16 9" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>Unified Dashboard</span>
                            </div>
                            <div class="about-feature-item">
                                <svg class="check-icon" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                                    <path d="M8 12L11 15L16 9" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>Student-First Design</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="footer">
            <div class="footer-container">
                <div class="footer-main">
                    <div class="footer-brand">
                        <div class="footer-logo">
                            <svg class="logo-icon" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M20 5L35 12.5V27.5L20 35L5 27.5V12.5L20 5Z" stroke="currentColor"
                                    stroke-width="2" stroke-linejoin="round" />
                                <circle cx="20" cy="20" r="6" fill="currentColor" />
                            </svg>
                            <span>OptiPlan</span>
                        </div>
                        <p class="footer-tagline"><?php echo getContent('footer_tagline', 'Your all-in-one AI-powered productivity dashboard for students and young professionals.'); ?></p>
                    </div>
                    <div class="footer-links">
                        <div class="footer-column">
                            <h3>Product</h3>
                            <ul>
                                <li><a href="#about-optiplan">About OptiPlan</a></li>
                                <li><a href="#tutorial">Tutorial</a></li>
                                <li><a href="#faq">FAQ</a></li>
                                <li><a href="#feedbacks">Testimonials</a></li>
                            </ul>
                        </div>
                        <div class="footer-column">
                            <h3>Resources</h3>
                            <ul>
                                <li><a href="#about-me">About Creator</a></li>
                                <li><a href="#">Documentation</a></li>
                                <li><a href="#">Help Center</a></li>
                                <li><a href="#">Contact</a></li>
                            </ul>
                        </div>
                        <div class="footer-column">
                            <h3>Legal</h3>
                            <ul>
                                <li><a href="#">Privacy Policy</a></li>
                                <li><a href="#">Terms of Service</a></li>
                                <li><a href="#">Cookie Policy</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p class="footer-copyright"><?php echo getContent('footer_copyright', '© 2026 OptiPlan. All rights reserved.'); ?></p>
                    <div class="footer-social">
                        <a href="#" aria-label="Twitter">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                        <a href="#" aria-label="LinkedIn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M20.5 2h-17A1.5 1.5 0 002 3.5v17A1.5 1.5 0 003.5 22h17a1.5 1.5 0 001.5-1.5v-17A1.5 1.5 0 0020.5 2zM8 19H5v-9h3zM6.5 8.25A1.75 1.75 0 118.3 6.5a1.78 1.78 0 01-1.8 1.75zM19 19h-3v-4.74c0-1.42-.6-1.93-1.38-1.93A1.74 1.74 0 0013 14.19a.66.66 0 000 .14V19h-3v-9h2.9v1.3a3.11 3.11 0 012.7-1.4c1.55 0 3.36.86 3.36 3.66z" />
                            </svg>
                        </a>
                        <a href="#" aria-label="Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>

        <script src="script.js"></script>

        <!-- Feedback Form AJAX Submit -->
        <script>
        (function() {
            var form = document.getElementById('feedbackForm');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var btn = form.querySelector('.btn-submit');
                var origText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = 'Sending...';

                fetch('../../api/submit_feedback.php', {
                    method: 'POST',
                    body: new FormData(form)
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        form.reset();
                        btn.innerHTML = 'Sent!';
                        setTimeout(function() { btn.innerHTML = origText; btn.disabled = false; }, 2500);
                    } else {
                        if (data.require_login) {
                            window.location.href = '../auth/login.php';
                        } else {
                            alert(data.message || 'Something went wrong.');
                        }
                        btn.innerHTML = origText;
                        btn.disabled = false;
                    }
                })
                .catch(function() {
                    alert('Network error. Please try again.');
                    btn.innerHTML = origText;
                    btn.disabled = false;
                });
            });
        })();
        </script>

        <!-- Cycling Text Animation -->
        <script>
        (function() {
            var words = ['Everything', 'Anything', 'Always'];
            var el = document.getElementById('cyclingText');
            if (!el) return;
            var index = 0;

            setInterval(function() {
                el.classList.remove('slide-in');
                el.classList.add('slide-out');

                el.addEventListener('animationend', function handler() {
                    el.removeEventListener('animationend', handler);
                    index = (index + 1) % words.length;
                    el.textContent = words[index];
                    el.classList.remove('slide-out');
                    el.classList.add('slide-in');
                });
            }, 3000);
        })();
        </script>

        <!-- Three.js for CPPN Shader Background -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
        <script>
        (function() {
            const container = document.getElementById('heroShaderBg');
            if (!container) return;

            const vertexShader = `
                varying vec2 vUv;
                void main() {
                    vUv = uv;
                    gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
                }
            `;

            const fragmentShader = `
                #ifdef GL_ES
                precision lowp float;
                #endif
                uniform float iTime;
                varying vec2 vUv;

                vec4 buf[8];
                vec4 sigmoid(vec4 x) { return 1. / (1. + exp(-x)); }

                vec4 cppn_fn(vec2 coordinate, float in0, float in1, float in2) {
                    buf[6] = vec4(coordinate.x, coordinate.y, 0.3948333106474662 + in0, 0.36 + in1);
                    buf[7] = vec4(0.14 + in2, sqrt(coordinate.x * coordinate.x + coordinate.y * coordinate.y), 0., 0.);
                    buf[0] = mat4(vec4(6.5404263, -3.6126034, 0.7590882, -1.13613), vec4(2.4582713, 3.1660357, 1.2219609, 0.06276096), vec4(-5.478085, -6.159632, 1.8701609, -4.7742867), vec4(6.039214, -5.542865, -0.90925294, 3.251348)) * buf[6]
                    + mat4(vec4(0.8473259, -5.722911, 3.975766, 1.6522468), vec4(-0.24321538, 0.5839259, -1.7661959, -5.350116), vec4(0.0, 0.0, 0.0, 0.0), vec4(0.0, 0.0, 0.0, 0.0)) * buf[7]
                    + vec4(0.21808943, 1.1243913, -1.7969975, 5.0294676);
                    buf[1] = mat4(vec4(-3.3522482, -6.0612736, 0.55641043, -4.4719114), vec4(0.8631464, 1.7432913, 5.643898, 1.6106541), vec4(2.4941394, -3.5012043, 1.7184316, 6.357333), vec4(3.310376, 8.209261, 1.1355612, -1.165539)) * buf[6]
                    + mat4(vec4(5.24046, -13.034365, 0.009859298, 15.870829), vec4(2.987511, 3.129433, -0.89023495, -1.6822904), vec4(0.0, 0.0, 0.0, 0.0), vec4(0.0, 0.0, 0.0, 0.0)) * buf[7]
                    + vec4(-5.9457836, -6.573602, -0.8812491, 1.5436668);
                    buf[0] = sigmoid(buf[0]);
                    buf[1] = sigmoid(buf[1]);
                    buf[2] = mat4(vec4(-15.219568, 8.095543, -2.429353, -1.9381982), vec4(-5.951362, 4.3115187, 2.6393783, 1.274315), vec4(-7.3145227, 6.7297835, 5.2473326, 5.9411426), vec4(5.0796127, 8.979051, -1.7278991, -1.158976)) * buf[6]
                    + mat4(vec4(-11.967154, -11.608155, 6.1486754, 11.237008), vec4(2.124141, -6.263192, -1.7050359, -0.7021966), vec4(0.0, 0.0, 0.0, 0.0), vec4(0.0, 0.0, 0.0, 0.0)) * buf[7]
                    + vec4(-4.17164, -3.2281182, -4.576417, -3.6401186);
                    buf[3] = mat4(vec4(3.1832156, -13.738922, 1.879223, 3.233465), vec4(0.64300746, 12.768129, 1.9141049, 0.50990224), vec4(-0.049295485, 4.4807224, 1.4733979, 1.801449), vec4(5.0039253, 13.000481, 3.3991797, -4.5561905)) * buf[6]
                    + mat4(vec4(-0.1285731, 7.720628, -3.1425676, 4.742367), vec4(0.6393625, 3.714393, -0.8108378, -0.39174938), vec4(0.0, 0.0, 0.0, 0.0), vec4(0.0, 0.0, 0.0, 0.0)) * buf[7]
                    + vec4(-1.1811101, -21.621881, 0.7851888, 1.2329718);
                    buf[2] = sigmoid(buf[2]);
                    buf[3] = sigmoid(buf[3]);
                    buf[4] = mat4(vec4(5.214916, -7.183024, 2.7228765, 2.6592617), vec4(-5.601878, -25.3591, 4.067988, 0.4602802), vec4(-10.57759, 24.286327, 21.102104, 37.546658), vec4(4.3024497, -1.9625226, 2.3458803, -1.372816)) * buf[0]
                    + mat4(vec4(-17.6526, -10.507558, 2.2587414, 12.462782), vec4(6.265566, -502.75443, -12.642513, 0.9112289), vec4(-10.983244, 20.741234, -9.701768, -0.7635988), vec4(5.383626, 1.4819539, -4.1911616, -4.8444734)) * buf[1]
                    + mat4(vec4(12.785233, -16.345072, -0.39901125, 1.7955981), vec4(-30.48365, -1.8345358, 1.4542528, -1.1118771), vec4(19.872723, -7.337935, -42.941723, -98.52709), vec4(8.337645, -2.7312303, -2.2927687, -36.142323)) * buf[2]
                    + mat4(vec4(-16.298317, 3.5471997, -0.44300047, -9.444417), vec4(57.5077, -35.609753, 16.163465, -4.1534753), vec4(-0.07470326, -3.8656476, -7.0901804, 3.1523974), vec4(-12.559385, -7.077619, 1.490437, -0.8211543)) * buf[3]
                    + vec4(-7.67914, 15.927437, 1.3207729, -1.6686112);
                    buf[5] = mat4(vec4(-1.4109162, -0.372762, -3.770383, -21.367174), vec4(-6.2103205, -9.35908, 0.92529047, 8.82561), vec4(11.460242, -22.348068, 13.625772, -18.693201), vec4(-0.3429052, -3.9905605, -2.4626114, -0.45033523)) * buf[0]
                    + mat4(vec4(7.3481627, -4.3661838, -6.3037653, -3.868115), vec4(1.5462853, 6.5488915, 1.9701879, -0.58291394), vec4(6.5858274, -2.2180402, 3.7127688, -1.3730392), vec4(-5.7973905, 10.134961, -2.3395722, -5.965605)) * buf[1]
                    + mat4(vec4(-2.5132585, -6.6685553, -1.4029363, -0.16285264), vec4(-0.37908727, 0.53738135, 4.389061, -1.3024765), vec4(-0.70647055, 2.0111287, -5.1659346, -3.728635), vec4(-13.562562, 10.487719, -0.9173751, -2.6487076)) * buf[2]
                    + mat4(vec4(-8.645013, 6.5546675, -6.3944063, -5.5933375), vec4(-0.57783127, -1.077275, 36.91025, 5.736769), vec4(14.283112, 3.7146652, 7.1452246, -4.5958776), vec4(2.7192075, 3.6021907, -4.366337, -2.3653464)) * buf[3]
                    + vec4(-5.9000807, -4.329569, 1.2427121, 8.59503);
                    buf[4] = sigmoid(buf[4]);
                    buf[5] = sigmoid(buf[5]);
                    buf[6] = mat4(vec4(-1.61102, 0.7970257, 1.4675229, 0.20917463), vec4(-28.793737, -7.1390953, 1.5025433, 4.656581), vec4(-10.94861, 39.66238, 0.74318546, -10.095605), vec4(-0.7229728, -1.5483948, 0.7301322, 2.1687684)) * buf[0]
                    + mat4(vec4(3.2547753, 21.489103, -1.0194173, -3.3100595), vec4(-3.7316632, -3.3792162, -7.223193, -0.23685838), vec4(13.1804495, 0.7916005, 5.338587, 5.687114), vec4(-4.167605, -17.798311, -6.815736, -1.6451967)) * buf[1]
                    + mat4(vec4(0.604885, -7.800309, -7.213122, -2.741014), vec4(-3.522382, -0.12359311, -0.5258442, 0.43852118), vec4(9.6752825, -22.853785, 2.062431, 0.099892326), vec4(-4.3196306, -17.730087, 2.5184598, 5.30267)) * buf[2]
                    + mat4(vec4(-6.545563, -15.790176, -6.0438633, -5.415399), vec4(-43.591583, 28.551912, -16.00161, 18.84728), vec4(4.212382, 8.394307, 3.0958717, 8.657522), vec4(-5.0237565, -4.450633, -4.4768, -5.5010443)) * buf[3]
                    + mat4(vec4(1.6985557, -67.05806, 6.897715, 1.9004834), vec4(1.8680354, 2.3915145, 2.5231109, 4.081538), vec4(11.158006, 1.7294737, 2.0738268, 7.386411), vec4(-4.256034, -306.24686, 8.258898, -17.132736)) * buf[4]
                    + mat4(vec4(1.6889864, -4.5852966, 3.8534803, -6.3482175), vec4(1.3543309, -1.2640043, 9.932754, 2.9079645), vec4(-5.2770967, 0.07150358, -0.13962056, 3.3269649), vec4(28.34703, -4.918278, 6.1044083, 4.085355)) * buf[5]
                    + vec4(6.6818056, 12.522166, -3.7075126, -4.104386);
                    buf[7] = mat4(vec4(-8.265602, -4.7027016, 5.098234, 0.7509808), vec4(8.6507845, -17.15949, 16.51939, -8.884479), vec4(-4.036479, -2.3946867, -2.6055532, -1.9866527), vec4(-2.2167742, -1.8135649, -5.9759874, 4.8846445)) * buf[0]
                    + mat4(vec4(6.7790847, 3.5076547, -2.8191125, -2.7028968), vec4(-5.743024, -0.27844876, 1.4958696, -5.0517144), vec4(13.122226, 15.735168, -2.9397483, -4.101023), vec4(-14.375265, -5.030483, -6.2599335, 2.9848232)) * buf[1]
                    + mat4(vec4(4.0950394, -0.94011575, -5.674733, 4.755022), vec4(4.3809423, 4.8310084, 1.7425908, -3.437416), vec4(2.117492, 0.16342592, -104.56341, 16.949184), vec4(-5.22543, -2.994248, 3.8350096, -1.9364246)) * buf[2]
                    + mat4(vec4(-5.900337, 1.7946124, -13.604192, -3.8060522), vec4(6.6583457, 31.911177, 25.164474, 91.81147), vec4(11.840538, 4.1503043, -0.7314397, 6.768467), vec4(-6.3967767, 4.034772, 6.1714606, -0.32874924)) * buf[3]
                    + mat4(vec4(3.4992442, -196.91893, -8.923708, 2.8142626), vec4(3.4806502, -3.1846354, 5.1725626, 5.1804223), vec4(-2.4009497, 15.585794, 1.2863957, 2.0252278), vec4(-71.25271, -62.441242, -8.138444, 0.50670296)) * buf[4]
                    + mat4(vec4(-12.291733, -11.176166, -7.3474145, 4.390294), vec4(10.805477, 5.6337385, -0.9385842, -4.7348723), vec4(-12.869276, -7.039391, 5.3029537, 7.5436664), vec4(1.4593618, 8.91898, 3.5101583, 5.840625)) * buf[5]
                    + vec4(2.2415268, -6.705987, -0.98861027, -2.117676);
                    buf[6] = sigmoid(buf[6]);
                    buf[7] = sigmoid(buf[7]);
                    buf[0] = mat4(vec4(1.6794263, 1.3817469, 2.9625452, 0.0), vec4(-1.8834411, -1.4806935, -3.5924516, 0.0), vec4(-1.3279216, -1.0918057, -2.3124623, 0.0), vec4(0.2662234, 0.23235129, 0.44178495, 0.0)) * buf[0]
                    + mat4(vec4(-0.6299101, -0.5945583, -0.9125601, 0.0), vec4(0.17828953, 0.18300213, 0.18182953, 0.0), vec4(-2.96544, -2.5819945, -4.9001055, 0.0), vec4(1.4195864, 1.1868085, 2.5176322, 0.0)) * buf[1]
                    + mat4(vec4(-1.2584374, -1.0552157, -2.1688404, 0.0), vec4(-0.7200217, -0.52666044, -1.438251, 0.0), vec4(0.15345335, 0.15196142, 0.272854, 0.0), vec4(0.945728, 0.8861938, 1.2766753, 0.0)) * buf[2]
                    + mat4(vec4(-2.4218085, -1.968602, -4.35166, 0.0), vec4(-22.683098, -18.0544, -41.954372, 0.0), vec4(0.63792, 0.5470648, 1.1078634, 0.0), vec4(-1.5489894, -1.3075932, -2.6444845, 0.0)) * buf[3]
                    + mat4(vec4(-0.49252132, -0.39877754, -0.91366625, 0.0), vec4(0.95609266, 0.7923952, 1.640221, 0.0), vec4(0.30616966, 0.15693925, 0.8639857, 0.0), vec4(1.1825981, 0.94504964, 2.176963, 0.0)) * buf[4]
                    + mat4(vec4(0.35446745, 0.3293795, 0.59547555, 0.0), vec4(-0.58784515, -0.48177817, -1.0614829, 0.0), vec4(2.5271258, 1.9991658, 4.6846647, 0.0), vec4(0.13042648, 0.08864098, 0.30187556, 0.0)) * buf[5]
                    + mat4(vec4(-1.7718065, -1.4033192, -3.3355875, 0.0), vec4(3.1664357, 2.638297, 5.378702, 0.0), vec4(-3.1724713, -2.6107926, -5.549295, 0.0), vec4(-2.851368, -2.249092, -5.3013067, 0.0)) * buf[6]
                    + mat4(vec4(1.5203838, 1.2212278, 2.8404984, 0.0), vec4(1.5210563, 1.2651345, 2.683903, 0.0), vec4(2.9789467, 2.4364579, 5.2347264, 0.0), vec4(2.2270417, 1.8825914, 3.8028636, 0.0)) * buf[7]
                    + vec4(-1.5468478, -3.6171484, 0.24762098, 0.0);
                    buf[0] = sigmoid(buf[0]);
                    return vec4(buf[0].x, buf[0].y, buf[0].z, 1.0);
                }

                void main() {
                    vec2 uv = vUv * 2.0 - 1.0; uv.y *= -1.0;
                    gl_FragColor = cppn_fn(uv, 0.1 * sin(0.3 * iTime), 0.1 * sin(0.69 * iTime), 0.1 * sin(0.44 * iTime));
                }
            `;

            var hero = container.closest('.hero');
            var w = hero.offsetWidth;
            var h = hero.offsetHeight;

            var scene = new THREE.Scene();
            var camera = new THREE.PerspectiveCamera(75, w / h, 0.1, 1000);
            camera.position.z = 1;

            var renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
            renderer.setSize(w, h);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            renderer.domElement.style.cssText = 'position:absolute;top:0;left:0;width:100%!important;height:100%!important;display:block;';
            container.appendChild(renderer.domElement);

            var uniforms = { iTime: { value: 0 } };
            var material = new THREE.ShaderMaterial({ vertexShader: vertexShader, fragmentShader: fragmentShader, uniforms: uniforms, side: THREE.DoubleSide });
            var vFov = 75 * (Math.PI / 180);
            var dist = 1;
            var planeH = 2 * Math.tan(vFov / 2) * dist;
            var planeW = planeH * (w / h);
            var scale = 1.15;
            var mesh = new THREE.Mesh(new THREE.PlaneGeometry(planeW * scale, planeH * scale), material);
            mesh.position.set(0, 0, 0);
            scene.add(mesh);

            var clock = new THREE.Clock();
            (function animate() {
                requestAnimationFrame(animate);
                uniforms.iTime.value = clock.getElapsedTime();
                renderer.render(scene, camera);
            })();

            window.addEventListener('resize', function() {
                var rw = hero.offsetWidth, rh = hero.offsetHeight;
                camera.aspect = rw / rh;
                camera.updateProjectionMatrix();
                renderer.setSize(rw, rh);
                var newH = 2 * Math.tan(vFov / 2) * dist;
                var newW = newH * (rw / rh);
                mesh.geometry.dispose();
                mesh.geometry = new THREE.PlaneGeometry(newW * scale, newH * scale);
            });
        })();
        </script>

        <!-- Typebot.io Chatbot Bubble -->
        <script>
            const typebotInitScript = document.createElement("script");
            typebotInitScript.type = "module";
            typebotInitScript.innerHTML = `import Typebot from 'https://cdn.jsdelivr.net/npm/@typebot.io/js@0/dist/web.js'

Typebot.initBubble({
  typebot: "faq-amk0u8y",
  theme: {
    button: { backgroundColor: "#1D1D1D" },
    chatWindow: { backgroundColor: "#F8F8F8" },
  },
});
`;
            document.body.append(typebotInitScript);
        </script>
</body>

</html>
