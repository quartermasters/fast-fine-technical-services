<?php
/**
 * Fast and Fine Technical Services FZE - Admin Dashboard
 *
 * Comprehensive admin panel with:
 * - Real-time statistics and analytics
 * - Recent bookings management
 * - Contact form submissions
 * - Revenue tracking
 * - User activity monitoring
 * - Quick actions
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

// Require admin authentication
requireAdminLogin();

// Get current admin user
$admin = getCurrentAdmin();
$db = Database::getInstance()->getConnection();

// ============================================================================
// FETCH DASHBOARD STATISTICS
// ============================================================================

try {
    // Total Bookings
    $stmt = $db->query("SELECT COUNT(*) as total FROM bookings");
    $totalBookings = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Bookings by Status
    $stmt = $db->query("
        SELECT
            status,
            COUNT(*) as count
        FROM bookings
        GROUP BY status
    ");
    $bookingsByStatus = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $bookingsByStatus[$row['status']] = $row['count'];
    }

    // Today's Bookings
    $stmt = $db->query("
        SELECT COUNT(*) as count
        FROM bookings
        WHERE DATE(created_at) = CURDATE()
    ");
    $todaysBookings = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // This Month's Bookings
    $stmt = $db->query("
        SELECT COUNT(*) as count
        FROM bookings
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
    $thisMonthBookings = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Total Revenue (Estimated)
    $stmt = $db->query("
        SELECT SUM(estimated_total) as total
        FROM bookings
        WHERE status IN ('completed', 'confirmed')
    ");
    $totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // This Month's Revenue
    $stmt = $db->query("
        SELECT SUM(estimated_total) as total
        FROM bookings
        WHERE status IN ('completed', 'confirmed')
        AND MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
    $thisMonthRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total Contact Submissions
    $stmt = $db->query("SELECT COUNT(*) as total FROM contact_submissions");
    $totalContacts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Unread Contact Submissions
    $stmt = $db->query("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'unread'");
    $unreadContacts = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Total Testimonials
    $stmt = $db->query("SELECT COUNT(*) as total FROM testimonials");
    $totalTestimonials = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total Page Views (Last 30 Days)
    $stmt = $db->query("
        SELECT COUNT(*) as count
        FROM analytics
        WHERE event_type = 'page_view'
        AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $totalPageViews = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Recent Bookings (Last 10)
    $stmt = $db->query("
        SELECT
            booking_reference,
            client_name,
            service_name,
            appointment_date,
            appointment_time,
            estimated_total,
            status,
            created_at
        FROM bookings
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $recentBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent Contact Submissions (Last 10)
    $stmt = $db->query("
        SELECT
            name,
            email,
            phone,
            service_interest,
            message,
            status,
            created_at
        FROM contact_submissions
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $recentContacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Popular Services (Top 5 by booking count)
    $stmt = $db->query("
        SELECT
            service_name,
            COUNT(*) as booking_count
        FROM bookings
        GROUP BY service_name
        ORDER BY booking_count DESC
        LIMIT 5
    ");
    $popularServices = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("[DASHBOARD ERROR] " . $e->getMessage());
    $totalBookings = 0;
    $totalRevenue = 0;
    $totalContacts = 0;
    $recentBookings = [];
    $recentContacts = [];
}

// Page title
$pageTitle = 'Dashboard - Admin Panel';
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

    <link rel="stylesheet" href="<?php echo assetUrl('css/admin-dashboard.css'); ?>">
</head>
<body>
    <!-- Header -->
    <?php require __DIR__ . '/includes/header.php'; ?>

    <!-- Main Content -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php require __DIR__ . '/includes/sidebar.php'; ?>

        <!-- Content Area -->
        <main class="dashboard-content">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Dashboard Overview</h1>
                    <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars($admin['full_name']); ?>!</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-refresh" onclick="location.reload()">
                        <i class="fa-solid fa-refresh"></i>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <!-- Total Bookings -->
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Bookings</div>
                        <div class="stat-value"><?php echo number_format($totalBookings); ?></div>
                        <div class="stat-info">
                            <i class="fa-solid fa-clock"></i>
                            Today: <?php echo number_format($todaysBookings); ?>
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-value">AED <?php echo number_format($totalRevenue, 2); ?></div>
                        <div class="stat-info">
                            <i class="fa-solid fa-calendar"></i>
                            This Month: AED <?php echo number_format($thisMonthRevenue, 2); ?>
                        </div>
                    </div>
                </div>

                <!-- Contact Submissions -->
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Contact Submissions</div>
                        <div class="stat-value"><?php echo number_format($totalContacts); ?></div>
                        <div class="stat-info">
                            <i class="fa-solid fa-bell"></i>
                            Unread: <?php echo number_format($unreadContacts); ?>
                        </div>
                    </div>
                </div>

                <!-- Page Views -->
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Page Views</div>
                        <div class="stat-value"><?php echo number_format($totalPageViews); ?></div>
                        <div class="stat-info">
                            <i class="fa-solid fa-chart-line"></i>
                            Last 30 Days
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row (Placeholder for future implementation) -->
            <div class="charts-row" style="display: none;">
                <div class="chart-card">
                    <h3>Bookings Trend</h3>
                    <canvas id="bookingsChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Revenue Trend</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="activity-grid">
                <!-- Recent Bookings -->
                <div class="activity-card">
                    <div class="activity-header">
                        <h2 class="activity-title">
                            <i class="fa-solid fa-calendar"></i>
                            Recent Bookings
                        </h2>
                        <a href="bookings.php" class="btn btn-sm">
                            View All
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="activity-content">
                        <?php if (empty($recentBookings)): ?>
                            <div class="empty-state">
                                <i class="fa-solid fa-inbox"></i>
                                <p>No bookings yet</p>
                            </div>
                        <?php else: ?>
                            <div class="bookings-list">
                                <?php foreach ($recentBookings as $booking): ?>
                                    <div class="booking-item">
                                        <div class="booking-info">
                                            <div class="booking-ref"><?php echo htmlspecialchars($booking['booking_reference']); ?></div>
                                            <div class="booking-client"><?php echo htmlspecialchars($booking['client_name']); ?></div>
                                            <div class="booking-service"><?php echo htmlspecialchars($booking['service_name']); ?></div>
                                            <div class="booking-date">
                                                <i class="fa-solid fa-calendar"></i>
                                                <?php echo date('M d, Y', strtotime($booking['appointment_date'])); ?>
                                            </div>
                                        </div>
                                        <div class="booking-meta">
                                            <div class="booking-amount">AED <?php echo number_format($booking['estimated_total'], 2); ?></div>
                                            <span class="status-badge status-<?php echo htmlspecialchars($booking['status']); ?>">
                                                <?php echo htmlspecialchars(ucfirst($booking['status'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Contact Submissions -->
                <div class="activity-card">
                    <div class="activity-header">
                        <h2 class="activity-title">
                            <i class="fa-solid fa-envelope"></i>
                            Recent Contacts
                        </h2>
                        <a href="contacts.php" class="btn btn-sm">
                            View All
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="activity-content">
                        <?php if (empty($recentContacts)): ?>
                            <div class="empty-state">
                                <i class="fa-solid fa-inbox"></i>
                                <p>No contact submissions yet</p>
                            </div>
                        <?php else: ?>
                            <div class="contacts-list">
                                <?php foreach ($recentContacts as $contact): ?>
                                    <div class="contact-item">
                                        <div class="contact-info">
                                            <div class="contact-name"><?php echo htmlspecialchars($contact['name']); ?></div>
                                            <div class="contact-email">
                                                <i class="fa-solid fa-envelope"></i>
                                                <?php echo htmlspecialchars($contact['email']); ?>
                                            </div>
                                            <div class="contact-service"><?php echo htmlspecialchars($contact['service_interest']); ?></div>
                                            <div class="contact-message"><?php echo htmlspecialchars(truncate($contact['message'], 80)); ?></div>
                                        </div>
                                        <div class="contact-meta">
                                            <span class="status-badge status-<?php echo htmlspecialchars($contact['status']); ?>">
                                                <?php echo htmlspecialchars(ucfirst($contact['status'])); ?>
                                            </span>
                                            <div class="contact-time"><?php echo timeAgo($contact['created_at']); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Popular Services (if data available) -->
            <?php if (!empty($popularServices)): ?>
            <div class="services-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fa-solid fa-star"></i>
                        Popular Services
                    </h2>
                </div>
                <div class="services-list">
                    <?php foreach ($popularServices as $service): ?>
                        <div class="service-item">
                            <div class="service-name"><?php echo htmlspecialchars($service['service_name']); ?></div>
                            <div class="service-count">
                                <span><?php echo number_format($service['booking_count']); ?></span> bookings
                            </div>
                            <div class="service-bar">
                                <div class="service-bar-fill" style="width: <?php echo min(100, ($service['booking_count'] / $totalBookings) * 100); ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Auto-refresh dashboard every 5 minutes
        setTimeout(function() {
            location.reload();
        }, 300000); // 5 minutes

        // Show time since last update
        const updateTime = document.createElement('div');
        updateTime.className = 'update-time';
        updateTime.textContent = 'Last updated: Just now';
        document.querySelector('.page-header').appendChild(updateTime);

        let seconds = 0;
        setInterval(function() {
            seconds++;
            const minutes = Math.floor(seconds / 60);
            if (minutes === 0) {
                updateTime.textContent = `Last updated: ${seconds} seconds ago`;
            } else {
                updateTime.textContent = `Last updated: ${minutes} minute${minutes > 1 ? 's' : ''} ago`;
            }
        }, 1000);
    </script>
</body>
</html>
