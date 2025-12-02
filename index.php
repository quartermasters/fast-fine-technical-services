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

<!-- Services Section -->
<section id="services" class="services-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title scroll-reveal">
                <i class="fa-solid fa-wrench"></i>
                <?php _e('our_services'); ?>
            </h2>
            <p class="section-subtitle scroll-reveal">
                Professional technical services for residential and commercial properties in Dubai
            </p>
        </div>

        <div class="services-grid">
            <?php
            // Get services from database
            $services = dbSelect("
                SELECT id, name_en, name_ar, slug, short_desc_en, short_desc_ar,
                       icon_class, starting_price, category
                FROM services
                WHERE is_active = 1
                ORDER BY display_order ASC
            ");

            foreach ($services as $index => $service):
                $name = $currentLang === 'ar' ? $service['name_ar'] : $service['name_en'];
                $desc = $currentLang === 'ar' ? $service['short_desc_ar'] : $service['short_desc_en'];
                $delay = ($index % 3) * 100 + 100;
            ?>
                <div class="service-card scroll-reveal delay-<?php echo $delay; ?>">
                    <div class="service-icon">
                        <i class="<?php echo escapeHTML($service['icon_class']); ?>"></i>
                    </div>
                    <h3 class="service-title"><?php echo escapeHTML($name); ?></h3>
                    <p class="service-description"><?php echo escapeHTML($desc); ?></p>
                    <div class="service-footer">
                        <span class="service-price">
                            <?php _e('starting_from'); ?>: <?php echo formatPrice($service['starting_price']); ?>
                        </span>
                        <a href="#service-<?php echo $service['id']; ?>" class="btn btn-sm btn-outline">
                            <span><?php _e('view_details'); ?></span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="section-cta scroll-reveal">
            <a href="#contact" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-headset"></i>
                <span><?php _e('get_in_touch'); ?></span>
            </a>
        </div>
    </div>
</section>

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

<!-- Testimonials Section (Placeholder) -->
<section id="testimonials" class="testimonials-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title scroll-reveal">
                <i class="fa-solid fa-star"></i>
                <?php _e('what_clients_say'); ?>
            </h2>
        </div>
        <div class="testimonials-content scroll-reveal">
            <p>Testimonials section content coming soon...</p>
        </div>
    </div>
</section>

<!-- Portfolio Section (Placeholder) -->
<section id="portfolio" class="portfolio-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title scroll-reveal">
                <i class="fa-solid fa-briefcase"></i>
                <?php _e('our_portfolio'); ?>
            </h2>
        </div>
        <div class="portfolio-content scroll-reveal">
            <p>Portfolio section content coming soon...</p>
        </div>
    </div>
</section>

<!-- Contact Section (Placeholder) -->
<section id="contact" class="contact-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title scroll-reveal">
                <i class="fa-solid fa-envelope"></i>
                <?php _e('contact_us'); ?>
            </h2>
        </div>
        <div class="contact-content scroll-reveal">
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fa-solid fa-phone"></i>
                    <div>
                        <strong><?php _e('phone'); ?></strong>
                        <a href="tel:<?php echo WHATSAPP_NUMBER; ?>"><?php echo PHONE_DISPLAY; ?></a>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fa-solid fa-envelope"></i>
                    <div>
                        <strong><?php _e('email'); ?></strong>
                        <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fa-solid fa-map-marker-alt"></i>
                    <div>
                        <strong><?php _e('address'); ?></strong>
                        <span><?php echo BUSINESS_ADDRESS; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>
