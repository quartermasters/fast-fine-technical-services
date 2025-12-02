<?php
/**
 * Fast and Fine Technical Services FZE - Admin Authentication Functions
 *
 * Provides secure authentication and authorization functions for admin panel.
 *
 * Features:
 * - Secure password hashing with bcrypt
 * - Login attempt tracking and rate limiting
 * - Session-based authentication
 * - Role-based access control (RBAC)
 * - Account lockout protection
 * - Two-factor authentication ready
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Prevent direct access
if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

// Require dependencies
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db-connect.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/functions.php';

// ============================================================================
// AUTHENTICATION FUNCTIONS
// ============================================================================

/**
 * Authenticate admin user with username and password
 *
 * @param string $username Username or email
 * @param string $password Plain text password
 * @return array|false User data array on success, false on failure
 */
function authenticateAdmin($username, $password) {
    global $db;

    try {
        // Clean input
        $username = trim($username);

        // Check for account lockout
        if (isAccountLocked($username)) {
            error_log("[AUTH] Account locked: {$username}");
            return false;
        }

        // Get user from database
        $stmt = $db->prepare("
            SELECT
                id, username, email, password_hash, role, status,
                full_name, last_login, login_attempts, locked_until
            FROM users
            WHERE (username = :username OR email = :username)
            AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Log failed attempt (user not found)
            recordFailedLogin($username);
            error_log("[AUTH] User not found: {$username}");
            return false;
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            // Log failed attempt (wrong password)
            recordFailedLogin($username, $user['id']);
            error_log("[AUTH] Invalid password for user: {$username}");
            return false;
        }

        // Check if password needs rehashing (algorithm update)
        if (password_needs_rehash($user['password_hash'], PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST])) {
            $newHash = password_hash($password, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);
            $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$newHash, $user['id']]);
        }

        // Reset login attempts
        resetLoginAttempts($user['id']);

        // Update last login
        $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);

        // Remove sensitive data
        unset($user['password_hash']);

        // Log successful login
        error_log("[AUTH] Successful login: {$username} (ID: {$user['id']})");

        return $user;

    } catch (Exception $e) {
        error_log("[AUTH ERROR] Authentication failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Create admin session after successful authentication
 *
 * @param array $user User data array
 * @return bool Success status
 */
function createAdminSession($user) {
    initSecureSession();

    try {
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);

        // Store user data in session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_email'] = $user['email'];
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['admin_full_name'] = $user['full_name'];
        $_SESSION['admin_login_time'] = time();
        $_SESSION['admin_last_activity'] = time();
        $_SESSION['admin_ip'] = getClientIP();
        $_SESSION['admin_user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Track analytics
        if (ANALYTICS_ENABLED) {
            trackEvent('admin_login', [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]);
        }

        return true;

    } catch (Exception $e) {
        error_log("[AUTH ERROR] Session creation failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if admin is logged in
 *
 * @return bool True if logged in, false otherwise
 */
function isAdminLoggedIn() {
    initSecureSession();

    if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }

    // Check session timeout (1 hour of inactivity)
    $timeout = SESSION_LIFETIME;
    if (isset($_SESSION['admin_last_activity']) && (time() - $_SESSION['admin_last_activity'] > $timeout)) {
        destroyAdminSession();
        return false;
    }

    // Update last activity
    $_SESSION['admin_last_activity'] = time();

    // Verify IP hasn't changed (optional, can be disabled if admin uses VPN)
    if (VERIFY_SESSION_IP && isset($_SESSION['admin_ip'])) {
        if ($_SESSION['admin_ip'] !== getClientIP()) {
            error_log("[AUTH] IP address mismatch - session destroyed");
            destroyAdminSession();
            return false;
        }
    }

    return true;
}

/**
 * Require admin login - redirect to login page if not authenticated
 *
 * @param string|null $requiredRole Required role (optional)
 * @return void
 */
function requireAdminLogin($requiredRole = null) {
    if (!isAdminLoggedIn()) {
        // Store intended destination
        $_SESSION['admin_redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';

        // Redirect to login
        header('Location: ' . siteUrl('admin/login.php'));
        exit;
    }

    // Check role if specified
    if ($requiredRole && !hasAdminRole($requiredRole)) {
        http_response_code(403);
        die('Access denied. Insufficient permissions.');
    }
}

/**
 * Check if admin has specific role
 *
 * @param string $role Required role (admin, manager, viewer)
 * @return bool True if user has role, false otherwise
 */
function hasAdminRole($role) {
    initSecureSession();

    if (!isAdminLoggedIn()) {
        return false;
    }

    $userRole = $_SESSION['admin_role'] ?? '';

    // Admin role has access to everything
    if ($userRole === 'admin') {
        return true;
    }

    // Check specific role
    return $userRole === $role;
}

/**
 * Get current admin user data
 *
 * @return array|null User data array or null if not logged in
 */
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['admin_user_id'] ?? null,
        'username' => $_SESSION['admin_username'] ?? null,
        'email' => $_SESSION['admin_email'] ?? null,
        'role' => $_SESSION['admin_role'] ?? null,
        'full_name' => $_SESSION['admin_full_name'] ?? null,
        'login_time' => $_SESSION['admin_login_time'] ?? null,
        'last_activity' => $_SESSION['admin_last_activity'] ?? null
    ];
}

/**
 * Destroy admin session (logout)
 *
 * @return void
 */
function destroyAdminSession() {
    initSecureSession();

    // Track logout event
    if (ANALYTICS_ENABLED && isAdminLoggedIn()) {
        trackEvent('admin_logout', [
            'user_id' => $_SESSION['admin_user_id'] ?? null,
            'session_duration' => (time() - ($_SESSION['admin_login_time'] ?? time()))
        ]);
    }

    // Clear admin session variables
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_user_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_role']);
    unset($_SESSION['admin_full_name']);
    unset($_SESSION['admin_login_time']);
    unset($_SESSION['admin_last_activity']);
    unset($_SESSION['admin_ip']);
    unset($_SESSION['admin_user_agent']);
    unset($_SESSION['admin_redirect_after_login']);

    // Regenerate session ID
    session_regenerate_id(true);
}

// ============================================================================
// LOGIN ATTEMPT TRACKING
// ============================================================================

/**
 * Record failed login attempt
 *
 * @param string $username Username or email
 * @param int|null $userId User ID if user exists
 * @return void
 */
function recordFailedLogin($username, $userId = null) {
    global $db;

    try {
        $ip = getClientIP();

        // Increment user's login attempts if user exists
        if ($userId) {
            $stmt = $db->prepare("
                UPDATE users
                SET
                    login_attempts = login_attempts + 1,
                    locked_until = CASE
                        WHEN login_attempts + 1 >= ? THEN DATE_ADD(NOW(), INTERVAL ? SECOND)
                        ELSE locked_until
                    END
                WHERE id = ?
            ");
            $stmt->execute([MAX_LOGIN_ATTEMPTS, LOGIN_LOCKOUT_TIME, $userId]);
        }

        // Log attempt to login_attempts table
        $stmt = $db->prepare("
            INSERT INTO login_attempts
            (username, ip_address, user_agent, success, created_at)
            VALUES (?, ?, ?, 0, NOW())
        ");
        $stmt->execute([
            $username,
            $ip,
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);

        // Track analytics
        if (ANALYTICS_ENABLED) {
            trackEvent('failed_login_attempt', [
                'username' => $username,
                'ip' => $ip
            ]);
        }

    } catch (Exception $e) {
        error_log("[AUTH ERROR] Failed to record login attempt: " . $e->getMessage());
    }
}

/**
 * Reset login attempts after successful login
 *
 * @param int $userId User ID
 * @return void
 */
function resetLoginAttempts($userId) {
    global $db;

    try {
        $stmt = $db->prepare("
            UPDATE users
            SET
                login_attempts = 0,
                locked_until = NULL
            WHERE id = ?
        ");
        $stmt->execute([$userId]);

    } catch (Exception $e) {
        error_log("[AUTH ERROR] Failed to reset login attempts: " . $e->getMessage());
    }
}

/**
 * Check if account is locked due to too many failed attempts
 *
 * @param string $username Username or email
 * @return bool True if locked, false otherwise
 */
function isAccountLocked($username) {
    global $db;

    try {
        $stmt = $db->prepare("
            SELECT id, login_attempts, locked_until
            FROM users
            WHERE (username = ? OR email = ?)
            AND locked_until IS NOT NULL
            AND locked_until > NOW()
            LIMIT 1
        ");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user !== false;

    } catch (Exception $e) {
        error_log("[AUTH ERROR] Failed to check account lock: " . $e->getMessage());
        return false;
    }
}

/**
 * Get remaining lockout time in seconds
 *
 * @param string $username Username or email
 * @return int Remaining seconds, 0 if not locked
 */
function getRemainingLockoutTime($username) {
    global $db;

    try {
        $stmt = $db->prepare("
            SELECT TIMESTAMPDIFF(SECOND, NOW(), locked_until) as remaining
            FROM users
            WHERE (username = ? OR email = ?)
            AND locked_until > NOW()
            LIMIT 1
        ");
        $stmt->execute([$username, $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? max(0, (int)$result['remaining']) : 0;

    } catch (Exception $e) {
        error_log("[AUTH ERROR] Failed to get lockout time: " . $e->getMessage());
        return 0;
    }
}

// ============================================================================
// USER MANAGEMENT
// ============================================================================

/**
 * Create new admin user
 *
 * @param array $data User data (username, email, password, full_name, role)
 * @return int|false User ID on success, false on failure
 */
function createAdminUser($data) {
    global $db;

    try {
        // Validate required fields
        $required = ['username', 'email', 'password', 'full_name'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }

        // Check if username or email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$data['username'], $data['email']]);
        if ($stmt->fetch()) {
            throw new Exception("Username or email already exists");
        }

        // Hash password
        $passwordHash = password_hash($data['password'], PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);

        // Insert user
        $stmt = $db->prepare("
            INSERT INTO users
            (username, email, password_hash, full_name, role, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'active', NOW())
        ");
        $stmt->execute([
            $data['username'],
            $data['email'],
            $passwordHash,
            $data['full_name'],
            $data['role'] ?? 'viewer'
        ]);

        $userId = $db->lastInsertId();

        error_log("[AUTH] Created new admin user: {$data['username']} (ID: {$userId})");

        return $userId;

    } catch (Exception $e) {
        error_log("[AUTH ERROR] Failed to create user: " . $e->getMessage());
        return false;
    }
}

// ============================================================================
// CONFIGURATION
// ============================================================================

// Define constants if not already defined
if (!defined('MAX_LOGIN_ATTEMPTS')) {
    define('MAX_LOGIN_ATTEMPTS', 5);
}
if (!defined('LOGIN_LOCKOUT_TIME')) {
    define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes
}
if (!defined('VERIFY_SESSION_IP')) {
    define('VERIFY_SESSION_IP', false); // Set to true to enable IP verification
}

// ============================================================================
// INITIALIZATION
// ============================================================================

if (isDebugMode()) {
    error_log('[AUTH] Admin authentication system loaded');
}
