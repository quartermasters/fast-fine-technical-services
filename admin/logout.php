<?php
/**
 * Fast and Fine Technical Services FZE - Admin Logout Handler
 *
 * Securely destroys admin session and redirects to login page.
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Define application constant
define('FAST_FINE_APP', true);

// Load dependencies
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';

// Initialize session
initSecureSession();

// Verify CSRF token (optional - for GET request logout)
if (isset($_GET['token']) && !verifyCsrfToken($_GET['token'])) {
    die('Invalid logout token');
}

// Destroy admin session
destroyAdminSession();

// Redirect to login page with logout message
$_SESSION['logout_success'] = true;
header('Location: ' . siteUrl('admin/login.php?logged_out=1'));
exit;
