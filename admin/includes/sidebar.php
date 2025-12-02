<?php
/**
 * Fast and Fine Technical Services FZE - Admin Sidebar Navigation
 *
 * Main navigation menu for admin panel
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Prevent direct access
if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

// Get current page
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <nav class="sidebar-nav">
        <!-- Dashboard -->
        <a href="dashboard.php" class="nav-item <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-line"></i>
            <span>Dashboard</span>
        </a>

        <!-- Bookings -->
        <a href="bookings.php" class="nav-item <?php echo $currentPage === 'bookings.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-calendar-check"></i>
            <span>Bookings</span>
            <?php
            // Get pending bookings count
            try {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
                $pendingCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                if ($pendingCount > 0) {
                    echo '<span class="badge">' . $pendingCount . '</span>';
                }
            } catch (Exception $e) {}
            ?>
        </a>

        <!-- Contact Submissions -->
        <a href="contacts.php" class="nav-item <?php echo $currentPage === 'contacts.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-envelope"></i>
            <span>Contacts</span>
            <?php
            // Get unread contacts count
            try {
                $stmt = $db->query("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'unread'");
                $unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                if ($unreadCount > 0) {
                    echo '<span class="badge badge-warning">' . $unreadCount . '</span>';
                }
            } catch (Exception $e) {}
            ?>
        </a>

        <!-- Services -->
        <a href="services.php" class="nav-item <?php echo $currentPage === 'services.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-wrench"></i>
            <span>Services</span>
        </a>

        <!-- Testimonials -->
        <a href="testimonials.php" class="nav-item <?php echo $currentPage === 'testimonials.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-star"></i>
            <span>Testimonials</span>
        </a>

        <!-- Portfolio/Projects -->
        <a href="projects.php" class="nav-item <?php echo $currentPage === 'projects.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-images"></i>
            <span>Portfolio</span>
        </a>

        <div class="nav-divider"></div>

        <!-- Analytics -->
        <a href="analytics.php" class="nav-item <?php echo $currentPage === 'analytics.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-bar"></i>
            <span>Analytics</span>
        </a>

        <!-- Users Management (Admin only) -->
        <?php if (hasAdminRole('admin')): ?>
        <a href="users.php" class="nav-item <?php echo $currentPage === 'users.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i>
            <span>Users</span>
        </a>
        <?php endif; ?>

        <!-- Settings (Admin only) -->
        <?php if (hasAdminRole('admin')): ?>
        <a href="settings.php" class="nav-item <?php echo $currentPage === 'settings.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-cog"></i>
            <span>Settings</span>
        </a>
        <?php endif; ?>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="sidebar-footer-content">
            <i class="fa-solid fa-info-circle"></i>
            <div class="sidebar-footer-text">
                <strong>Fast & Fine v1.0</strong>
                <small>Admin Panel</small>
            </div>
        </div>
    </div>
</aside>
