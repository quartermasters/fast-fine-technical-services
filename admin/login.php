<?php
/**
 * Fast and Fine Technical Services FZE - Admin Login Page
 *
 * Secure admin authentication with:
 * - CSRF protection
 * - Bcrypt password hashing
 * - Rate limiting
 * - Account lockout protection
 * - Session hijacking prevention
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Define application constant
define('FAST_FINE_APP', true);

// Load dependencies
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db-connect.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';

// Initialize session
initSecureSession();

// Redirect if already logged in
if (isAdminLoggedIn()) {
    $redirectUrl = $_SESSION['admin_redirect_after_login'] ?? siteUrl('admin/index.php');
    unset($_SESSION['admin_redirect_after_login']);
    header('Location: ' . $redirectUrl);
    exit;
}

// Initialize variables
$errors = [];
$successMessage = '';
$username = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please try again.';
    }

    // Rate limiting check
    $clientIP = getClientIP();
    if (!checkRateLimit($clientIP, 'admin_login', 60, 10)) { // 10 attempts per minute
        $errors[] = 'Too many login attempts. Please wait a moment.';
    }

    if (empty($errors)) {
        // Get form data
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate inputs
        if (empty($username)) {
            $errors[] = 'Username or email is required.';
        }

        if (empty($password)) {
            $errors[] = 'Password is required.';
        }

        if (empty($errors)) {
            // Check if account is locked
            if (isAccountLocked($username)) {
                $remainingTime = getRemainingLockoutTime($username);
                $minutes = ceil($remainingTime / 60);
                $errors[] = "Account is temporarily locked due to too many failed login attempts. Please try again in {$minutes} minute(s).";
            } else {
                // Attempt authentication
                $user = authenticateAdmin($username, $password);

                if ($user) {
                    // Create admin session
                    if (createAdminSession($user)) {
                        // Get redirect URL
                        $redirectUrl = $_SESSION['admin_redirect_after_login'] ?? siteUrl('admin/index.php');
                        unset($_SESSION['admin_redirect_after_login']);

                        // Redirect to admin panel
                        header('Location: ' . $redirectUrl);
                        exit;
                    } else {
                        $errors[] = 'Failed to create session. Please try again.';
                    }
                } else {
                    $errors[] = 'Invalid username or password.';
                }
            }
        }
    }
}

// Page title
$pageTitle = 'Admin Login - ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo assetUrl('images/favicon.png'); ?>">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/<?php echo ICON_VERSION_FONTAWESOME; ?>/css/all.min.css">

    <style>
        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, <?php echo BRAND_NAVY; ?> 0%, #004d8c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Login Container */
        .login-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
        }

        /* Header */
        .login-header {
            background: linear-gradient(135deg, <?php echo BRAND_NAVY; ?> 0%, #004d8c 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }

        .login-logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .login-logo-accent {
            color: <?php echo BRAND_CYAN; ?>;
        }

        .login-subtitle {
            color: #cccccc;
            font-size: 16px;
        }

        /* Content */
        .login-content {
            padding: 40px 30px;
        }

        .login-title {
            color: <?php echo BRAND_NAVY; ?>;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }

        .login-description {
            color: #666666;
            font-size: 14px;
            text-align: center;
            margin-bottom: 30px;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            color: <?php echo BRAND_NAVY; ?>;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: <?php echo BRAND_CYAN; ?>;
            box-shadow: 0 0 0 3px rgba(0, 159, 227, 0.1);
        }

        .form-input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999999;
            font-size: 18px;
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .form-checkbox input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .form-checkbox label {
            color: #666666;
            font-size: 14px;
            cursor: pointer;
            user-select: none;
        }

        /* Button */
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, <?php echo BRAND_CYAN; ?> 0%, #007acc 100%);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: linear-gradient(135deg, #007acc 0%, #006bb3 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 159, 227, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn i {
            margin-right: 8px;
        }

        /* Alerts */
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            color: #991b1b;
        }

        .alert-success {
            background-color: #f0fdf4;
            border-left: 4px solid #16a34a;
            color: #166534;
        }

        .alert ul {
            list-style: none;
            padding-left: 0;
        }

        .alert li {
            padding: 4px 0;
        }

        .alert li:before {
            content: '• ';
            font-weight: bold;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            padding: 20px 30px;
            background-color: #f8f9fa;
            border-top: 1px solid #e0e0e0;
        }

        .login-footer-link {
            color: <?php echo BRAND_CYAN; ?>;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .login-footer-link:hover {
            text-decoration: underline;
        }

        .login-footer-text {
            color: #999999;
            font-size: 12px;
            margin-top: 15px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                border-radius: 0;
            }

            .login-header {
                padding: 30px 20px;
            }

            .login-content {
                padding: 30px 20px;
            }

            .login-logo {
                font-size: 28px;
            }
        }

        /* Loading Spinner */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 1s ease-in-out infinite;
            margin-left: 10px;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn.loading .spinner {
            display: inline-block;
        }

        .btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <div class="login-logo">
                Fast <span class="login-logo-accent">&</span> Fine
            </div>
            <div class="login-subtitle">Technical Services FZE</div>
        </div>

        <!-- Content -->
        <div class="login-content">
            <h1 class="login-title">Admin Login</h1>
            <p class="login-description">Sign in to access the admin panel</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm">
                <?php echo csrfField(); ?>

                <!-- Username -->
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fa-solid fa-user"></i> Username or Email
                    </label>
                    <div class="form-input-wrapper">
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-input"
                            placeholder="Enter your username or email"
                            value="<?php echo htmlspecialchars($username); ?>"
                            required
                            autocomplete="username"
                            autofocus
                        >
                        <i class="fa-solid fa-user form-input-icon"></i>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fa-solid fa-lock"></i> Password
                    </label>
                    <div class="form-input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >
                        <i class="fa-solid fa-lock form-input-icon" id="togglePassword" style="cursor: pointer;"></i>
                    </div>
                </div>

                <!-- Remember Me (optional - not implemented in this version) -->
                <!-- <div class="form-checkbox">
                    <input type="checkbox" id="remember" name="remember" value="1">
                    <label for="remember">Remember me for 30 days</label>
                </div> -->

                <!-- Submit Button -->
                <button type="submit" class="btn" id="loginBtn">
                    <i class="fa-solid fa-sign-in-alt"></i>
                    <span>Sign In</span>
                    <span class="spinner"></span>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <a href="<?php echo siteUrl(); ?>" class="login-footer-link">
                <i class="fa-solid fa-arrow-left"></i> Back to Website
            </a>
            <p class="login-footer-text">
                &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        // Password toggle functionality
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-lock');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-lock');
            }
        });

        // Loading state on form submit
        document.getElementById('loginForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
        });

        // Auto-remove error messages after 10 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 10000);
    </script>
</body>
</html>
