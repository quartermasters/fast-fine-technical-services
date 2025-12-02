/**
 * Fast and Fine Technical Services FZE - Main JavaScript
 *
 * Handles all interactive functionality:
 * - Mobile menu
 * - Theme toggle (dark/light)
 * - Language switcher
 * - Smooth scrolling
 * - Scroll animations
 * - Form handling
 * - Analytics tracking
 * - Modals and overlays
 *
 * @package FastAndFine
 * @version 1.0.0
 */

(function() {
    'use strict';

    // ========================================================================
    // GLOBAL STATE
    // ========================================================================

    const state = {
        theme: localStorage.getItem('theme') || 'light',
        language: document.documentElement.lang || 'en',
        mobileMenuOpen: false,
        scrolled: false
    };

    // ========================================================================
    // DOM ELEMENTS
    // ========================================================================

    const DOM = {
        header: document.getElementById('header'),
        mobileMenuToggle: document.getElementById('mobileMenuToggle'),
        mobileMenuClose: document.getElementById('mobileMenuClose'),
        mobileMenuOverlay: document.getElementById('mobileMenuOverlay'),
        themeToggle: document.getElementById('themeToggle'),
        langBtn: document.getElementById('langBtn'),
        langDropdown: document.getElementById('langDropdown'),
        scrollProgress: document.getElementById('scrollProgress'),
        backToTop: document.getElementById('backToTop'),
        navLinks: document.querySelectorAll('.nav-link, .mobile-nav-link'),
        body: document.body
    };

    // ========================================================================
    // THEME MANAGEMENT
    // ========================================================================

    function initTheme() {
        // Set initial theme
        document.body.setAttribute('data-theme', state.theme);

        // Theme toggle handler
        if (DOM.themeToggle) {
            DOM.themeToggle.addEventListener('click', toggleTheme);
        }
    }

    function toggleTheme() {
        state.theme = state.theme === 'light' ? 'dark' : 'light';
        document.body.setAttribute('data-theme', state.theme);
        localStorage.setItem('theme', state.theme);

        // Track theme change
        trackEvent('theme_changed', { theme: state.theme });
    }

    // ========================================================================
    // MOBILE MENU
    // ========================================================================

    function initMobileMenu() {
        if (DOM.mobileMenuToggle) {
            DOM.mobileMenuToggle.addEventListener('click', toggleMobileMenu);
        }

        if (DOM.mobileMenuClose) {
            DOM.mobileMenuClose.addEventListener('click', closeMobileMenu);
        }

        if (DOM.mobileMenuOverlay) {
            DOM.mobileMenuOverlay.addEventListener('click', function(e) {
                if (e.target === DOM.mobileMenuOverlay) {
                    closeMobileMenu();
                }
            });
        }

        // Close menu when clicking nav links
        document.querySelectorAll('.mobile-nav-link').forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });
    }

    function toggleMobileMenu() {
        state.mobileMenuOpen = !state.mobileMenuOpen;

        if (state.mobileMenuOpen) {
            openMobileMenu();
        } else {
            closeMobileMenu();
        }
    }

    function openMobileMenu() {
        DOM.mobileMenuOverlay.classList.add('active');
        DOM.body.style.overflow = 'hidden';
        trackEvent('mobile_menu_opened');
    }

    function closeMobileMenu() {
        state.mobileMenuOpen = false;
        DOM.mobileMenuOverlay.classList.remove('active');
        DOM.body.style.overflow = '';
        trackEvent('mobile_menu_closed');
    }

    // ========================================================================
    // SMOOTH SCROLLING
    // ========================================================================

    function initSmoothScroll() {
        DOM.navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');

                if (href && href.startsWith('#')) {
                    e.preventDefault();
                    const targetId = href.substring(1);
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        const headerHeight = DOM.header ? DOM.header.offsetHeight : 80;
                        const targetPosition = targetElement.offsetTop - headerHeight;

                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });

                        // Update active nav link
                        updateActiveNavLink(this);

                        // Track navigation
                        trackEvent('navigation_click', { target: targetId });
                    }
                }
            });
        });
    }

    function updateActiveNavLink(activeLink) {
        DOM.navLinks.forEach(link => link.classList.remove('active'));
        activeLink.classList.add('active');

        // Update mobile nav link if exists
        const section = activeLink.getAttribute('data-section');
        if (section) {
            document.querySelectorAll(`[data-section="${section}"]`).forEach(link => {
                link.classList.add('active');
            });
        }
    }

    // ========================================================================
    // SCROLL HANDLING
    // ========================================================================

    function initScrollHandlers() {
        let ticking = false;

        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Initial check
        handleScroll();
    }

    function handleScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / scrollHeight) * 100;

        // Update scroll progress
        if (DOM.scrollProgress) {
            DOM.scrollProgress.style.width = scrollPercent + '%';
        }

        // Header shadow on scroll
        if (scrollTop > 50 && !state.scrolled) {
            state.scrolled = true;
            DOM.header?.classList.add('scrolled');
        } else if (scrollTop <= 50 && state.scrolled) {
            state.scrolled = false;
            DOM.header?.classList.remove('scrolled');
        }

        // Back to top button visibility
        if (DOM.backToTop) {
            if (scrollTop > 300) {
                DOM.backToTop.classList.add('visible');
            } else {
                DOM.backToTop.classList.remove('visible');
            }
        }

        // Update active section in navigation
        updateActiveSection();
    }

    function updateActiveSection() {
        const sections = document.querySelectorAll('section[id]');
        const scrollPos = window.pageYOffset + 200;

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');

            if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                DOM.navLinks.forEach(link => {
                    const linkSection = link.getAttribute('data-section');
                    if (linkSection === sectionId) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            }
        });
    }

    // ========================================================================
    // BACK TO TOP
    // ========================================================================

    function initBackToTop() {
        if (DOM.backToTop) {
            DOM.backToTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                trackEvent('back_to_top_clicked');
            });
        }
    }

    // ========================================================================
    // SCROLL REVEAL ANIMATIONS
    // ========================================================================

    function initScrollReveal() {
        const revealElements = document.querySelectorAll('.scroll-reveal, .scroll-reveal-left, .scroll-reveal-right, .scroll-reveal-scale');

        const observerOptions = {
            threshold: 0.15,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    // Optionally unobserve after reveal
                    // observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        revealElements.forEach(el => observer.observe(el));
    }

    // ========================================================================
    // FORM HANDLING
    // ========================================================================

    function initForms() {
        // Newsletter form
        const newsletterForm = document.getElementById('newsletterForm');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', handleNewsletterSubmit);
        }

        // Contact form
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', handleContactSubmit);
        }

        // Booking form
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            bookingForm.addEventListener('submit', handleBookingSubmit);
        }
    }

    async function handleNewsletterSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Subscribing...</span>';

            const response = await fetch('/handlers/newsletter.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showNotification('success', result.message || 'Successfully subscribed!');
                form.reset();
                trackEvent('newsletter_subscribed', { email: formData.get('email') });
            } else {
                showNotification('error', result.message || 'Subscription failed');
            }
        } catch (error) {
            console.error('Newsletter error:', error);
            showNotification('error', 'An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> <span>Subscribe</span>';
        }
    }

    async function handleContactSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';

            const response = await fetch('/handlers/contact-form.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showNotification('success', result.message || 'Message sent successfully!');
                form.reset();
                trackEvent('contact_form_submitted');
            } else {
                showNotification('error', result.message || 'Failed to send message');
            }
        } catch (error) {
            console.error('Contact form error:', error);
            showNotification('error', 'An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send Message';
        }
    }

    async function handleBookingSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Creating Booking...';

            const response = await fetch('/handlers/booking-handler.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showNotification('success', result.message || 'Booking created successfully!');
                form.reset();
                trackEvent('booking_created', { bookingId: result.data?.booking_id });

                // Optionally redirect to confirmation page
                if (result.data?.booking_id) {
                    setTimeout(() => {
                        window.location.href = `/booking-confirmation.php?id=${result.data.booking_id}`;
                    }, 2000);
                }
            } else {
                showNotification('error', result.message || 'Failed to create booking');
            }
        } catch (error) {
            console.error('Booking error:', error);
            showNotification('error', 'An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-calendar-check"></i> Book Now';
        }
    }

    // ========================================================================
    // NOTIFICATIONS
    // ========================================================================

    function showNotification(type, message, duration = 5000) {
        // Remove existing notifications
        const existing = document.querySelector('.notification');
        if (existing) {
            existing.remove();
        }

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;

        const icon = type === 'success' ? 'fa-check-circle' :
                     type === 'error' ? 'fa-exclamation-circle' :
                     'fa-info-circle';

        notification.innerHTML = `
            <i class="fa-solid ${icon}"></i>
            <span>${message}</span>
            <button class="notification-close">
                <i class="fa-solid fa-times"></i>
            </button>
        `;

        document.body.appendChild(notification);

        // Show notification
        setTimeout(() => notification.classList.add('show'), 100);

        // Auto-hide
        const hideTimeout = setTimeout(() => hideNotification(notification), duration);

        // Manual close
        notification.querySelector('.notification-close').addEventListener('click', () => {
            clearTimeout(hideTimeout);
            hideNotification(notification);
        });
    }

    function hideNotification(notification) {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }

    // ========================================================================
    // ANALYTICS TRACKING
    // ========================================================================

    function trackEvent(eventType, details = {}) {
        // Track with custom analytics system
        if (typeof fetch !== 'undefined') {
            fetch('/api/track-event.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    event_type: eventType,
                    details: details,
                    timestamp: new Date().toISOString()
                })
            }).catch(err => console.error('Analytics error:', err));
        }

        // Track with Google Analytics if available
        if (typeof gtag !== 'undefined') {
            gtag('event', eventType, details);
        }
    }

    // Track page visibility
    function initVisibilityTracking() {
        let startTime = Date.now();

        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                const timeSpent = Date.now() - startTime;
                trackEvent('page_hidden', {
                    time_spent: Math.round(timeSpent / 1000)
                });
            } else {
                startTime = Date.now();
                trackEvent('page_visible');
            }
        });
    }

    // ========================================================================
    // LAZY LOADING
    // ========================================================================

    function initLazyLoading() {
        const lazyImages = document.querySelectorAll('img[data-src], iframe[data-src]');

        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        const src = element.getAttribute('data-src');

                        if (src) {
                            element.src = src;
                            element.removeAttribute('data-src');
                            element.classList.add('loaded');
                        }

                        imageObserver.unobserve(element);
                    }
                });
            });

            lazyImages.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback for older browsers
            lazyImages.forEach(element => {
                const src = element.getAttribute('data-src');
                if (src) {
                    element.src = src;
                    element.removeAttribute('data-src');
                }
            });
        }
    }

    // ========================================================================
    // STATISTICS COUNTER
    // ========================================================================

    function initCounters() {
        const counters = document.querySelectorAll('[data-count]');

        if (counters.length === 0) return;

        const counterObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => counterObserver.observe(counter));
    }

    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000;
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current).toLocaleString();
            }
        }, 16);
    }

    // ========================================================================
    // UTILITY FUNCTIONS
    // ========================================================================

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

    function throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // ========================================================================
    // INITIALIZATION
    // ========================================================================

    function init() {
        console.log('Fast & Fine - Initializing...');

        // Initialize all modules
        initTheme();
        initMobileMenu();
        initSmoothScroll();
        initScrollHandlers();
        initBackToTop();
        initScrollReveal();
        initForms();
        initLazyLoading();
        initCounters();
        initVisibilityTracking();

        // Track page load
        trackEvent('page_loaded', {
            page: window.location.pathname,
            referrer: document.referrer
        });

        console.log('Fast & Fine - Ready!');
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Export functions for global use
    window.FastAndFine = {
        trackEvent,
        showNotification,
        toggleTheme,
        openMobileMenu,
        closeMobileMenu
    };

})();
