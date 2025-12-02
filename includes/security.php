<?php
/**
 * Fast and Fine Technical Services FZE - Security Functions
 *
 * This file provides comprehensive security features including:
 * - CSRF token generation and validation
 * - Input sanitization and validation
 * - XSS protection
 * - Rate limiting
 * - Password hashing and verification
 * - Session security
 * - IP blocking
 * - File upload validation
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

// ============================================================================
// SESSION SECURITY
// ============================================================================

/**
 * Initialize secure session
 */
function initSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Configure session security
        ini_set('session.cookie_httponly', SESSION_COOKIE_HTTPONLY ? 1 : 0);
        ini_set('session.cookie_secure', SESSION_COOKIE_SECURE ? 1 : 0);
        ini_set('session.cookie_samesite', SESSION_COOKIE_SAMESITE);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

        session_name(SESSION_NAME);
        session_start();

        // Regenerate session ID periodically
        if (!isset($_SESSION['CREATED'])) {
            $_SESSION['CREATED'] = time();
        } elseif (time() - $_SESSION['CREATED'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['CREATED'] = time();
        }

        // Browser fingerprint validation
        if (!isset($_SESSION['FINGERPRINT'])) {
            $_SESSION['FINGERPRINT'] = generateFingerprint();
        } elseif ($_SESSION['FINGERPRINT'] !== generateFingerprint()) {
            // Potential session hijacking
            session_destroy();
            session_start();
            $_SESSION['FINGERPRINT'] = generateFingerprint();
        }
    }
}

/**
 * Generate browser fingerprint for session validation
 *
 * @return string
 */
function generateFingerprint() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    $acceptEnc = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';

    return hash('sha256', $userAgent . $acceptLang . $acceptEnc . CSRF_TOKEN_NAME);
}

/**
 * Destroy session securely
 */
function destroySession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}

// ============================================================================
// CSRF PROTECTION
// ============================================================================

/**
 * Generate CSRF token
 *
 * @return string
 */
function generateCSRFToken() {
    initSecureSession();

    if (!isset($_SESSION['csrf_tokens'])) {
        $_SESSION['csrf_tokens'] = [];
    }

    // Generate new token
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_tokens'][$token] = time();

    // Clean up expired tokens
    cleanupExpiredCSRFTokens();

    return $token;
}

/**
 * Validate CSRF token
 *
 * @param string $token Token to validate
 * @return bool
 */
function validateCSRFToken($token) {
    initSecureSession();

    if (!isset($_SESSION['csrf_tokens']) || empty($token)) {
        return false;
    }

    // Check if token exists and is not expired
    if (isset($_SESSION['csrf_tokens'][$token])) {
        $tokenTime = $_SESSION['csrf_tokens'][$token];

        if (time() - $tokenTime <= CSRF_TOKEN_EXPIRY) {
            // Token is valid, remove it (single use)
            unset($_SESSION['csrf_tokens'][$token]);
            return true;
        } else {
            // Token expired
            unset($_SESSION['csrf_tokens'][$token]);
            return false;
        }
    }

    return false;
}

/**
 * Clean up expired CSRF tokens
 */
function cleanupExpiredCSRFTokens() {
    if (isset($_SESSION['csrf_tokens'])) {
        $currentTime = time();
        foreach ($_SESSION['csrf_tokens'] as $token => $timestamp) {
            if ($currentTime - $timestamp > CSRF_TOKEN_EXPIRY) {
                unset($_SESSION['csrf_tokens'][$token]);
            }
        }
    }
}

/**
 * Get CSRF token input field (HTML)
 *
 * @return string
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
}

/**
 * Get CSRF token meta tag (HTML)
 *
 * @return string
 */
function csrfMeta() {
    $token = generateCSRFToken();
    return '<meta name="' . CSRF_TOKEN_NAME . '" content="' . $token . '">';
}

// ============================================================================
// INPUT SANITIZATION
// ============================================================================

/**
 * Sanitize string input
 *
 * @param string $input
 * @return string
 */
function sanitizeString($input) {
    if (is_null($input)) {
        return '';
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize email
 *
 * @param string $email
 * @return string|false
 */
function sanitizeEmail($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

/**
 * Sanitize URL
 *
 * @param string $url
 * @return string|false
 */
function sanitizeURL($url) {
    return filter_var(trim($url), FILTER_SANITIZE_URL);
}

/**
 * Sanitize integer
 *
 * @param mixed $input
 * @return int
 */
function sanitizeInt($input) {
    return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
}

/**
 * Sanitize float
 *
 * @param mixed $input
 * @return float
 */
function sanitizeFloat($input) {
    return (float) filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

/**
 * Sanitize phone number
 *
 * @param string $phone
 * @return string
 */
function sanitizePhone($phone) {
    // Remove all non-numeric characters except + at the start
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    // Ensure + is only at the start
    if (strpos($phone, '+') !== false) {
        $phone = '+' . str_replace('+', '', $phone);
    }
    return $phone;
}

/**
 * Sanitize array of inputs
 *
 * @param array $array
 * @param string $type Type of sanitization (string, email, int, etc.)
 * @return array
 */
function sanitizeArray($array, $type = 'string') {
    if (!is_array($array)) {
        return [];
    }

    $sanitized = [];
    foreach ($array as $key => $value) {
        $key = sanitizeString($key);

        switch ($type) {
            case 'email':
                $sanitized[$key] = sanitizeEmail($value);
                break;
            case 'int':
                $sanitized[$key] = sanitizeInt($value);
                break;
            case 'float':
                $sanitized[$key] = sanitizeFloat($value);
                break;
            case 'url':
                $sanitized[$key] = sanitizeURL($value);
                break;
            default:
                $sanitized[$key] = sanitizeString($value);
        }
    }

    return $sanitized;
}

// ============================================================================
// INPUT VALIDATION
// ============================================================================

/**
 * Validate email address
 *
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate URL
 *
 * @param string $url
 * @return bool
 */
function validateURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Validate phone number (international format)
 *
 * @param string $phone
 * @return bool
 */
function validatePhone($phone) {
    // International phone number format: +XXX XXXXXXXXXX
    return preg_match('/^\+?[1-9]\d{1,14}$/', str_replace([' ', '-', '(', ')'], '', $phone));
}

/**
 * Validate date format
 *
 * @param string $date
 * @param string $format
 * @return bool
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Validate string length
 *
 * @param string $string
 * @param int $min Minimum length
 * @param int $max Maximum length
 * @return bool
 */
function validateLength($string, $min = 1, $max = 255) {
    $length = mb_strlen($string, 'UTF-8');
    return $length >= $min && $length <= $max;
}

/**
 * Validate password strength
 *
 * @param string $password
 * @param int $minLength Minimum length (default from config)
 * @return array [bool success, string message]
 */
function validatePassword($password, $minLength = null) {
    if ($minLength === null) {
        $minLength = defined('ADMIN_PASSWORD_MIN_LENGTH') ? ADMIN_PASSWORD_MIN_LENGTH : 8;
    }

    if (strlen($password) < $minLength) {
        return [false, "Password must be at least {$minLength} characters long"];
    }

    if (!preg_match('/[A-Z]/', $password)) {
        return [false, "Password must contain at least one uppercase letter"];
    }

    if (!preg_match('/[a-z]/', $password)) {
        return [false, "Password must contain at least one lowercase letter"];
    }

    if (!preg_match('/[0-9]/', $password)) {
        return [false, "Password must contain at least one number"];
    }

    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return [false, "Password must contain at least one special character"];
    }

    return [true, "Password is strong"];
}

// ============================================================================
// PASSWORD HASHING
// ============================================================================

/**
 * Hash password securely
 *
 * @param string $password
 * @return string
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_HASH_ALGO, [
        'cost' => PASSWORD_HASH_COST
    ]);
}

/**
 * Verify password against hash
 *
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if password hash needs rehashing
 *
 * @param string $hash
 * @return bool
 */
function needsRehash($hash) {
    return password_needs_rehash($hash, PASSWORD_HASH_ALGO, [
        'cost' => PASSWORD_HASH_COST
    ]);
}

// ============================================================================
// RATE LIMITING
// ============================================================================

/**
 * Check rate limit for IP address
 *
 * @param string $action Action identifier (e.g., 'login', 'contact_form')
 * @param int $maxAttempts Maximum attempts allowed
 * @param int $timeWindow Time window in seconds
 * @return bool True if within rate limit, false if exceeded
 */
function checkRateLimit($action, $maxAttempts = null, $timeWindow = null) {
    if (!RATE_LIMIT_ENABLED) {
        return true;
    }

    $maxAttempts = $maxAttempts ?? RATE_LIMIT_REQUESTS;
    $timeWindow = $timeWindow ?? RATE_LIMIT_WINDOW;

    $ip = getUserIP();
    $key = "rate_limit_{$action}_{$ip}";

    initSecureSession();

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'count' => 1,
            'start_time' => time()
        ];
        return true;
    }

    $elapsed = time() - $_SESSION[$key]['start_time'];

    // Reset if time window has passed
    if ($elapsed > $timeWindow) {
        $_SESSION[$key] = [
            'count' => 1,
            'start_time' => time()
        ];
        return true;
    }

    // Increment counter
    $_SESSION[$key]['count']++;

    // Check if limit exceeded
    if ($_SESSION[$key]['count'] > $maxAttempts) {
        if (isDebugMode()) {
            error_log("[RATE LIMIT] IP {$ip} exceeded rate limit for action: {$action}");
        }
        return false;
    }

    return true;
}

/**
 * Get remaining time for rate limit cooldown
 *
 * @param string $action
 * @param int $timeWindow
 * @return int Seconds remaining
 */
function getRateLimitCooldown($action, $timeWindow = null) {
    $timeWindow = $timeWindow ?? RATE_LIMIT_WINDOW;
    $ip = getUserIP();
    $key = "rate_limit_{$action}_{$ip}";

    initSecureSession();

    if (!isset($_SESSION[$key])) {
        return 0;
    }

    $elapsed = time() - $_SESSION[$key]['start_time'];
    $remaining = $timeWindow - $elapsed;

    return max(0, $remaining);
}

// ============================================================================
// LOGIN SECURITY
// ============================================================================

/**
 * Track failed login attempt
 *
 * @param string $identifier User identifier (email, username, etc.)
 * @return bool True if account should be locked
 */
function trackFailedLogin($identifier) {
    $ip = getUserIP();
    $key = "failed_login_{$identifier}_{$ip}";

    initSecureSession();

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'count' => 1,
            'first_attempt' => time()
        ];
    } else {
        $_SESSION[$key]['count']++;
    }

    // Check if account should be locked
    if ($_SESSION[$key]['count'] >= MAX_LOGIN_ATTEMPTS) {
        $_SESSION[$key]['locked_until'] = time() + LOGIN_LOCKOUT_TIME;
        return true;
    }

    return false;
}

/**
 * Check if account is locked
 *
 * @param string $identifier
 * @return bool
 */
function isAccountLocked($identifier) {
    $ip = getUserIP();
    $key = "failed_login_{$identifier}_{$ip}";

    initSecureSession();

    if (!isset($_SESSION[$key]['locked_until'])) {
        return false;
    }

    // Check if lockout period has passed
    if (time() > $_SESSION[$key]['locked_until']) {
        unset($_SESSION[$key]);
        return false;
    }

    return true;
}

/**
 * Get lockout remaining time
 *
 * @param string $identifier
 * @return int Seconds remaining
 */
function getLockoutTime($identifier) {
    $ip = getUserIP();
    $key = "failed_login_{$identifier}_{$ip}";

    initSecureSession();

    if (!isset($_SESSION[$key]['locked_until'])) {
        return 0;
    }

    $remaining = $_SESSION[$key]['locked_until'] - time();
    return max(0, $remaining);
}

/**
 * Reset failed login attempts
 *
 * @param string $identifier
 */
function resetFailedLogins($identifier) {
    $ip = getUserIP();
    $key = "failed_login_{$identifier}_{$ip}";

    initSecureSession();

    if (isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
}

// ============================================================================
// IP SECURITY
// ============================================================================

/**
 * Get user's IP address
 *
 * @return string
 */
function getUserIP() {
    $ip = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/**
 * Check if IP is blocked
 *
 * @param string $ip
 * @return bool
 */
function isIPBlocked($ip = null) {
    if (!ENABLE_IP_BLOCKING) {
        return false;
    }

    $ip = $ip ?? getUserIP();
    $blockedIPs = BLOCKED_IPS ?? [];

    return in_array($ip, $blockedIPs);
}

/**
 * Block current request if IP is blocked
 */
function blockIfIPBlocked() {
    if (isIPBlocked()) {
        http_response_code(403);
        die('Access denied');
    }
}

// ============================================================================
// FILE UPLOAD SECURITY
// ============================================================================

/**
 * Validate uploaded file
 *
 * @param array $file $_FILES array element
 * @param array $allowedTypes Allowed MIME types
 * @param int $maxSize Maximum file size in bytes
 * @return array [bool success, string message, array|null fileInfo]
 */
function validateUploadedFile($file, $allowedTypes = null, $maxSize = null) {
    // Check if file was uploaded
    if (!isset($file['error']) || is_array($file['error'])) {
        return [false, 'Invalid file upload', null];
    }

    // Check for upload errors
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return [false, 'No file uploaded', null];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return [false, 'File size exceeds limit', null];
        default:
            return [false, 'Upload error occurred', null];
    }

    // Set defaults
    $allowedTypes = $allowedTypes ?? array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOCUMENT_TYPES);
    $maxSize = $maxSize ?? UPLOAD_MAX_SIZE;

    // Check file size
    if ($file['size'] > $maxSize) {
        $maxSizeMB = round($maxSize / 1024 / 1024, 2);
        return [false, "File size exceeds maximum of {$maxSizeMB}MB", null];
    }

    // Verify MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        return [false, 'File type not allowed', null];
    }

    // Additional image validation
    if (in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return [false, 'Invalid image file', null];
        }
    }

    // Generate secure filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $secureFilename = bin2hex(random_bytes(16)) . '.' . $extension;

    return [true, 'File is valid', [
        'original_name' => basename($file['name']),
        'secure_name' => $secureFilename,
        'mime_type' => $mimeType,
        'size' => $file['size'],
        'tmp_name' => $file['tmp_name']
    ]];
}

// ============================================================================
// XSS PROTECTION
// ============================================================================

/**
 * Escape output for HTML context
 *
 * @param string $string
 * @return string
 */
function escapeHTML($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Escape output for JavaScript context
 *
 * @param string $string
 * @return string
 */
function escapeJS($string) {
    return json_encode($string, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

/**
 * Escape output for URL context
 *
 * @param string $string
 * @return string
 */
function escapeURL($string) {
    return urlencode($string);
}

/**
 * Strip all HTML tags except allowed ones
 *
 * @param string $string
 * @param array $allowedTags
 * @return string
 */
function stripTags($string, $allowedTags = []) {
    if (empty($allowedTags)) {
        return strip_tags($string);
    }

    $allowed = '<' . implode('><', $allowedTags) . '>';
    return strip_tags($string, $allowed);
}

// ============================================================================
// SECURITY HEADERS
// ============================================================================

/**
 * Set security headers (called automatically from config.php)
 */
function setSecurityHeaders() {
    if (!headers_sent()) {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }
}

// ============================================================================
// INITIALIZATION
// ============================================================================

// Initialize session and check IP blocking
initSecureSession();
blockIfIPBlocked();

if (isDebugMode()) {
    error_log('[SECURITY] Security functions initialized');
}
