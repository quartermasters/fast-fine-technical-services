<?php
/**
 * Fast and Fine Technical Services FZE - Helper Functions
 *
 * This file provides general helper functions for:
 * - Translation and localization
 * - Date and time formatting
 * - Email sending
 * - HTTP responses
 * - Currency formatting
 * - String and array utilities
 * - Booking ID generation
 * - Analytics tracking
 *
 * @package FastAndFine
 * @version 1.0.0
 * @author Fast and Fine Technical Services FZE
 */

// Prevent direct access
if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

// Require dependencies
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db-connect.php';
require_once __DIR__ . '/security.php';

// ============================================================================
// TRANSLATION & LOCALIZATION
// ============================================================================

/**
 * Get current language from session or default
 *
 * @return string Language code (en, ar)
 */
function getCurrentLanguage() {
    initSecureSession();

    if (isset($_SESSION['language']) && in_array($_SESSION['language'], SUPPORTED_LANGUAGES)) {
        return $_SESSION['language'];
    }

    // Check URL parameter
    if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGUAGES)) {
        $_SESSION['language'] = $_GET['lang'];
        return $_GET['lang'];
    }

    // Check browser language
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if (in_array($browserLang, SUPPORTED_LANGUAGES)) {
            $_SESSION['language'] = $browserLang;
            return $browserLang;
        }
    }

    return DEFAULT_LANGUAGE;
}

/**
 * Set current language
 *
 * @param string $lang Language code
 * @return bool
 */
function setLanguage($lang) {
    if (!in_array($lang, SUPPORTED_LANGUAGES)) {
        return false;
    }

    initSecureSession();
    $_SESSION['language'] = $lang;
    return true;
}

/**
 * Check if current language is RTL
 *
 * @return bool
 */
function isRTL() {
    return getCurrentLanguage() === 'ar';
}

/**
 * Get translation for a key
 *
 * @param string $key Translation key
 * @param array $replacements Placeholder replacements
 * @return string
 */
function translate($key, $replacements = []) {
    static $translations = null;

    if ($translations === null) {
        $translationsFile = __DIR__ . '/translations.php';
        if (file_exists($translationsFile)) {
            $translations = require $translationsFile;
        } else {
            $translations = [];
        }
    }

    $lang = getCurrentLanguage();
    $text = $translations[$lang][$key] ?? $key;

    // Replace placeholders
    if (!empty($replacements)) {
        foreach ($replacements as $placeholder => $value) {
            $text = str_replace("{{$placeholder}}", $value, $text);
        }
    }

    return $text;
}

/**
 * Translation shorthand function
 *
 * @param string $key
 * @param array $replacements
 * @return string
 */
function __($key, $replacements = []) {
    return translate($key, $replacements);
}

/**
 * Echo translation
 *
 * @param string $key
 * @param array $replacements
 */
function _e($key, $replacements = []) {
    echo translate($key, $replacements);
}

// ============================================================================
// DATE & TIME FORMATTING
// ============================================================================

/**
 * Format date according to locale
 *
 * @param string|int $date Date string or timestamp
 * @param string $format Custom format (optional)
 * @return string
 */
function formatDate($date, $format = null) {
    if (is_numeric($date)) {
        $timestamp = $date;
    } else {
        $timestamp = strtotime($date);
    }

    if (!$timestamp) {
        return $date;
    }

    $format = $format ?? DATE_FORMAT;
    return date($format, $timestamp);
}

/**
 * Format time according to locale
 *
 * @param string|int $time Time string or timestamp
 * @param string $format Custom format (optional)
 * @return string
 */
function formatTime($time, $format = null) {
    if (is_numeric($time)) {
        $timestamp = $time;
    } else {
        $timestamp = strtotime($time);
    }

    if (!$timestamp) {
        return $time;
    }

    $format = $format ?? TIME_FORMAT;
    return date($format, $timestamp);
}

/**
 * Format datetime according to locale
 *
 * @param string|int $datetime Datetime string or timestamp
 * @param string $format Custom format (optional)
 * @return string
 */
function formatDateTime($datetime, $format = null) {
    if (is_numeric($datetime)) {
        $timestamp = $datetime;
    } else {
        $timestamp = strtotime($datetime);
    }

    if (!$timestamp) {
        return $datetime;
    }

    $format = $format ?? DATETIME_FORMAT;
    return date($format, $timestamp);
}

/**
 * Get time ago string (e.g., "2 hours ago")
 *
 * @param string|int $datetime
 * @return string
 */
function timeAgo($datetime) {
    if (is_numeric($datetime)) {
        $timestamp = $datetime;
    } else {
        $timestamp = strtotime($datetime);
    }

    $diff = time() - $timestamp;

    if ($diff < 60) {
        return __('just_now');
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return __('minutes_ago', ['count' => $mins]);
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return __('hours_ago', ['count' => $hours]);
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return __('days_ago', ['count' => $days]);
    } else {
        return formatDate($timestamp);
    }
}

/**
 * Convert datetime to Dubai timezone
 *
 * @param string $datetime
 * @return DateTime
 */
function toDubaiTime($datetime) {
    $dt = new DateTime($datetime);
    $dt->setTimezone(new DateTimeZone(DEFAULT_TIMEZONE));
    return $dt;
}

// ============================================================================
// EMAIL FUNCTIONS
// ============================================================================

/**
 * Send email using configured service
 *
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param array $options Additional options
 * @return bool
 */
function sendEmail($to, $subject, $body, $options = []) {
    $from = $options['from'] ?? NO_REPLY_EMAIL;
    $fromName = $options['from_name'] ?? SITE_NAME;
    $replyTo = $options['reply_to'] ?? SUPPORT_EMAIL;

    switch (EMAIL_SERVICE) {
        case 'sendgrid':
            return sendEmailSendGrid($to, $subject, $body, $from, $fromName, $replyTo);

        case 'mailgun':
            return sendEmailMailgun($to, $subject, $body, $from, $fromName, $replyTo);

        case 'aws_ses':
            return sendEmailAWSSES($to, $subject, $body, $from, $fromName, $replyTo);

        default:
            return sendEmailPHP($to, $subject, $body, $from, $fromName, $replyTo);
    }
}

/**
 * Send email using PHP mail()
 *
 * @param string $to
 * @param string $subject
 * @param string $body
 * @param string $from
 * @param string $fromName
 * @param string $replyTo
 * @return bool
 */
function sendEmailPHP($to, $subject, $body, $from, $fromName, $replyTo) {
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        "From: {$fromName} <{$from}>",
        "Reply-To: {$replyTo}",
        'X-Mailer: PHP/' . phpversion()
    ];

    $success = mail($to, $subject, $body, implode("\r\n", $headers));

    if ($success && isDebugMode()) {
        error_log("[EMAIL] Sent to {$to}: {$subject}");
    }

    return $success;
}

/**
 * Send email using SendGrid API
 *
 * @param string $to
 * @param string $subject
 * @param string $body
 * @param string $from
 * @param string $fromName
 * @param string $replyTo
 * @return bool
 */
function sendEmailSendGrid($to, $subject, $body, $from, $fromName, $replyTo) {
    // Implementation would use SendGrid API
    // For now, fallback to PHP mail
    return sendEmailPHP($to, $subject, $body, $from, $fromName, $replyTo);
}

/**
 * Send email using Mailgun API
 *
 * @param string $to
 * @param string $subject
 * @param string $body
 * @param string $from
 * @param string $fromName
 * @param string $replyTo
 * @return bool
 */
function sendEmailMailgun($to, $subject, $body, $from, $fromName, $replyTo) {
    // Implementation would use Mailgun API
    // For now, fallback to PHP mail
    return sendEmailPHP($to, $subject, $body, $from, $fromName, $replyTo);
}

/**
 * Send email using AWS SES
 *
 * @param string $to
 * @param string $subject
 * @param string $body
 * @param string $from
 * @param string $fromName
 * @param string $replyTo
 * @return bool
 */
function sendEmailAWSSES($to, $subject, $body, $from, $fromName, $replyTo) {
    // Implementation would use AWS SES API
    // For now, fallback to PHP mail
    return sendEmailPHP($to, $subject, $body, $from, $fromName, $replyTo);
}

/**
 * Load email template
 *
 * @param string $template Template name
 * @param array $data Template data
 * @return string
 */
function loadEmailTemplate($template, $data = []) {
    $templatePath = BASE_PATH . "/includes/email-templates/{$template}.php";

    if (!file_exists($templatePath)) {
        return '';
    }

    ob_start();
    extract($data);
    include $templatePath;
    return ob_get_clean();
}

// ============================================================================
// HTTP RESPONSE FUNCTIONS
// ============================================================================

/**
 * Send JSON response
 *
 * @param array $data Response data
 * @param int $statusCode HTTP status code
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Send success JSON response
 *
 * @param mixed $data
 * @param string $message
 */
function jsonSuccess($data = null, $message = 'Success') {
    jsonResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], 200);
}

/**
 * Send error JSON response
 *
 * @param string $message
 * @param int $statusCode
 * @param array $errors
 */
function jsonError($message = 'Error', $statusCode = 400, $errors = []) {
    jsonResponse([
        'success' => false,
        'message' => $message,
        'errors' => $errors
    ], $statusCode);
}

/**
 * Redirect to URL
 *
 * @param string $url
 * @param int $statusCode
 */
function redirect($url, $statusCode = 302) {
    header("Location: {$url}", true, $statusCode);
    exit;
}

/**
 * Redirect back to previous page
 */
function redirectBack() {
    $referer = $_SERVER['HTTP_REFERER'] ?? siteUrl();
    redirect($referer);
}

// ============================================================================
// CURRENCY & FORMATTING
// ============================================================================

/**
 * Format price with currency
 *
 * @param float $amount
 * @param bool $includeCurrency
 * @return string
 */
function formatPrice($amount, $includeCurrency = true) {
    $formatted = number_format($amount, 2, '.', ',');

    if (!$includeCurrency) {
        return $formatted;
    }

    if (CURRENCY_POSITION === 'before') {
        return CURRENCY_SYMBOL . ' ' . $formatted;
    } else {
        return $formatted . ' ' . CURRENCY_SYMBOL;
    }
}

/**
 * Format number
 *
 * @param float $number
 * @param int $decimals
 * @return string
 */
function formatNumber($number, $decimals = 0) {
    return number_format($number, $decimals, '.', ',');
}

/**
 * Format phone number for display
 *
 * @param string $phone
 * @return string
 */
function formatPhoneDisplay($phone) {
    // Format: +971-52-739-8010
    $cleaned = preg_replace('/[^0-9+]/', '', $phone);

    if (strpos($cleaned, '+971') === 0) {
        // UAE number
        $parts = str_split(substr($cleaned, 4));
        return '+971-' . implode('', array_slice($parts, 0, 2)) . '-' .
               implode('', array_slice($parts, 2, 3)) . '-' .
               implode('', array_slice($parts, 5));
    }

    return $cleaned;
}

// ============================================================================
// STRING UTILITIES
// ============================================================================

/**
 * Generate slug from string
 *
 * @param string $string
 * @return string
 */
function generateSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

/**
 * Truncate string to specified length
 *
 * @param string $string
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate($string, $length = 100, $suffix = '...') {
    if (mb_strlen($string, 'UTF-8') <= $length) {
        return $string;
    }

    return mb_substr($string, 0, $length, 'UTF-8') . $suffix;
}

/**
 * Generate random string
 *
 * @param int $length
 * @return string
 */
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Generate OTP code
 *
 * @param int $length
 * @return string
 */
function generateOTP($length = 6) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= random_int(0, 9);
    }
    return $otp;
}

// ============================================================================
// ARRAY UTILITIES
// ============================================================================

/**
 * Get value from array by key with default
 *
 * @param array $array
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function arrayGet($array, $key, $default = null) {
    return $array[$key] ?? $default;
}

/**
 * Check if all keys exist in array
 *
 * @param array $array
 * @param array $keys
 * @return bool
 */
function arrayHasKeys($array, $keys) {
    foreach ($keys as $key) {
        if (!array_key_exists($key, $array)) {
            return false;
        }
    }
    return true;
}

/**
 * Filter array by keys
 *
 * @param array $array
 * @param array $keys
 * @return array
 */
function arrayOnly($array, $keys) {
    return array_intersect_key($array, array_flip($keys));
}

// ============================================================================
// BOOKING UTILITIES
// ============================================================================

/**
 * Generate unique booking ID
 *
 * @return string
 */
function generateBookingID() {
    $prefix = BOOKING_ID_PREFIX;
    $length = BOOKING_ID_LENGTH - strlen($prefix);

    do {
        $number = '';
        for ($i = 0; $i < $length; $i++) {
            $number .= random_int(0, 9);
        }
        $bookingID = $prefix . $number;

        // Check if ID already exists
        $exists = dbExists('bookings', 'booking_id', $bookingID);
    } while ($exists);

    return $bookingID;
}

/**
 * Calculate quote price based on service and options
 *
 * @param int $serviceId
 * @param array $options
 * @return float
 */
function calculateQuotePrice($serviceId, $options = []) {
    // Get base price from service
    $service = dbSelectOne("SELECT starting_price FROM services WHERE id = :id", ['id' => $serviceId]);

    if (!$service) {
        return 0.00;
    }

    $basePrice = (float) $service['starting_price'];

    // Apply size multiplier
    $propertySize = $options['property_size'] ?? 'small';
    $sizeMultiplier = $propertySize === 'large' ? SIZE_MULTIPLIER_LARGE : SIZE_MULTIPLIER_SMALL;

    // Apply urgency multiplier
    $urgency = $options['urgency'] ?? 'standard';
    $urgencyMultiplier = match($urgency) {
        'emergency' => URGENCY_EMERGENCY,
        'priority' => URGENCY_PRIORITY,
        default => URGENCY_STANDARD
    };

    $totalPrice = $basePrice * $sizeMultiplier * $urgencyMultiplier;

    return round($totalPrice, 2);
}

// ============================================================================
// ANALYTICS TRACKING
// ============================================================================

/**
 * Track analytics event
 *
 * @param string $eventType Event type
 * @param array $details Event details
 * @return bool
 */
function trackEvent($eventType, $details = []) {
    if (!ANALYTICS_ENABLED) {
        return false;
    }

    try {
        $ip = getUserIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        // Detect device type
        $deviceType = 'desktop';
        if (preg_match('/mobile|android|iphone|ipad|tablet/i', $userAgent)) {
            if (preg_match('/ipad|tablet/i', $userAgent)) {
                $deviceType = 'tablet';
            } else {
                $deviceType = 'mobile';
            }
        }

        // Get session ID
        initSecureSession();
        $sessionId = session_id();

        dbInsert("
            INSERT INTO analytics (
                event_type, event_category, page_url, referrer_url,
                ip_address, user_agent, device_type, session_id, details
            ) VALUES (
                :event_type, :event_category, :page_url, :referrer_url,
                :ip_address, :user_agent, :device_type, :session_id, :details
            )
        ", [
            'event_type' => $eventType,
            'event_category' => $details['category'] ?? 'general',
            'page_url' => $_SERVER['REQUEST_URI'] ?? '',
            'referrer_url' => $referer,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'session_id' => $sessionId,
            'details' => json_encode($details)
        ]);

        return true;

    } catch (Exception $e) {
        if (isDebugMode()) {
            error_log("[ANALYTICS ERROR] " . $e->getMessage());
        }
        return false;
    }
}

// ============================================================================
// MISCELLANEOUS UTILITIES
// ============================================================================

/**
 * Check if request is AJAX
 *
 * @return bool
 */
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Check if request method is POST
 *
 * @return bool
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request method is GET
 *
 * @return bool
 */
function isGet() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Get current URL
 *
 * @return string
 */
function getCurrentURL() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Dump variable and die (debugging)
 *
 * @param mixed $var
 */
function dd($var) {
    if (!isDebugMode()) {
        return;
    }

    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

/**
 * Log debug message
 *
 * @param string $message
 * @param mixed $data
 */
function logDebug($message, $data = null) {
    if (!isDebugMode()) {
        return;
    }

    $log = "[DEBUG] {$message}";
    if ($data !== null) {
        $log .= ' | Data: ' . json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    error_log($log);
}

/**
 * Get file size in human-readable format
 *
 * @param int $bytes
 * @return string
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor]);
}

/**
 * Check if site is in maintenance mode
 *
 * @return bool
 */
function isMaintenanceMode() {
    return defined('MAINTENANCE_MODE') && MAINTENANCE_MODE === true;
}

/**
 * Display maintenance page if enabled
 */
function checkMaintenanceMode() {
    if (isMaintenanceMode()) {
        http_response_code(503);
        include BASE_PATH . '/maintenance.html';
        exit;
    }
}

// ============================================================================
// INITIALIZATION
// ============================================================================

if (isDebugMode()) {
    error_log('[FUNCTIONS] Helper functions initialized');
}

/**
 * USAGE EXAMPLES:
 *
 * // Translation
 * echo __('welcome_message');
 * _e('contact_us');
 *
 * // Date formatting
 * echo formatDate('2025-12-02');
 * echo formatDateTime('2025-12-02 14:30:00');
 * echo timeAgo('2025-12-01 10:00:00');
 *
 * // Email
 * sendEmail('customer@example.com', 'Booking Confirmation', $emailBody);
 *
 * // JSON responses
 * jsonSuccess(['booking_id' => 'FF12345'], 'Booking created successfully');
 * jsonError('Invalid input', 400);
 *
 * // Currency
 * echo formatPrice(150.50); // د.إ 150.50
 *
 * // Booking
 * $bookingID = generateBookingID(); // FF12345678
 * $price = calculateQuotePrice(1, ['urgency' => 'emergency', 'property_size' => 'large']);
 *
 * // Analytics
 * trackEvent('booking_created', ['service_id' => 1, 'amount' => 500]);
 */
