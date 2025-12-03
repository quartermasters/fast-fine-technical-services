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

// Include Dubai Premium Hero (Concept 2)
require_once __DIR__ . '/sections/hero-premium-dubai.php';

// OLD: High-conversion hero (commented out - replaced with Premium Dubai concept)
// require_once __DIR__ . '/sections/hero-conversion.php';

// Include secondary services showcase
require_once __DIR__ . '/sections/secondary-services.php';
?>

<!-- Services Section (Interactive) - Now hidden or repurposed -->
<?php // require_once __DIR__ . '/sections/services-interactive.php'; ?>

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

<!-- Booking Calculator Section -->
<?php require_once __DIR__ . '/sections/booking-calculator.php'; ?>

<!-- Contact Section -->
<?php require_once __DIR__ . '/sections/contact.php'; ?>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>
