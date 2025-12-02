<?php
/**
 * Fast and Fine Technical Services FZE - Header Component
 *
 * Includes:
 * - Navigation menu
 * - Language switcher (EN/AR)
 * - Theme toggle (Dark/Light)
 * - Mobile menu
 * - WhatsApp quick contact
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Prevent direct access
if(!defined('FAST_FINE_APP')) {
    define('FAST_FINE_APP', true);
}

// Load dependencies
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/translations.php';

$currentLang = getCurrentLanguage();
$isRTL = isRTL();
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>" dir="<?php echo $isRTL ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- SEO Meta Tags -->
    <title><?php echo SEO_TITLE; ?></title>
    <meta name="description" content="<?php echo SEO_DESCRIPTION; ?>">
    <meta name="keywords" content="<?php echo SEO_KEYWORDS; ?>">
    <meta name="author" content="<?php echo SEO_AUTHOR; ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    <meta property="og:title" content="<?php echo SEO_TITLE; ?>">
    <meta property="og:description" content="<?php echo SEO_DESCRIPTION; ?>">
    <meta property="og:image" content="<?php echo SEO_OG_IMAGE; ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo SITE_URL; ?>">
    <meta property="twitter:title" content="<?php echo SEO_TITLE; ?>">
    <meta property="twitter:description" content="<?php echo SEO_DESCRIPTION; ?>">
    <meta property="twitter:image" content="<?php echo SEO_OG_IMAGE; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo assetUrl('images/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo assetUrl('images/favicon-16x16.png'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo assetUrl('images/apple-touch-icon.png'); ?>">

    <!-- PWA Manifest -->
    <?php if(PWA_ENABLED): ?>
    <link rel="manifest" href="<?php echo siteUrl('manifest.json'); ?>">
    <meta name="theme-color" content="<?php echo PWA_THEME_COLOR; ?>">
    <?php endif; ?>

    <!-- Preconnect to external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Font Awesome (Professional Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/<?php echo ICON_VERSION_FONTAWESOME; ?>/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo assetUrl('css/main.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetUrl('css/sections.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetUrl('css/animations.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetUrl('css/responsive.css'); ?>">

    <!-- CSRF Token -->
    <?php echo csrfMeta(); ?>

    <!-- Google Analytics -->
    <?php if(ANALYTICS_ENABLED && GOOGLE_ANALYTICS_ID): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo GOOGLE_ANALYTICS_ID; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo GOOGLE_ANALYTICS_ID; ?>');
    </script>
    <?php endif; ?>
</head>
<body class="<?php echo $isRTL ? 'rtl' : 'ltr'; ?>" data-theme="light">

<!-- Header -->
<header class="header" id="header">
    <div class="header-top">
        <div class="container">
            <div class="header-top-content">
                <!-- Contact Info -->
                <div class="header-contact">
                    <a href="tel:<?php echo WHATSAPP_NUMBER; ?>" class="header-contact-item">
                        <i class="fa-solid fa-phone"></i>
                        <span><?php echo PHONE_DISPLAY; ?></span>
                    </a>
                    <a href="mailto:<?php echo ADMIN_EMAIL; ?>" class="header-contact-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span><?php echo ADMIN_EMAIL; ?></span>
                    </a>
                    <span class="header-contact-item">
                        <i class="fa-solid fa-clock"></i>
                        <span><?php _e('available_24_7'); ?></span>
                    </span>
                </div>

                <!-- Language & Theme Switcher -->
                <div class="header-actions">
                    <!-- Language Switcher -->
                    <div class="language-switcher">
                        <button class="lang-btn" id="langBtn" aria-label="<?php _e('language'); ?>">
                            <i class="fa-solid fa-globe"></i>
                            <span><?php echo strtoupper($currentLang); ?></span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="lang-dropdown" id="langDropdown">
                            <a href="?lang=en" class="lang-option <?php echo $currentLang === 'en' ? 'active' : ''; ?>">
                                <span>English</span>
                                <?php if($currentLang === 'en'): ?>
                                <i class="fa-solid fa-check"></i>
                                <?php endif; ?>
                            </a>
                            <a href="?lang=ar" class="lang-option <?php echo $currentLang === 'ar' ? 'active' : ''; ?>">
                                <span>العربية</span>
                                <?php if($currentLang === 'ar'): ?>
                                <i class="fa-solid fa-check"></i>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>

                    <!-- Theme Toggle -->
                    <button class="theme-toggle" id="themeToggle" aria-label="<?php _e('theme'); ?>">
                        <i class="fa-solid fa-sun light-icon"></i>
                        <i class="fa-solid fa-moon dark-icon"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="header-main">
        <div class="container">
            <nav class="navbar" role="navigation">
                <!-- Logo -->
                <a href="<?php echo siteUrl(); ?>" class="logo">
                    <img src="<?php echo assetUrl('images/logo.png'); ?>" alt="<?php echo SITE_NAME; ?>" class="logo-img">
                    <span class="logo-text">
                        <strong>Fast & Fine</strong>
                        <small><?php _e('site_tagline'); ?></small>
                    </span>
                </a>

                <!-- Desktop Navigation -->
                <ul class="nav-menu" id="navMenu">
                    <li class="nav-item">
                        <a href="#home" class="nav-link active" data-section="home">
                            <i class="fa-solid fa-home"></i>
                            <span><?php _e('home'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#services" class="nav-link" data-section="services">
                            <i class="fa-solid fa-wrench"></i>
                            <span><?php _e('services'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#portfolio" class="nav-link" data-section="portfolio">
                            <i class="fa-solid fa-briefcase"></i>
                            <span><?php _e('portfolio'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#testimonials" class="nav-link" data-section="testimonials">
                            <i class="fa-solid fa-star"></i>
                            <span><?php _e('testimonials'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#about" class="nav-link" data-section="about">
                            <i class="fa-solid fa-info-circle"></i>
                            <span><?php _e('about'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#contact" class="nav-link" data-section="contact">
                            <i class="fa-solid fa-envelope"></i>
                            <span><?php _e('contact'); ?></span>
                        </a>
                    </li>
                </ul>

                <!-- CTA Buttons -->
                <div class="header-cta">
                    <a href="#quote" class="btn btn-outline" id="getQuoteBtn">
                        <i class="fa-solid fa-calculator"></i>
                        <span><?php _e('get_quote'); ?></span>
                    </a>
                    <a href="#booking" class="btn btn-primary" id="bookNowBtn">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span><?php _e('book_now'); ?></span>
                    </a>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="<?php _e('menu'); ?>">
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                </button>
            </nav>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="scroll-progress" id="scrollProgress"></div>
</header>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay">
    <div class="mobile-menu">
        <div class="mobile-menu-header">
            <div class="logo">
                <img src="<?php echo assetUrl('images/logo.png'); ?>" alt="<?php echo SITE_NAME; ?>" class="logo-img">
                <span class="logo-text">Fast & Fine</span>
            </div>
            <button class="mobile-menu-close" id="mobileMenuClose">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <nav class="mobile-nav">
            <a href="#home" class="mobile-nav-link active" data-section="home">
                <i class="fa-solid fa-home"></i>
                <span><?php _e('home'); ?></span>
            </a>
            <a href="#services" class="mobile-nav-link" data-section="services">
                <i class="fa-solid fa-wrench"></i>
                <span><?php _e('services'); ?></span>
            </a>
            <a href="#portfolio" class="mobile-nav-link" data-section="portfolio">
                <i class="fa-solid fa-briefcase"></i>
                <span><?php _e('portfolio'); ?></span>
            </a>
            <a href="#testimonials" class="mobile-nav-link" data-section="testimonials">
                <i class="fa-solid fa-star"></i>
                <span><?php _e('testimonials'); ?></span>
            </a>
            <a href="#about" class="mobile-nav-link" data-section="about">
                <i class="fa-solid fa-info-circle"></i>
                <span><?php _e('about'); ?></span>
            </a>
            <a href="#contact" class="mobile-nav-link" data-section="contact">
                <i class="fa-solid fa-envelope"></i>
                <span><?php _e('contact'); ?></span>
            </a>
        </nav>

        <div class="mobile-menu-cta">
            <a href="#quote" class="btn btn-outline btn-block">
                <i class="fa-solid fa-calculator"></i>
                <span><?php _e('get_quote'); ?></span>
            </a>
            <a href="#booking" class="btn btn-primary btn-block">
                <i class="fa-solid fa-calendar-check"></i>
                <span><?php _e('book_now'); ?></span>
            </a>
        </div>

        <div class="mobile-menu-contact">
            <a href="tel:<?php echo WHATSAPP_NUMBER; ?>" class="mobile-contact-item">
                <i class="fa-solid fa-phone"></i>
                <span><?php echo PHONE_DISPLAY; ?></span>
            </a>
            <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', WHATSAPP_NUMBER); ?>" class="mobile-contact-item" target="_blank">
                <i class="fa-brands fa-whatsapp"></i>
                <span><?php _e('chat_on_whatsapp'); ?></span>
            </a>
        </div>
    </div>
</div>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', WHATSAPP_NUMBER); ?>?text=<?php echo urlencode(__('hello')); ?>"
   class="whatsapp-float"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="<?php _e('chat_on_whatsapp'); ?>">
    <i class="fa-brands fa-whatsapp"></i>
</a>

<!-- Main Content -->
<main class="main-content" id="mainContent">
