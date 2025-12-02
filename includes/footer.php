<?php
/**
 * Fast and Fine Technical Services FZE - Footer Component
 *
 * Includes:
 * - Company info and logo
 * - Quick links
 * - Services menu
 * - Contact information
 * - Social media links
 * - Newsletter subscription
 * - Copyright notice
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Prevent direct access
if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}
?>

</main><!-- End Main Content -->

<!-- Footer -->
<footer class="footer" id="footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                <!-- Company Info -->
                <div class="footer-col footer-about">
                    <div class="footer-logo">
                        <img src="<?php echo assetUrl('images/logo-white.png'); ?>" alt="<?php echo SITE_NAME; ?>" class="footer-logo-img">
                        <span class="footer-logo-text">Fast & Fine</span>
                    </div>
                    <p class="footer-description">
                        <?php _e('site_tagline'); ?>
                    </p>
                    <div class="footer-features">
                        <div class="footer-feature">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span><?php _e('certified_experts'); ?></span>
                        </div>
                        <div class="footer-feature">
                            <i class="fa-solid fa-clock"></i>
                            <span><?php _e('available_24_7'); ?></span>
                        </div>
                        <div class="footer-feature">
                            <i class="fa-solid fa-award"></i>
                            <span><?php _e('satisfaction_guarantee'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-col">
                    <h3 class="footer-title">
                        <i class="fa-solid fa-link"></i>
                        <?php _e('quick_links'); ?>
                    </h3>
                    <ul class="footer-links">
                        <li><a href="#home"><i class="fa-solid fa-chevron-right"></i> <?php _e('home'); ?></a></li>
                        <li><a href="#about"><i class="fa-solid fa-chevron-right"></i> <?php _e('about'); ?></a></li>
                        <li><a href="#services"><i class="fa-solid fa-chevron-right"></i> <?php _e('services'); ?></a></li>
                        <li><a href="#portfolio"><i class="fa-solid fa-chevron-right"></i> <?php _e('portfolio'); ?></a></li>
                        <li><a href="#testimonials"><i class="fa-solid fa-chevron-right"></i> <?php _e('testimonials'); ?></a></li>
                        <li><a href="#contact"><i class="fa-solid fa-chevron-right"></i> <?php _e('contact'); ?></a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div class="footer-col">
                    <h3 class="footer-title">
                        <i class="fa-solid fa-wrench"></i>
                        <?php _e('services_menu'); ?>
                    </h3>
                    <ul class="footer-links">
                        <li><a href="#services"><i class="fa-solid fa-broom"></i> <?php _e('building_cleaning'); ?></a></li>
                        <li><a href="#services"><i class="fa-solid fa-hammer"></i> <?php _e('carpentry'); ?></a></li>
                        <li><a href="#services"><i class="fa-solid fa-faucet-drip"></i> <?php _e('plumbing'); ?></a></li>
                        <li><a href="#services"><i class="fa-solid fa-snowflake"></i> <?php _e('air_conditioning'); ?></a></li>
                        <li><a href="#services"><i class="fa-solid fa-gears"></i> <?php _e('electromechanical'); ?></a></li>
                        <li><a href="#services"><i class="fa-solid fa-paint-roller"></i> <?php _e('painting'); ?></a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-col">
                    <h3 class="footer-title">
                        <i class="fa-solid fa-address-book"></i>
                        <?php _e('contact_info'); ?>
                    </h3>
                    <ul class="footer-contact">
                        <li>
                            <i class="fa-solid fa-map-marker-alt"></i>
                            <div>
                                <strong><?php _e('address'); ?></strong>
                                <span><?php echo BUSINESS_ADDRESS; ?></span>
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-phone"></i>
                            <div>
                                <strong><?php _e('phone'); ?></strong>
                                <a href="tel:<?php echo WHATSAPP_NUMBER; ?>"><?php echo PHONE_DISPLAY; ?></a>
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-envelope"></i>
                            <div>
                                <strong><?php _e('email'); ?></strong>
                                <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a>
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-clock"></i>
                            <div>
                                <strong><?php _e('business_hours'); ?></strong>
                                <span><?php echo BUSINESS_HOURS; ?></span>
                            </div>
                        </li>
                    </ul>

                    <!-- Social Media -->
                    <div class="footer-social">
                        <h4 class="footer-social-title"><?php _e('social_media'); ?>:</h4>
                        <div class="social-links">
                            <?php if(FACEBOOK_URL): ?>
                            <a href="<?php echo FACEBOOK_URL; ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                            <?php endif; ?>

                            <?php if(INSTAGRAM_URL): ?>
                            <a href="<?php echo INSTAGRAM_URL; ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Instagram">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                            <?php endif; ?>

                            <?php if(TWITTER_URL): ?>
                            <a href="<?php echo TWITTER_URL; ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Twitter">
                                <i class="fa-brands fa-twitter"></i>
                            </a>
                            <?php endif; ?>

                            <?php if(LINKEDIN_URL): ?>
                            <a href="<?php echo LINKEDIN_URL; ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="LinkedIn">
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                            <?php endif; ?>

                            <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', WHATSAPP_NUMBER); ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="WhatsApp">
                                <i class="fa-brands fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Newsletter -->
            <div class="footer-newsletter">
                <div class="newsletter-content">
                    <div class="newsletter-text">
                        <i class="fa-solid fa-envelope-open-text"></i>
                        <div>
                            <h3><?php _e('newsletter'); ?></h3>
                            <p><?php _e('subscribe_newsletter'); ?></p>
                        </div>
                    </div>
                    <form class="newsletter-form" id="newsletterForm">
                        <?php echo csrfField(); ?>
                        <div class="newsletter-input-group">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" name="email" placeholder="<?php _e('enter_email'); ?>" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-paper-plane"></i>
                                <span><?php _e('subscribe'); ?></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <div class="footer-copyright">
                    <p>
                        <?php echo translate('copyright', ['year' => date('Y')]); ?>
                    </p>
                </div>
                <div class="footer-legal">
                    <a href="#privacy-policy"><?php _e('privacy_policy'); ?></a>
                    <span class="separator">|</span>
                    <a href="#terms-conditions"><?php _e('terms_conditions'); ?></a>
                </div>
                <div class="footer-powered">
                    <span><?php _e('powered_by'); ?> <i class="fa-solid fa-heart"></i></span>
                    <a href="https://claude.com/claude-code" target="_blank" rel="noopener">Claude Code</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" aria-label="<?php _e('back_to_top'); ?>">
        <i class="fa-solid fa-arrow-up"></i>
    </button>
</footer>

<!-- Scripts -->
<script src="<?php echo assetUrl('js/main.js'); ?>"></script>
<script src="<?php echo assetUrl('js/animations.js'); ?>"></script>
<script src="<?php echo assetUrl('js/services.js'); ?>"></script>
<script src="<?php echo assetUrl('js/portfolio.js'); ?>"></script>

<?php if(FEATURE_LIVE_CHAT): ?>
<!-- WhatsApp Chat Widget Script -->
<script>
    // WhatsApp chat widget initialization
    const whatsappNumber = '<?php echo str_replace(['+', '-', ' '], '', WHATSAPP_NUMBER); ?>';
</script>
<?php endif; ?>

<!-- PWA Service Worker -->
<?php if(PWA_ENABLED): ?>
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('<?php echo siteUrl('service-worker.js'); ?>')
            .then(reg => console.log('Service Worker registered', reg))
            .catch(err => console.log('Service Worker registration failed', err));
    }
</script>
<?php endif; ?>

<!-- Analytics Tracking -->
<?php if(ANALYTICS_ENABLED): ?>
<script>
    // Track page view
    if(typeof trackEvent === 'function') {
        trackEvent('page_view', {
            page: window.location.pathname,
            title: document.title
        });
    }
</script>
<?php endif; ?>

</body>
</html>
