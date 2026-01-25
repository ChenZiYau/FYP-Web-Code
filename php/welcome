<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiPlan - The Evolution of Productivity</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- Sticky Header -->
    <header class="header">
    <div class="container header-content">
        <div class="logo-container">
            <a href="#welcome" class="logo-link">
                <img src="../img/optiplanlogo.png" alt="OptiPlan Logo" class="logo-img">

            </a>
        </div>
        
        <nav class="nav">
            <a href="#about" class="nav-link">About Me</a>
            <a href="#tutorials" class="nav-link">Tutorial Vids</a>
            <a href="#faq" class="nav-link">FAQ</a>
            <a href="#feedback" class="nav-link">Feedbacks</a>
        </nav>
        
        <div class="header-auth">
            <a href="#login" class="btn-login-nav">Log In</a>
            <a href="#signup" class="btn-try-nav">Try Now</a>
        </div>
    </div>
</header>

<section id="welcome" class="hero">
    <div class="container">
        <div class="hero-flex">
            <div class="hero-content">
                <h1 class="hero-title">The Evolution of Productivity</h1>
                <p class="hero-tagline">Bridging the Gap Between Ambition and Organization</p>
                <p class="hero-intro">
                    A unified, intelligent productivity tool designed to balance study, finance, 
                    and daily tasks in a single, streamlined platform.
                </p>
                <div class="hero-buttons">
                    <a href="#login" class="btn btn-primary">Log In</a>
                    <a href="#signup" class="btn btn-secondary">Get Started</a>
                </div>
            </div>
            <div class="hero-spacer"></div>
        </div>
    </div>
</section>

    <!-- About Section -->
    <section id="about" class="about">
    <div class="container">
        <h2 class="section-title">About OptiPlan</h2>
        
        <div class="about-card-main">
            <div class="card-header">
                <h3 class="card-subtitle">Solving the Digital Fragment</h3>
                <p class="card-intro">
                    In today's fast-paced digital world, productivity tools are scattered across multiple platforms, creating inefficiency and cognitive overload. OptiPlan addresses this critical challenge by unifying essential productivity features into a single, intelligent ecosystem.
                </p>
            </div>

            <div class="pillars-grid">
                <div class="pillar-card">
                    <div class="pillar-header">
                        <span class="pillar-emoji">🗓️</span>
                        <h3>Smart Scheduling</h3>
                    </div>
                    <p>Intelligent calendar management that adapts to your workflow and priorities.</p>
                </div>
                
                <div class="pillar-card">
                    <div class="pillar-header">
                        <span class="pillar-emoji">🎴</span>
                        <h3>Flashcards</h3>
                    </div>
                    <p>Efficient learning tools with spaced repetition algorithms for better retention.</p>
                </div>

                <div class="pillar-card">
                    <div class="pillar-header">
                        <span class="pillar-emoji">💰</span>
                        <h3>Finance Tracking</h3>
                    </div>
                    <p>Comprehensive budget management and expense tracking in one place.</p>
                </div>

                <div class="pillar-card">
                    <div class="pillar-header">
                        <span class="pillar-emoji">🤖</span>
                        <h3>AI Assistant</h3>
                    </div>
                    <p>Your Digital Architect, orchestrating tasks and insights seamlessly.</p>
                </div>
            </div>

            <div class="ai-highlight-box">
                <h3 class="card-subtitle">Meet Your Digital Architect</h3>
                <p>
                    OptiPlan's AI assistant isn't just another chatbot—it's your Digital Architect. This intelligent system understands your habits, anticipates your needs, and orchestrates your digital life with precision. From scheduling conflicts to financial insights, your Digital Architect ensures every element of your productivity ecosystem works in perfect harmony.
                </p>
            </div>
        </div>
    </div>
</section>

    <!-- Tutorial Videos Section -->
<section id="tutorials" class="tutorials">
    <div class="container">
        <h2 class="section-title">Product Guide</h2>
        
        <div class="tutorial-card-main">
            <div class="tutorial-content-wrapper">
                <div class="tutorial-list">
                    <?php
                    $videos = [
                        ['title' => 'AI Schedule Optimizer', 'icon' => '📅'],
                        ['title' => 'Flashcard Studio', 'icon' => '🎴'],
                        ['title' => 'Finance Dashboard', 'icon' => '📊'],
                        ['title' => 'Collaborative Workspace', 'icon' => '👥'],
                        ['title' => 'Smart Task Manager', 'icon' => '✓'],
                        ['title' => 'AI Note Summarizer', 'icon' => '📝']
                    ];
                    
                    foreach ($videos as $video): ?>
                        <div class="tutorial-item">
                            <span class="tutorial-emoji"><?php echo $video['icon']; ?></span>
                            <h3><?php echo $video['title']; ?></h3>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="tutorial-video-display">
                    <div class="video-player">
                        <div class="video-overlay">
                            <div class="play-icon">▶</div>
                        </div>
                        <div class="video-placeholder-text">
                            <p>Select a feature to watch tutorial</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- FAQ Section -->
    <section id="faq" class="faq">
        <div class="container">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(0)">
                        <span>What makes OptiPlan different from other productivity tools?</span>
                        <span class="faq-arrow">▼</span>
                    </button>
                    <div class="faq-answer" id="faq-0">
                        <p>OptiPlan unifies scheduling, learning, finance tracking, and collaboration in one AI-powered platform, eliminating the need to juggle multiple apps.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(1)">
                        <span>Is my data secure?</span>
                        <span class="faq-arrow">▼</span>
                    </button>
                    <div class="faq-answer" id="faq-1">
                        <p>Absolutely. We use industry-standard encryption and never share your personal information with third parties.</p>
                    </div>
                </div>          
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(2)">
                        <span>Can I use OptiPlan on mobile devices?</span>
                        <span class="faq-arrow">▼</span>
                    </button>
                    <div class="faq-answer" id="faq-2">
                        <p>Yes! OptiPlan is fully responsive and works seamlessly across desktop, tablet, and mobile devices.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(3)">
                        <span>How does the AI assistant work?</span>
                        <span class="faq-arrow">▼</span>
                    </button>
                    <div class="faq-answer" id="faq-3">
                        <p>Our AI analyzes your patterns, priorities, and goals to provide personalized recommendations, schedule optimizations, and intelligent note summaries.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(4)">
                        <span>Is there a free trial?</span>
                        <span class="faq-arrow">▼</span>
                    </button>
                <div class="faq-answer" id="faq-4">
                    <p>Yes, we offer a 14-day free trial with full access to all premium features.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Feedback Section -->
    <section id="feedback" class="feedback">
        <div class="container">
            <h2 class="section-title">Send Us Your Feedback</h2>
            <form class="feedback-form" method="POST" action="submit_feedback.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea id="message" name="message" rows="6" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Feedback</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Platform</h4>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#pricing">Pricing</a></li>
                        <li><a href="#integrations">Integrations</a></li>
                        <li><a href="#api">API</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#help">Help Center</a></li>
                        <li><a href="#contact">Contact Us</a></li>
                        <li><a href="#status">System Status</a></li>
                        <li><a href="#community">Community</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Socials</h4>
                    <ul>
                        <li><a href="#twitter">Twitter</a></li>
                        <li><a href="#linkedin">LinkedIn</a></li>
                        <li><a href="#github">GitHub</a></li>
                        <li><a href="#discord">Discord</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#privacy">Privacy Policy</a></li>
                        <li><a href="#terms">Terms of Service</a></li>
                        <li><a href="#cookies">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 OptiPlan. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // FAQ Toggle
        function toggleFAQ(index) {
    const allItems = document.querySelectorAll('.faq-item');
    const itemToToggle = allItems[index];

    // Close others for that "Accordion" feel (optional)
    allItems.forEach((item, i) => {
        if (i !== index) item.classList.remove('active');
    });

    // Toggle the clicked one
    itemToToggle.classList.toggle('active');
}
    </script>
</body>
</html>