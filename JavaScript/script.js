/* ==========================================
   OPTIPLAN - INTERACTIVE JAVASCRIPT
   ========================================== */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // HEADER SCROLL EFFECT
    // ==========================================
    const header = document.getElementById('header');
    let lastScroll = 0;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        // Add shadow on scroll
        if (currentScroll > 10) {
            header.style.boxShadow = '0 4px 6px -1px rgb(0 0 0 / 0.1)';
        } else {
            header.style.boxShadow = 'none';
        }
        
        lastScroll = currentScroll;
    });
    
    
    // ==========================================
    // SMOOTH SCROLLING FOR NAVIGATION LINKS
    // ==========================================
    const smoothScrollLinks = document.querySelectorAll('a[href^="#"]');
    
    smoothScrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            
            // Handle scroll to top
            if (targetId === '#top' || targetId === '#') {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                return;
            }
            
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                // Close mobile menu if open
                closeMobileMenu();
                
                // Close dropdown if open
                closeAllDropdowns();
                
                // Scroll to target
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    
    // ==========================================
    // DROPDOWN MENU FUNCTIONALITY (robust + accessible)
    // ==========================================
    const dropdowns = document.querySelectorAll('.dropdown');
    
    // Close all dropdowns and reset triggers
    function closeAllDropdowns() {
        dropdowns.forEach(dd => {
            dd.classList.remove('active');
            const trig = dd.querySelector('.dropdown-trigger');
            if (trig) trig.setAttribute('aria-expanded', 'false');
        });
    }
    
    // Close on click outside any dropdown
    document.addEventListener('click', function(e) {
        let clickedInsideAny = false;
        dropdowns.forEach(dd => {
            if (dd.contains(e.target)) clickedInsideAny = true;
        });
        if (!clickedInsideAny) closeAllDropdowns();
    });
    
    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAllDropdowns();
    });
    
    // Initialize each dropdown instance
    dropdowns.forEach(dd => {
        const trigger = dd.querySelector('.dropdown-trigger');
        const links = dd.querySelectorAll('.dropdown-link');

        if (!trigger) return;

        // Toggle the dropdown and update aria state
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            const isActive = dd.classList.toggle('active');
            trigger.setAttribute('aria-expanded', String(isActive));

            // close other dropdowns when opening this one
            if (isActive) {
                dropdowns.forEach(other => {
                    if (other !== dd) {
                        other.classList.remove('active');
                        const otherTrig = other.querySelector('.dropdown-trigger');
                        if (otherTrig) otherTrig.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        });

        // Close when clicking a link inside
        links.forEach(link => {
            link.addEventListener('click', function() {
                dd.classList.remove('active');
                trigger.setAttribute('aria-expanded', 'false');
            });
        });
    });
    
    
    // ==========================================
    // FAQ ACCORDION FUNCTIONALITY
    // ==========================================
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', function() {
            const isActive = item.classList.contains('active');
            
            // Close all FAQ items
            faqItems.forEach(faqItem => {
                faqItem.classList.remove('active');
                faqItem.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            });
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                item.classList.add('active');
                question.setAttribute('aria-expanded', 'true');
            }
        });
    });
    
    
    // ==========================================
    // MOBILE MENU FUNCTIONALITY
    // ==========================================
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    const navActions = document.querySelector('.nav-actions');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            // Toggle mobile menu (you would add CSS to show/hide nav-links and nav-actions)
            this.classList.toggle('active');
            navLinks.classList.toggle('mobile-active');
            navActions.classList.toggle('mobile-active');
            
            // Prevent body scroll when menu is open
            document.body.classList.toggle('menu-open');
        });
    }
    
    function closeMobileMenu() {
        if (mobileMenuToggle) {
            mobileMenuToggle.classList.remove('active');
            navLinks.classList.remove('mobile-active');
            navActions.classList.remove('mobile-active');
            document.body.classList.remove('menu-open');
        }
    }
    
    
    // ==========================================
    // VIDEO PLACEHOLDER INTERACTION
    // ==========================================
    const videoPlaceholder = document.querySelector('.video-placeholder');
    
    if (videoPlaceholder) {
        videoPlaceholder.addEventListener('click', function() {
            // You can replace this with actual video embed or modal
            alert('Video player would open here. Replace the placeholder with your actual video embed code.');
            
            // Example of how to replace with iframe:
            // const videoWrapper = this.parentElement;
            // videoWrapper.innerHTML = '<iframe src="YOUR_VIDEO_URL" frameborder="0" allowfullscreen></iframe>';
        });
    }
    
    
    // ==========================================
    // SCROLL ANIMATIONS (FADE-IN ON SCROLL)
    // ==========================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Add animation class to elements
    const animatedElements = document.querySelectorAll('.problem-card, .feature-card, .feedback-card, .about-card');
    
    animatedElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(element);
    });
    
    
    // ==========================================
    // LOGO CLICK - SCROLL TO TOP
    // ==========================================
    const logoLink = document.querySelector('.logo-link');
    
    if (logoLink) {
        logoLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            
            // Close mobile menu if open
            closeMobileMenu();
        });
    }
    
    
    // ==========================================
    // DYNAMIC COPYRIGHT YEAR
    // ==========================================
    const copyrightYear = document.querySelector('.footer-copyright');
    if (copyrightYear) {
        const currentYear = new Date().getFullYear();
        copyrightYear.innerHTML = `&copy; ${currentYear} OptiPlan. All rights reserved.`;
    }
    
    
    // ==========================================
    // FORM VALIDATION (if you add forms later)
    // ==========================================
    // You can add form validation logic here when needed
    
    
    // ==========================================
    // ACCESSIBILITY: KEYBOARD NAVIGATION
    // ==========================================
    // Close dropdown on Tab key press when focus leaves
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            const dropdown = document.querySelector('.dropdown.active');
            if (dropdown) {
                setTimeout(function() {
                    if (!dropdown.contains(document.activeElement)) {
                        dropdown.classList.remove('active');
                    }
                }, 0);
            }
        }
    });
    
    
    // ==========================================
    // PERFORMANCE: DEBOUNCED SCROLL HANDLER
    // ==========================================
    let scrollTimeout;
    
    window.addEventListener('scroll', function() {
        if (scrollTimeout) {
            window.cancelAnimationFrame(scrollTimeout);
        }
        
        scrollTimeout = window.requestAnimationFrame(function() {
            // Any additional scroll-based animations can go here
        });
    });
    
    
    // ==========================================
    // CONSOLE LOG - REMOVE IN PRODUCTION
    // ==========================================
    console.log('OptiPlan - Interactive JavaScript Loaded Successfully');
    console.log('All features initialized and ready to use');
    
});


/* ==========================================
   ADDITIONAL UTILITY FUNCTIONS
   ========================================== */

/**
 * Debounce function to limit how often a function can run
 * Useful for scroll and resize event handlers
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
 * Check if element is in viewport
 * Useful for custom scroll animations
 */
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

/**
 * Smooth scroll to element
 * Alternative method if needed
 */
function smoothScrollTo(targetId) {
    const targetElement = document.querySelector(targetId);
    if (targetElement) {
        const headerOffset = 80;
        const elementPosition = targetElement.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
}