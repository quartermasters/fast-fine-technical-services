<?php
/**
 * Fast and Fine Technical Services FZE - Contact Form Handler
 *
 * Features:
 * - CSRF token validation
 * - Input sanitization and validation
 * - File upload handling with security checks
 * - Email notifications to admin and customer
 * - Database storage
 * - Rate limiting to prevent spam
 * - Comprehensive error handling
 *
 * @package FastAndFine
 * @version 1.0.0
 */

define('FAST_FINE_APP', true);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db-connect.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/translations.php';

// Set JSON response headers
header('Content-Type: application/json');

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception(__('invalid_request_method'));
    }

    // Initialize secure session
    initSecureSession();

    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        throw new Exception(__('invalid_csrf_token'));
    }

    // Rate limiting - 3 submissions per hour per IP
    if (!checkRateLimit('contact_form', 3, 3600)) {
        throw new Exception(__('rate_limit_exceeded'));
    }

    // Get and sanitize input
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeEmail($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $serviceId = (int)($_POST['service_id'] ?? 0);
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    $contactMethod = sanitizeInput($_POST['contact_method'] ?? 'phone');
    $ipAddress = getUserIP();
    $userAgent = sanitizeInput($_SERVER['HTTP_USER_AGENT'] ?? '');

    // Validation
    $errors = [];

    // Validate name
    if (empty($name) || strlen($name) < 2) {
        $errors['name'] = __('name_required');
    } elseif (strlen($name) > 100) {
        $errors['name'] = __('name_too_long');
    } elseif (!preg_match('/^[\p{L}\p{M}\s\'-]+$/u', $name)) {
        $errors['name'] = __('name_invalid_format');
    }

    // Validate email
    if (empty($email)) {
        $errors['email'] = __('email_required');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = __('email_invalid');
    } elseif (strlen($email) > 255) {
        $errors['email'] = __('email_too_long');
    }

    // Validate phone
    if (empty($phone)) {
        $errors['phone'] = __('phone_required');
    } elseif (!preg_match('/^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/', $phone)) {
        $errors['phone'] = __('phone_invalid');
    }

    // Validate service (optional but check if provided)
    if ($serviceId > 0) {
        $serviceExists = dbSelectOne("SELECT id FROM services WHERE id = :id AND is_active = 1", ['id' => $serviceId]);
        if (!$serviceExists) {
            $errors['service'] = __('service_invalid');
        }
    }

    // Validate subject
    if (empty($subject) || strlen($subject) < 3) {
        $errors['subject'] = __('subject_required');
    } elseif (strlen($subject) > 200) {
        $errors['subject'] = __('subject_too_long');
    }

    // Validate message
    if (empty($message) || strlen($message) < 10) {
        $errors['message'] = __('message_required');
    } elseif (strlen($message) > 2000) {
        $errors['message'] = __('message_too_long');
    }

    // Validate contact method
    $validContactMethods = ['phone', 'email', 'whatsapp'];
    if (!in_array($contactMethod, $validContactMethods)) {
        $contactMethod = 'phone'; // Default fallback
    }

    // Check for spam patterns
    $spamPatterns = [
        '/\b(viagra|cialis|casino|poker|lottery|winner)\b/i',
        '/<script/i',
        '/\[url=/i',
        '/\[link=/i'
    ];

    foreach ($spamPatterns as $pattern) {
        if (preg_match($pattern, $message) || preg_match($pattern, $subject)) {
            $errors['spam'] = __('spam_detected');
            logError('Spam detected in contact form', [
                'ip' => $ipAddress,
                'name' => $name,
                'email' => $email,
                'subject' => $subject
            ]);
            break;
        }
    }

    // If validation errors, return them
    if (!empty($errors)) {
        $response['errors'] = $errors;
        $response['message'] = __('validation_errors');
        echo json_encode($response);
        exit;
    }

    // Handle file uploads
    $uploadedFiles = [];
    $uploadErrors = [];

    if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
        $fileCount = count($_FILES['photos']['name']);

        // Check max files limit
        if ($fileCount > MAX_UPLOAD_FILES) {
            $uploadErrors[] = __('too_many_files', ['max' => MAX_UPLOAD_FILES]);
        } else {
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['photos']['name'][$i],
                        'type' => $_FILES['photos']['type'][$i],
                        'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                        'error' => $_FILES['photos']['error'][$i],
                        'size' => $_FILES['photos']['size'][$i]
                    ];

                    $uploadResult = validateAndUploadFile($file, 'contact-photos');

                    if ($uploadResult['success']) {
                        $uploadedFiles[] = $uploadResult['file_path'];
                    } else {
                        $uploadErrors[] = $uploadResult['error'];
                    }
                } elseif ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                    $uploadErrors[] = __('file_upload_error') . ' ' . $_FILES['photos']['name'][$i];
                }
            }
        }
    }

    // If upload errors occurred, include them but don't fail the submission
    if (!empty($uploadErrors)) {
        $response['upload_warnings'] = $uploadErrors;
    }

    // Begin database transaction
    Database::getInstance()->beginTransaction();

    try {
        // Insert into database
        $sql = "INSERT INTO contact_submissions (
            name,
            email,
            phone,
            service_id,
            subject,
            message,
            contact_method,
            attachments,
            ip_address,
            user_agent,
            status,
            created_at
        ) VALUES (
            :name,
            :email,
            :phone,
            :service_id,
            :subject,
            :message,
            :contact_method,
            :attachments,
            :ip_address,
            :user_agent,
            'new',
            NOW()
        )";

        $params = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'service_id' => $serviceId > 0 ? $serviceId : null,
            'subject' => $subject,
            'message' => $message,
            'contact_method' => $contactMethod,
            'attachments' => !empty($uploadedFiles) ? json_encode($uploadedFiles) : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ];

        dbExecute($sql, $params);
        $submissionId = Database::getInstance()->lastInsertId();

        // Commit transaction
        Database::getInstance()->commit();

        // Track analytics event
        trackAnalyticsEvent('contact_form_submission', [
            'submission_id' => $submissionId,
            'service_id' => $serviceId,
            'has_attachments' => !empty($uploadedFiles)
        ]);

        // Send email notifications
        $emailSent = false;
        $autoReplySent = false;

        try {
            // Email to admin
            $adminEmailData = [
                'to' => ADMIN_EMAIL,
                'to_name' => SITE_NAME,
                'subject' => "[Contact Form] " . $subject,
                'template' => 'contact-admin',
                'data' => [
                    'submission_id' => $submissionId,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'service_id' => $serviceId,
                    'subject' => $subject,
                    'message' => $message,
                    'contact_method' => $contactMethod,
                    'attachments' => $uploadedFiles,
                    'ip_address' => $ipAddress,
                    'submitted_at' => date('Y-m-d H:i:s')
                ]
            ];

            $emailSent = sendEmail($adminEmailData);

            // Auto-reply to customer
            $customerEmailData = [
                'to' => $email,
                'to_name' => $name,
                'subject' => __('contact_form_received_subject'),
                'template' => 'contact-customer',
                'data' => [
                    'name' => $name,
                    'submission_id' => $submissionId,
                    'subject' => $subject,
                    'contact_method' => $contactMethod
                ]
            ];

            $autoReplySent = sendEmail($customerEmailData);

        } catch (Exception $emailError) {
            // Log email error but don't fail the submission
            logError('Contact form email error', [
                'submission_id' => $submissionId,
                'error' => $emailError->getMessage()
            ]);
        }

        // Prepare success response
        $response['success'] = true;
        $response['message'] = __('contact_form_success');
        $response['submission_id'] = $submissionId;

        if (!$emailSent) {
            $response['warning'] = __('email_notification_pending');
        }

        // Send WhatsApp notification to admin if enabled
        if (defined('WHATSAPP_NOTIFICATIONS_ENABLED') && WHATSAPP_NOTIFICATIONS_ENABLED) {
            try {
                $whatsappMessage = sprintf(
                    "🔔 *New Contact Form Submission*\n\n" .
                    "📝 ID: #%s\n" .
                    "👤 Name: %s\n" .
                    "📧 Email: %s\n" .
                    "📱 Phone: %s\n" .
                    "📋 Subject: %s\n\n" .
                    "View details in admin panel.",
                    $submissionId,
                    $name,
                    $email,
                    $phone,
                    $subject
                );

                // This would integrate with WhatsApp Business API
                // sendWhatsAppNotification(ADMIN_WHATSAPP, $whatsappMessage);

            } catch (Exception $whatsappError) {
                logError('WhatsApp notification error', [
                    'submission_id' => $submissionId,
                    'error' => $whatsappError->getMessage()
                ]);
            }
        }

    } catch (Exception $dbError) {
        // Rollback transaction
        Database::getInstance()->rollBack();

        // Clean up uploaded files
        foreach ($uploadedFiles as $filePath) {
            $fullPath = __DIR__ . '/../uploads/' . $filePath;
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }

        throw new Exception(__('database_error'));
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();

    // Log error
    logError('Contact form error', [
        'error' => $e->getMessage(),
        'ip' => getUserIP(),
        'data' => [
            'name' => $name ?? '',
            'email' => $email ?? '',
            'subject' => $subject ?? ''
        ]
    ]);
}

// Send response
echo json_encode($response);
exit;
