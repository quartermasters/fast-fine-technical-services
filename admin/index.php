<?php
/**
 * Fast and Fine Technical Services FZE - Admin Index
 *
 * Redirects to dashboard.php
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

// Require admin authentication
requireAdminLogin();

// Redirect to dashboard
header('Location: ' . siteUrl('admin/dashboard.php'));
exit;

// Get current admin user
$admin = getCurrentAdmin();

// Page title
$pageTitle = 'Admin Dashboard - ' . SITE_NAME;
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
        }

        /* Header */
        .admin-header {
            background: linear-gradient(135deg, <?php echo BRAND_NAVY; ?> 0%, #004d8c 100%);
            color: #ffffff;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .admin-header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-logo {
            font-size: 24px;
            font-weight: bold;
        }

        .admin-logo-accent {
            color: <?php echo BRAND_CYAN; ?>;
        }

        .admin-user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .admin-user-name {
            font-weight: 600;
        }

        .admin-user-role {
            font-size: 12px;
            color: #cccccc;
            text-transform: uppercase;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .btn i {
            margin-right: 8px;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Welcome Card */
        .welcome-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
            margin-bottom: 30px;
        }

        .welcome-icon {
            font-size: 64px;
            color: <?php echo BRAND_CYAN; ?>;
            margin-bottom: 20px;
        }

        .welcome-title {
            color: <?php echo BRAND_NAVY; ?>;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .welcome-text {
            color: #666666;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .success-badge {
            display: inline-block;
            padding: 8px 16px;
            background: #d4edda;
            color: #155724;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .info-card {
            background: #f8f9fa;
            border-left: 4px solid <?php echo BRAND_CYAN; ?>;
            padding: 20px;
            border-radius: 8px;
        }

        .info-card-label {
            color: #666666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .info-card-value {
            color: <?php echo BRAND_NAVY; ?>;
            font-size: 18px;
            font-weight: bold;
        }

        /* Coming Soon Badge */
        .coming-soon {
            display: inline-block;
            padding: 4px 12px;
            background: <?php echo BRAND_YELLOW; ?>;
            color: <?php echo BRAND_NAVY; ?>;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        /* Footer */
        .admin-footer {
            text-align: center;
            padding: 20px;
            color: #999999;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <div class="admin-header-content">
            <div class="admin-logo">
                Fast <span class="admin-logo-accent">&</span> Fine <span style="font-size: 14px; font-weight: normal; margin-left: 10px;">Admin Panel</span>
            </div>
            <div class="admin-user-info">
                <div>
                    <div class="admin-user-name">
                        <i class="fa-solid fa-user-circle"></i>
                        <?php echo htmlspecialchars($admin['full_name']); ?>
                    </div>
                    <div class="admin-user-role">
                        <?php echo htmlspecialchars($admin['role']); ?>
                    </div>
                </div>
                <a href="<?php echo siteUrl('admin/logout.php?token=' . urlencode(generateCsrfToken())); ?>" class="btn">
                    <i class="fa-solid fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="welcome-card">
            <div class="welcome-icon">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <div class="success-badge">
                <i class="fa-solid fa-shield-check"></i> Authentication Successful
            </div>
            <h1 class="welcome-title">Welcome, <?php echo htmlspecialchars($admin['full_name']); ?>!</h1>
            <p class="welcome-text">
                You have successfully logged in to the admin panel. The full dashboard is currently under development.
            </p>
            <p class="welcome-text">
                <strong>Next Task:</strong> Implementing comprehensive analytics dashboard with stats, charts, and management tools.
                <span class="coming-soon">Coming Soon</span>
            </p>
        </div>

        <!-- Session Info -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-label">
                    <i class="fa-solid fa-user"></i> Username
                </div>
                <div class="info-card-value">
                    <?php echo htmlspecialchars($admin['username']); ?>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-label">
                    <i class="fa-solid fa-envelope"></i> Email
                </div>
                <div class="info-card-value">
                    <?php echo htmlspecialchars($admin['email']); ?>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-label">
                    <i class="fa-solid fa-shield-halved"></i> Role
                </div>
                <div class="info-card-value">
                    <?php echo htmlspecialchars(ucfirst($admin['role'])); ?>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-label">
                    <i class="fa-solid fa-clock"></i> Login Time
                </div>
                <div class="info-card-value">
                    <?php echo date('g:i A', $admin['login_time']); ?>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div style="margin-top: 40px; text-align: center;">
            <a href="<?php echo siteUrl(); ?>" class="btn" style="display: inline-block; padding: 12px 24px; background: <?php echo BRAND_CYAN; ?>; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold;">
                <i class="fa-solid fa-globe"></i>
                View Website
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="admin-footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
        <p style="margin-top: 10px; font-size: 12px;">
            Generated with <a href="https://claude.com/claude-code" target="_blank" style="color: <?php echo BRAND_CYAN; ?>; text-decoration: none;">Claude Code</a>
        </p>
    </footer>
</body>
</html>
