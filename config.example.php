<?php
/**
 * Fast and Fine Technical Services FZE - Configuration Template
 *
 * SETUP INSTRUCTIONS:
 * 1. Copy this file to config.php
 * 2. Update all placeholder values with your actual settings
 * 3. NEVER commit config.php to version control
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Define application constant (prevent double inclusion)
if (!defined('FAST_FINE_APP')) {
    define('FAST_FINE_APP', true);
}

// ============================================================================
// ENVIRONMENT SETTINGS
// ============================================================================
define('ENVIRONMENT', 'development');           // development, staging, production
define('DEBUG_MODE', true);                     // Show detailed errors (set to false in production)
define('MAINTENANCE_MODE', false);              // Enable maintenance mode

// ============================================================================
// DATABASE CONFIGURATION
// ============================================================================

// For LOCAL DEVELOPMENT (SQLite):
define('DB_TYPE', 'sqlite');                    // Using SQLite for local dev
define('DB_PATH', __DIR__ . '/database/fastfine.db');  // SQLite database file
define('DB_CHARSET', 'utf8mb4');

// For PRODUCTION (MySQL):
// Uncomment these and update with your MySQL credentials
// define('DB_TYPE', 'mysql');
// define('DB_HOST', 'localhost');              // Your database host
// define('DB_NAME', 'your_database_name');     // Your database name
// define('DB_USER', 'your_database_user');     // Your database username
// define('DB_PASSWORD', 'your_database_password'); // Your database password
// define('DB_PORT', 3306);
// define('DB_CHARSET', 'utf8mb4');

// Fallback MySQL settings (not used with SQLite)
define('DB_HOST', 'localhost');
define('DB_NAME', 'fastfine_db');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_PORT', 3306);

// ============================================================================
// SITE INFORMATION
// ============================================================================
define('SITE_NAME', 'Fast and Fine Technical Services FZE');
define('SITE_TAGLINE', 'Professional Technical Services in Dubai');
define('SITE_URL', 'http://localhost:8000');    // Update with your domain: https://yourdomain.com
define('ADMIN_EMAIL', 'admin@fastandfine.com'); // Update with your email
define('NO_REPLY_EMAIL', 'noreply@fastandfine.com'); // Update with your no-reply email

// Language Settings
define('DEFAULT_LANGUAGE', 'en');               // Default language (en or ar)
define('SUPPORTED_LANGUAGES', ['en', 'ar']);    // Supported languages

// ============================================================================
// CONTACT INFORMATION
// ============================================================================
define('WHATSAPP_NUMBER', '+971501234567');     // Update with your WhatsApp number
define('PHONE_DISPLAY', '+971 50 123 4567');    // Update with formatted phone display
define('BUSINESS_ADDRESS', 'Dubai, United Arab Emirates'); // Update with your address
define('COMPANY_ADDRESS', 'Dubai, United Arab Emirates');  // Alias for BUSINESS_ADDRESS
define('BUSINESS_HOURS', 'Open 24/7');          // Update with your business hours

// ============================================================================
// SOCIAL MEDIA LINKS
// ============================================================================
define('FACEBOOK_URL', 'https://facebook.com/fastandfine');     // Update with your Facebook page
define('INSTAGRAM_URL', 'https://instagram.com/fastandfine');   // Update with your Instagram profile
define('TWITTER_URL', 'https://twitter.com/fastandfine');       // Update with your Twitter profile
define('LINKEDIN_URL', 'https://linkedin.com/company/fastandfine'); // Update with your LinkedIn page
define('GOOGLE_BUSINESS_URL', 'https://g.page/r/YOUR_GOOGLE_BUSINESS_ID/review'); // Update with your Google Business URL

// Social media aliases (for compatibility)
define('SOCIAL_FACEBOOK', FACEBOOK_URL);
define('SOCIAL_INSTAGRAM', INSTAGRAM_URL);
define('SOCIAL_TWITTER', TWITTER_URL);
define('SOCIAL_LINKEDIN', LINKEDIN_URL);

// ============================================================================
// GOOGLE SERVICES
// ============================================================================
define('GOOGLE_MAPS_API_KEY', '');              // Add your Google Maps API key (optional)
define('GOOGLE_MAPS_LINK', 'https://maps.google.com/?q=Dubai,UAE');  // Update with your location
define('GOOGLE_ANALYTICS_ID', '');              // Add your Google Analytics ID (e.g., G-XXXXXXXXXX)

// ============================================================================
// EMAIL CONFIGURATION
// ============================================================================
define('EMAIL_ENABLED', false);                 // Enable emails (set to true in production)
define('EMAIL_SERVICE', 'php_mail');            // php_mail, sendgrid, mailgun

// SendGrid Configuration (if using SendGrid)
define('SENDGRID_API_KEY', '');                 // Your SendGrid API key
define('SENDGRID_FROM_EMAIL', NO_REPLY_EMAIL);
define('SENDGRID_FROM_NAME', SITE_NAME);

// Mailgun Configuration (if using Mailgun)
define('MAILGUN_API_KEY', '');                  // Your Mailgun API key
define('MAILGUN_DOMAIN', '');                   // Your Mailgun domain

// ============================================================================
// SECURITY SETTINGS
// ============================================================================
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LIFETIME', 3600);            // 1 hour (deprecated - use CSRF_TOKEN_EXPIRY)
define('CSRF_TOKEN_EXPIRY', 3600);              // 1 hour
define('SESSION_LIFETIME', 3600);               // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900);              // 15 minutes
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 12);

// Session Configuration
define('SESSION_NAME', 'FASTFINE_SESSION');
define('SESSION_COOKIE_HTTPONLY', true);
define('SESSION_COOKIE_SECURE', ENVIRONMENT === 'production');  // Only secure in production
define('SESSION_COOKIE_SAMESITE', 'Lax');

// Rate Limiting
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_REQUESTS', 100);             // Requests per window
define('RATE_LIMIT_WINDOW', 60);                // Time window in seconds

// IP Blocking
define('ENABLE_IP_BLOCKING', false);            // Disable in local dev
define('BLOCKED_IPS', []);                      // Array of blocked IP addresses

// ============================================================================
// FILE UPLOAD SETTINGS
// ============================================================================
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024);     // 5MB
define('MAX_UPLOAD_SIZE', UPLOAD_MAX_SIZE);     // Alias for compatibility
define('MAX_UPLOAD_FILES', 5);                  // Maximum number of files per upload
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_DOCUMENT_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
define('UPLOAD_PATH', __DIR__ . '/uploads/');

// ============================================================================
// FEATURE FLAGS
// ============================================================================
define('FEATURE_LIVE_CHAT', true);
define('FEATURE_BOOKING', true);
define('FEATURE_NEWSLETTER', true);
define('ANALYTICS_ENABLED', false);             // Enable in production (requires GOOGLE_ANALYTICS_ID)
define('PWA_ENABLED', false);                   // Enable Progressive Web App features

// ============================================================================
// BRANDING COLORS
// ============================================================================
define('BRAND_NAVY', '#002D57');
define('BRAND_CYAN', '#009FE3');
define('BRAND_YELLOW', '#FDB913');
define('PWA_THEME_COLOR', BRAND_NAVY);

// ============================================================================
// CURRENCY SETTINGS
// ============================================================================
define('CURRENCY_SYMBOL', 'AED');               // UAE Dirham
define('CURRENCY_POSITION', 'before');          // before or after amount
define('CURRENCY_DECIMALS', 2);

// ============================================================================
// DATE/TIME FORMATTING
// ============================================================================
define('DATE_FORMAT', 'Y-m-d');                 // Date format
define('TIME_FORMAT', 'H:i');                   // Time format (24-hour)
define('DATETIME_FORMAT', 'Y-m-d H:i:s');       // Datetime format
define('DEFAULT_TIMEZONE', 'Asia/Dubai');        // Default timezone

// ============================================================================
// BOOKING SETTINGS
// ============================================================================
define('BOOKING_ID_PREFIX', 'FF');              // Booking ID prefix
define('BOOKING_ID_LENGTH', 10);                // Total length including prefix

// Size multipliers for pricing
define('SIZE_MULTIPLIER_SMALL', 1.0);
define('SIZE_MULTIPLIER_LARGE', 1.5);

// Urgency multipliers for pricing
define('URGENCY_STANDARD', 1.0);
define('URGENCY_PRIORITY', 1.3);
define('URGENCY_EMERGENCY', 1.8);

// ============================================================================
// ADDITIONAL EMAIL SETTINGS
// ============================================================================
define('SUPPORT_EMAIL', ADMIN_EMAIL);           // Support email

// ============================================================================
// PATH SETTINGS
// ============================================================================
define('BASE_PATH', __DIR__);

// ============================================================================
// ICON VERSIONS
// ============================================================================
define('ICON_VERSION_FONTAWESOME', '6.4.0');

// ============================================================================
// SEO SETTINGS
// ============================================================================
define('SEO_TITLE', 'Fast & Fine Technical Services | Professional Services in Dubai');
define('SEO_DESCRIPTION', 'Professional technical services in Dubai including building cleaning, carpentry, plumbing, AC services, and more. Available 24/7.');
define('SEO_KEYWORDS', 'technical services dubai, building cleaning, carpentry, plumbing, AC services, Dubai maintenance');
define('SEO_AUTHOR', 'Fast and Fine Technical Services FZE');
define('SEO_OG_IMAGE', SITE_URL . '/assets/images/og-image.jpg');

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Get base URL
 */
function siteUrl($path = '') {
    $url = SITE_URL;
    if ($path) {
        $url .= '/' . ltrim($path, '/');
    }
    return $url;
}

/**
 * Get asset URL
 */
function assetUrl($path) {
    return siteUrl('assets/' . ltrim($path, '/'));
}

/**
 * Check if debug mode is enabled
 */
function isDebugMode() {
    return defined('DEBUG_MODE') && DEBUG_MODE === true;
}

// ============================================================================
// ERROR REPORTING
// ============================================================================
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/logs/php-error.log');
}

// ============================================================================
// SESSION CONFIGURATION
// ============================================================================
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax');

    // Only require HTTPS in production
    if (ENVIRONMENT === 'production') {
        ini_set('session.cookie_secure', 1);
    }

    session_start();
}

// ============================================================================
// TIMEZONE
// ============================================================================
date_default_timezone_set(DEFAULT_TIMEZONE);

// ============================================================================
// DEVELOPMENT NOTICES
// ============================================================================
if (isDebugMode()) {
    echo "<!-- DEVELOPMENT MODE ENABLED -->\n";
    if (DB_TYPE === 'sqlite') {
        echo "<!-- Using SQLite database: " . DB_PATH . " -->\n";
    }
}
