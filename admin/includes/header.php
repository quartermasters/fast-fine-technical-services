<?php
/**
 * Fast and Fine Technical Services FZE - Admin Header
 *
 * Global header for all admin pages
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Prevent direct access
if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

// Get current admin if not already loaded
if (!isset($admin)) {
    $admin = getCurrentAdmin();
}
?>
<header class="admin-header">
    <div class="admin-header-content">
        <div class="admin-logo">
            <a href="<?php echo siteUrl('admin/dashboard.php'); ?>" style="color: inherit; text-decoration: none;">
                Fast <span class="logo-accent">&</span> Fine
                <span class="logo-subtitle">Admin</span>
            </a>
        </div>

        <div class="admin-header-actions">
            <a href="<?php echo siteUrl(); ?>" class="header-btn" target="_blank" title="View Website">
                <i class="fa-solid fa-globe"></i>
                <span class="btn-text">Website</span>
            </a>

            <div class="admin-user-menu">
                <button class="user-menu-trigger" id="userMenuTrigger">
                    <i class="fa-solid fa-user-circle"></i>
                    <span class="user-name"><?php echo htmlspecialchars($admin['full_name'] ?? 'Admin'); ?></span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>

                <div class="user-menu-dropdown" id="userMenuDropdown">
                    <div class="user-menu-header">
                        <div class="user-avatar">
                            <i class="fa-solid fa-user-circle"></i>
                        </div>
                        <div class="user-info">
                            <div class="user-name-full"><?php echo htmlspecialchars($admin['full_name'] ?? ''); ?></div>
                            <div class="user-email"><?php echo htmlspecialchars($admin['email'] ?? ''); ?></div>
                            <div class="user-role-badge"><?php echo htmlspecialchars(ucfirst($admin['role'] ?? 'admin')); ?></div>
                        </div>
                    </div>

                    <div class="user-menu-divider"></div>

                    <a href="profile.php" class="user-menu-item">
                        <i class="fa-solid fa-user"></i>
                        <span>Profile Settings</span>
                    </a>

                    <a href="<?php echo siteUrl('admin/logout.php?token=' . urlencode(generateCsrfToken())); ?>" class="user-menu-item logout">
                        <i class="fa-solid fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
// User menu dropdown toggle
document.getElementById('userMenuTrigger')?.addEventListener('click', function(e) {
    e.stopPropagation();
    const dropdown = document.getElementById('userMenuDropdown');
    dropdown.classList.toggle('show');
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('userMenuDropdown');
    if (dropdown && !e.target.closest('.admin-user-menu')) {
        dropdown.classList.remove('show');
    }
});
</script>
