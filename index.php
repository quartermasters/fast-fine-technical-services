<?php
/**
 * Fast and Fine Technical Services FZE - Main Index File
 *
 * This is the main entry point for the website
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Define application constant
define('FAST_FINE_APP', true);

// Load dependencies
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db-connect.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/translations.php';

// Check maintenance mode
checkMaintenanceMode();

// Initialize session
initSecureSession();

// Get current language
$currentLang = getCurrentLanguage();

// Track page view
if (ANALYTICS_ENABLED) {
    trackEvent('page_view', [
        'page' => 'home',
        'language' => $currentLang
    ]);
}

// Include header
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section id="home" class="hero-section">
    <div class="hero-background">
        <div class="particles-container" id="particles"></div>
    </div>

    <div class="container">
        <div class="hero-content scroll-reveal">
            <h1 class="hero-title">
                <?php _e('hero_title'); ?>
            </h1>
            <p class="hero-subtitle">
                <?php _e('hero_subtitle'); ?>
            </p>
            <div class="hero-cta">
                <a href="#booking" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span><?php _e('hero_cta_primary'); ?></span>
                </a>
                <a href="#quote" class="btn btn-outline btn-lg">
                    <i class="fa-solid fa-calculator"></i>
                    <span><?php _e('hero_cta_secondary'); ?></span>
                </a>
            </div>
            <div class="hero-features">
                <div class="feature-badge">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span><?php _e('certified_experts'); ?></span>
                </div>
                <div class="feature-badge">
                    <i class="fa-solid fa-clock"></i>
                    <span><?php _e('available_24_7'); ?></span>
                </div>
                <div class="feature-badge">
                    <i class="fa-solid fa-award"></i>
                    <span><?php _e('satisfaction_guarantee'); ?></span>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="hero-stats">
            <div class="stat-item scroll-reveal delay-100">
                <i class="fa-solid fa-briefcase icon-pulse"></i>
                <div class="stat-content">
                    <span class="stat-number" data-count="15">0</span>
                    <span class="stat-label"><?php _e('years_experience'); ?></span>
                </div>
            </div>
            <div class="stat-item scroll-reveal delay-200">
                <i class="fa-solid fa-users icon-pulse"></i>
                <div class="stat-content">
                    <span class="stat-number" data-count="5000">0</span>
                    <span class="stat-label"><?php _e('happy_clients'); ?></span>
                </div>
            </div>
            <div class="stat-item scroll-reveal delay-300">
                <i class="fa-solid fa-check-circle icon-pulse"></i>
                <div class="stat-content">
                    <span class="stat-number" data-count="12000">0</span>
                    <span class="stat-label"><?php _e('projects_completed'); ?></span>
                </div>
            </div>
            <div class="stat-item scroll-reveal delay-400">
                <i class="fa-solid fa-wrench icon-pulse"></i>
                <div class="stat-content">
                    <span class="stat-number" data-count="9">0</span>
                    <span class="stat-label"><?php _e('services_offered'); ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section (Interactive) -->
<?php require_once __DIR__ . '/sections/services-interactive.php'; ?>

<!-- About Section (Placeholder) -->
<section id="about" class="about-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title scroll-reveal">
                <i class="fa-solid fa-info-circle"></i>
                <?php _e('about'); ?>
            </h2>
        </div>
        <div class="about-content scroll-reveal">
            <p>About section content coming soon...</p>
        </div>
    </div>
</section>

<!-- Testimonials Section (Live Carousel) -->
<?php require_once __DIR__ . '/sections/testimonials-live.php'; ?>

<!-- Portfolio Section (Interactive) -->
<?php require_once __DIR__ . '/sections/projects-portfolio.php'; ?>

<!-- Contact Section -->
<?php require_once __DIR__ . '/sections/contact.php'; ?>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>
