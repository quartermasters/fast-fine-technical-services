<?php
/**
 * Fast and Fine Technical Services FZE - Booking Handler
 *
 * Processes booking form submissions and creates bookings
 *
 * Features:
 * - Complete form validation
 * - File upload handling
 * - Database insertion
 * - Email notifications
 * - Calendar integration
 * - Booking reference generation
 * - Analytics tracking
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Prevent direct access
define('FAST_FINE_APP', true);

// Load dependencies
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db-connect.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/functions.php';

// Set JSON headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'booking_reference' => null,
    'errors' => []
];

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. POST required.');
    }

    // Initialize session if needed
    initSecureSession();

    // Verify CSRF token
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid security token. Please refresh the page.');
    }

    // Rate limiting check
    $clientIP = getClientIP();
    if (!checkRateLimit($clientIP, 'booking_submit', 300, 5)) { // 5 bookings per 5 minutes
        throw new Exception('Too many booking requests. Please wait a few minutes.');
    }

    // Get database connection
    $db = Database::getInstance()->getConnection();

    // Start transaction
    $db->beginTransaction();

    // ========================================================================
    // VALIDATE INPUT
    // ========================================================================

    $errors = [];

    // Service ID
    $serviceId = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
    if (!$serviceId || $serviceId <= 0) {
        $errors['service_id'] = 'Please select a service.';
    }

    // Urgency
    $urgency = filter_input(INPUT_POST, 'urgency', FILTER_SANITIZE_STRING);
    if (!in_array($urgency, ['regular', 'priority', 'emergency'])) {
        $errors['urgency'] = 'Invalid urgency level.';
    }

    // Date
    $bookingDate = filter_input(INPUT_POST, 'booking_date', FILTER_SANITIZE_STRING);
    if (!$bookingDate) {
        $errors['booking_date'] = 'Booking date is required.';
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $bookingDate);
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        if (!$date || $date < $today) {
            $errors['booking_date'] = 'Invalid booking date. Cannot book in the past.';
        }
    }

    // Time
    $bookingTime = filter_input(INPUT_POST, 'booking_time', FILTER_SANITIZE_STRING);
    if (!$bookingTime) {
        $errors['booking_time'] = 'Booking time is required.';
    }

    // Duration
    $duration = filter_input(INPUT_POST, 'estimated_duration', FILTER_VALIDATE_FLOAT);
    if (!$duration || $duration <= 0 || $duration > 24) {
        $errors['estimated_duration'] = 'Duration must be between 1 and 24 hours.';
    }

    // Emirate
    $emirate = filter_input(INPUT_POST, 'emirate', FILTER_SANITIZE_STRING);
    if (!$emirate) {
        $errors['emirate'] = 'Emirate is required.';
    }

    // Area
    $area = filter_input(INPUT_POST, 'area', FILTER_SANITIZE_STRING);
    if (!$area || strlen($area) < 2) {
        $errors['area'] = 'Area is required.';
    }

    // Property Type
    $propertyType = filter_input(INPUT_POST, 'property_type', FILTER_SANITIZE_STRING);
    if (!$propertyType) {
        $errors['property_type'] = 'Property type is required.';
    }

    // Address
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    if (!$address || strlen($address) < 10) {
        $errors['address'] = 'Full address is required (minimum 10 characters).';
    }

    // Issue Description
    $issueDescription = filter_input(INPUT_POST, 'issue_description', FILTER_SANITIZE_STRING);
    if (!$issueDescription || strlen($issueDescription) < 10) {
        $errors['issue_description'] = 'Issue description is required (minimum 10 characters).';
    }

    // Client Name
    $clientName = filter_input(INPUT_POST, 'client_name', FILTER_SANITIZE_STRING);
    if (!$clientName || strlen($clientName) < 3) {
        $errors['client_name'] = 'Full name is required (minimum 3 characters).';
    }

    // Client Email
    $clientEmail = filter_input(INPUT_POST, 'client_email', FILTER_VALIDATE_EMAIL);
    if (!$clientEmail) {
        $errors['client_email'] = 'Valid email address is required.';
    }

    // Client Phone
    $clientPhone = filter_input(INPUT_POST, 'client_phone', FILTER_SANITIZE_STRING);
    if (!$clientPhone || !preg_match('/^[\+]?[0-9]{10,15}$/', str_replace(['-', ' '], '', $clientPhone))) {
        $errors['client_phone'] = 'Valid phone number is required.';
    }

    // Contact Method
    $contactMethod = filter_input(INPUT_POST, 'contact_method', FILTER_SANITIZE_STRING);
    if (!in_array($contactMethod, ['phone', 'whatsapp', 'email'])) {
        $contactMethod = 'phone'; // Default
    }

    // Alternate Phone (optional)
    $alternatePhone = filter_input(INPUT_POST, 'alternate_phone', FILTER_SANITIZE_STRING);

    // Pricing
    $totalPrice = filter_input(INPUT_POST, 'total_price', FILTER_VALIDATE_FLOAT);
    $vatAmount = filter_input(INPUT_POST, 'vat_amount', FILTER_VALIDATE_FLOAT);
    $subtotal = filter_input(INPUT_POST, 'subtotal', FILTER_VALIDATE_FLOAT);

    // Terms Acceptance
    $termsAccepted = filter_input(INPUT_POST, 'terms_accepted', FILTER_VALIDATE_BOOLEAN);
    if (!$termsAccepted) {
        $errors['terms_accepted'] = 'You must accept the terms and conditions.';
    }

    // Special Requirements (optional)
    $requirements = $_POST['requirements'] ?? [];
    $requirementsJson = json_encode($requirements);

    // If validation errors, return early
    if (!empty($errors)) {
        $response['errors'] = $errors;
        $response['message'] = 'Please correct the errors and try again.';
        echo json_encode($response);
        $db->rollBack();
        exit;
    }

    // ========================================================================
    // GENERATE BOOKING REFERENCE
    // ========================================================================

    $bookingReference = generateBookingReference();

    // ========================================================================
    // HANDLE FILE UPLOADS
    // ========================================================================

    $uploadedFiles = [];
    if (!empty($_FILES['photos']['name'][0])) {
        $uploadDir = __DIR__ . '/../uploads/bookings/' . date('Y/m/');

        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileCount = count($_FILES['photos']['name']);
        $maxFiles = 5;
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        for ($i = 0; $i < min($fileCount, $maxFiles); $i++) {
            if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = $_FILES['photos']['name'][$i];
                $fileTmpPath = $_FILES['photos']['tmp_name'][$i];
                $fileSize = $_FILES['photos']['size'][$i];
                $fileType = $_FILES['photos']['type'][$i];

                // Validate file
                if ($fileSize > $maxFileSize) {
                    continue; // Skip files that are too large
                }

                if (!in_array($fileType, $allowedTypes)) {
                    continue; // Skip invalid file types
                }

                // Generate unique filename
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = $bookingReference . '_' . ($i + 1) . '_' . uniqid() . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;

                // Move file
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $uploadedFiles[] = [
                        'original_name' => $fileName,
                        'stored_name' => $newFileName,
                        'path' => str_replace(__DIR__ . '/../', '', $destPath),
                        'size' => $fileSize,
                        'type' => $fileType
                    ];
                }
            }
        }
    }

    $photosJson = json_encode($uploadedFiles);

    // ========================================================================
    // INSERT BOOKING INTO DATABASE
    // ========================================================================

    $stmt = $db->prepare("
        INSERT INTO bookings (
            booking_reference,
            service_id,
            urgency,
            booking_date,
            booking_time,
            estimated_duration,
            emirate,
            area,
            property_type,
            address,
            issue_description,
            special_requirements,
            photos,
            client_name,
            client_email,
            client_phone,
            alternate_phone,
            preferred_contact_method,
            subtotal,
            vat_amount,
            total_price,
            status,
            client_ip,
            created_at,
            updated_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW(), NOW()
        )
    ");

    $stmt->execute([
        $bookingReference,
        $serviceId,
        $urgency,
        $bookingDate,
        $bookingTime,
        $duration,
        $emirate,
        $area,
        $propertyType,
        $address,
        $issueDescription,
        $requirementsJson,
        $photosJson,
        $clientName,
        $clientEmail,
        $clientPhone,
        $alternatePhone,
        $contactMethod,
        $subtotal,
        $vatAmount,
        $totalPrice,
        $clientIP
    ]);

    $bookingId = $db->lastInsertId();

    // Commit transaction
    $db->commit();

    // ========================================================================
    // SEND EMAIL NOTIFICATIONS
    // ========================================================================

    // Send confirmation email to client
    if (EMAIL_ENABLED) {
        try {
            $emailSubject = "Booking Confirmation - $bookingReference";
            $emailBody = generateBookingConfirmationEmail([
                'booking_reference' => $bookingReference,
                'client_name' => $clientName,
                'service_id' => $serviceId,
                'booking_date' => $bookingDate,
                'booking_time' => $bookingTime,
                'address' => "$area, $emirate",
                'total_price' => $totalPrice
            ]);

            sendEmail($clientEmail, $emailSubject, $emailBody);

            // Send notification to admin
            $adminEmailBody = generateBookingNotificationEmail([
                'booking_reference' => $bookingReference,
                'booking_id' => $bookingId,
                'client_name' => $clientName,
                'client_email' => $clientEmail,
                'client_phone' => $clientPhone,
                'service_id' => $serviceId,
                'urgency' => $urgency,
                'booking_date' => $bookingDate,
                'booking_time' => $bookingTime,
                'emirate' => $emirate,
                'area' => $area,
                'total_price' => $totalPrice
            ]);

            sendEmail(ADMIN_EMAIL, "New Booking - $bookingReference", $adminEmailBody);
        } catch (Exception $e) {
            // Log email error but don't fail the booking
            error_log("Booking email notification failed: " . $e->getMessage());
        }
    }

    // ========================================================================
    // ANALYTICS TRACKING
    // ========================================================================

    if (ANALYTICS_ENABLED) {
        trackEvent('booking_completed', [
            'booking_reference' => $bookingReference,
            'service_id' => $serviceId,
            'urgency' => $urgency,
            'total_price' => $totalPrice,
            'client_ip' => $clientIP
        ]);
    }

    // ========================================================================
    // SUCCESS RESPONSE
    // ========================================================================

    $response['success'] = true;
    $response['message'] = 'Booking created successfully! We will contact you shortly.';
    $response['booking_reference'] = $bookingReference;
    $response['booking_id'] = $bookingId;

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    $response['success'] = false;
    $response['message'] = $e->getMessage();

    // Log error
    error_log("Booking Handler Error: " . $e->getMessage());

    // Set appropriate HTTP status code
    http_response_code(400);
}

// Output JSON response
echo json_encode($response);
exit;

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Generate unique booking reference
 *
 * @return string Booking reference (e.g., FFB-20250101-A1B2C3)
 */
function generateBookingReference(): string
{
    $prefix = 'FFB'; // Fast & Fine Booking
    $date = date('Ymd');
    $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

    return "$prefix-$date-$random";
}

/**
 * Generate booking confirmation email for client
 *
 * @param array $data Booking data
 * @return string HTML email content
 */
function generateBookingConfirmationEmail(array $data): string
{
    $serviceName = getServiceName($data['service_id']);

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #002D57; color: #fff; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .booking-details { background: #fff; border: 1px solid #ddd; padding: 15px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Booking Confirmed!</h1>
        </div>
        <div class="content">
            <p>Dear {$data['client_name']},</p>
            <p>Thank you for choosing Fast and Fine Technical Services. Your booking has been confirmed.</p>

            <div class="booking-details">
                <h3>Booking Details</h3>
                <div class="detail-row">
                    <strong>Booking Reference:</strong>
                    <span>{$data['booking_reference']}</span>
                </div>
                <div class="detail-row">
                    <strong>Service:</strong>
                    <span>$serviceName</span>
                </div>
                <div class="detail-row">
                    <strong>Date:</strong>
                    <span>{$data['booking_date']}</span>
                </div>
                <div class="detail-row">
                    <strong>Time:</strong>
                    <span>{$data['booking_time']}</span>
                </div>
                <div class="detail-row">
                    <strong>Location:</strong>
                    <span>{$data['address']}</span>
                </div>
                <div class="detail-row">
                    <strong>Estimated Total:</strong>
                    <span>AED {$data['total_price']}</span>
                </div>
            </div>

            <p>Our team will contact you within 2 hours to confirm the appointment details.</p>
            <p>If you have any questions, please don't hesitate to contact us.</p>

            <p>Best regards,<br>Fast and Fine Technical Services Team</p>
        </div>
        <div class="footer">
            <p>&copy; 2025 Fast and Fine Technical Services FZE. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
}

/**
 * Generate booking notification email for admin
 *
 * @param array $data Booking data
 * @return string HTML email content
 */
function generateBookingNotificationEmail(array $data): string
{
    $serviceName = getServiceName($data['service_id']);
    $urgencyBadge = match ($data['urgency']) {
        'emergency' => '<span style="background: #EF4444; color: #fff; padding: 3px 8px; border-radius: 3px;">EMERGENCY</span>',
        'priority' => '<span style="background: #F59E0B; color: #fff; padding: 3px 8px; border-radius: 3px;">PRIORITY</span>',
        default => '<span style="background: #10B981; color: #fff; padding: 3px 8px; border-radius: 3px;">REGULAR</span>'
    };

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #009FE3; color: #fff; padding: 20px; }
        .content { background: #f9f9f9; padding: 20px; }
        .booking-details { background: #fff; border: 1px solid #ddd; padding: 15px; margin: 20px 0; }
        .detail-row { padding: 8px 0; border-bottom: 1px solid #eee; }
        .action-button { display: inline-block; background: #002D57; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Booking Received</h2>
            <p>Booking Reference: {$data['booking_reference']}</p>
        </div>
        <div class="content">
            <p>A new booking has been submitted. {$urgencyBadge}</p>

            <div class="booking-details">
                <h3>Customer Information</h3>
                <div class="detail-row"><strong>Name:</strong> {$data['client_name']}</div>
                <div class="detail-row"><strong>Email:</strong> {$data['client_email']}</div>
                <div class="detail-row"><strong>Phone:</strong> {$data['client_phone']}</div>

                <h3 style="margin-top: 20px;">Booking Information</h3>
                <div class="detail-row"><strong>Service:</strong> $serviceName</div>
                <div class="detail-row"><strong>Urgency:</strong> {$data['urgency']}</div>
                <div class="detail-row"><strong>Date:</strong> {$data['booking_date']}</div>
                <div class="detail-row"><strong>Time:</strong> {$data['booking_time']}</div>
                <div class="detail-row"><strong>Location:</strong> {$data['area']}, {$data['emirate']}</div>
                <div class="detail-row"><strong>Total:</strong> AED {$data['total_price']}</div>
            </div>

            <p><strong>Action Required:</strong> Please contact the customer within 2 hours to confirm the appointment.</p>

            <a href="{$_SERVER['HTTP_HOST']}/admin/bookings/view/{$data['booking_id']}" class="action-button">View Full Details</a>
        </div>
    </div>
</body>
</html>
HTML;
}

/**
 * Get service name by ID
 *
 * @param int $serviceId Service ID
 * @return string Service name
 */
function getServiceName(int $serviceId): string
{
    $services = [
        1 => 'Building Cleaning Services',
        2 => 'Carpentry and Flooring',
        3 => 'Plumbing Services',
        4 => 'Air Conditioning',
        5 => 'Electromechanical Services',
        6 => 'Painting Services',
        7 => 'Electrical Services',
        8 => 'Gypsum & Partition',
        9 => 'Tiling Services'
    ];

    return $services[$serviceId] ?? 'Unknown Service';
}
