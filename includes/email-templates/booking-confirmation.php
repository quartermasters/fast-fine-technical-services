<?php
/**
 * Fast and Fine Technical Services FZE - Booking Confirmation Email Template
 *
 * This template is used to send booking confirmation emails to customers.
 *
 * Required variables:
 * - $bookingReference: Unique booking reference (e.g., FFB-20251202-ABC123)
 * - $clientName: Customer name
 * - $serviceName: Name of the booked service
 * - $appointmentDate: Date of appointment
 * - $appointmentTime: Time of appointment
 * - $propertyType: Type of property
 * - $location: Location/address
 * - $estimatedTotal: Total estimated price
 * - $urgency: Urgency level (Regular, Priority, Emergency)
 *
 * Optional variables:
 * - $notes: Special requirements or notes
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Prevent direct access
if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

// Set email variables for base template
$emailTitle = 'Booking Confirmation - ' . $bookingReference;
$preheader = 'Your booking has been confirmed. Reference: ' . $bookingReference;

// Build email content
ob_start();
?>

<h1 class="email-title" style="color: #002D57; font-size: 24px; font-weight: bold; margin: 0 0 20px 0;">
    Booking Confirmed!
</h1>

<div class="alert-success" style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 4px;">
    <p style="margin: 0; color: #155724; font-weight: bold;">
        ✓ Your service booking has been successfully confirmed
    </p>
</div>

<p class="email-text" style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0 0 15px 0;">
    Dear <strong><?php echo htmlspecialchars($clientName); ?></strong>,
</p>

<p class="email-text" style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0 0 15px 0;">
    Thank you for choosing Fast and Fine Technical Services! We're pleased to confirm your booking. Our professional team will be ready to serve you at the scheduled time.
</p>

<!-- Booking Details -->
<div class="info-box" style="background-color: #f8f9fa; border-left: 4px solid #009FE3; padding: 20px; margin: 20px 0;">
    <h2 class="info-box-title" style="color: #002D57; font-size: 18px; font-weight: bold; margin: 0 0 15px 0;">
        Booking Details
    </h2>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <strong style="color: #002D57;">Booking Reference:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; text-align: right;">
                <span style="color: #009FE3; font-weight: bold; font-size: 18px;"><?php echo htmlspecialchars($bookingReference); ?></span>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <strong style="color: #002D57;">Service:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; text-align: right;">
                <?php echo htmlspecialchars($serviceName); ?>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <strong style="color: #002D57;">Appointment Date:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; text-align: right;">
                <?php echo htmlspecialchars($appointmentDate); ?>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <strong style="color: #002D57;">Appointment Time:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; text-align: right;">
                <?php echo htmlspecialchars($appointmentTime); ?>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <strong style="color: #002D57;">Property Type:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; text-align: right;">
                <?php echo htmlspecialchars($propertyType); ?>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <strong style="color: #002D57;">Location:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; text-align: right;">
                <?php echo htmlspecialchars($location); ?>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <strong style="color: #002D57;">Urgency:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; text-align: right;">
                <span style="<?php
                    echo $urgency === 'emergency' ? 'color: #dc3545; font-weight: bold;' :
                         ($urgency === 'priority' ? 'color: #ffc107; font-weight: bold;' : 'color: #28a745;');
                ?>">
                    <?php echo htmlspecialchars(ucfirst($urgency)); ?>
                </span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0 8px 0;">
                <strong style="color: #002D57; font-size: 18px;">Estimated Total:</strong>
            </td>
            <td style="padding: 12px 0 8px 0; text-align: right;">
                <span style="color: #009FE3; font-weight: bold; font-size: 20px;">AED <?php echo number_format($estimatedTotal, 2); ?></span>
            </td>
        </tr>
    </table>
</div>

<?php if (!empty($notes)): ?>
<!-- Special Requirements -->
<div class="info-box" style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0;">
    <h3 style="color: #856404; font-size: 16px; font-weight: bold; margin: 0 0 10px 0;">
        Special Requirements:
    </h3>
    <p style="color: #856404; margin: 0; white-space: pre-wrap;">
        <?php echo htmlspecialchars($notes); ?>
    </p>
</div>
<?php endif; ?>

<!-- What Happens Next -->
<div style="margin: 30px 0;">
    <h2 style="color: #002D57; font-size: 20px; font-weight: bold; margin: 0 0 15px 0;">
        What Happens Next?
    </h2>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="padding: 12px 0; vertical-align: top; width: 50px;">
                <div style="width: 40px; height: 40px; background-color: #009FE3; color: #ffffff; border-radius: 50%; text-align: center; line-height: 40px; font-weight: bold; font-size: 18px;">
                    1
                </div>
            </td>
            <td style="padding: 12px 0; vertical-align: top;">
                <strong style="color: #002D57; display: block; margin-bottom: 5px;">Confirmation Call</strong>
                <span style="color: #666666;">Our team will call you within 24 hours to confirm the details and answer any questions.</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0; vertical-align: top;">
                <div style="width: 40px; height: 40px; background-color: #009FE3; color: #ffffff; border-radius: 50%; text-align: center; line-height: 40px; font-weight: bold; font-size: 18px;">
                    2
                </div>
            </td>
            <td style="padding: 12px 0; vertical-align: top;">
                <strong style="color: #002D57; display: block; margin-bottom: 5px;">Technician Assignment</strong>
                <span style="color: #666666;">We'll assign our best technician based on your service requirements.</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0; vertical-align: top;">
                <div style="width: 40px; height: 40px; background-color: #009FE3; color: #ffffff; border-radius: 50%; text-align: center; line-height: 40px; font-weight: bold; font-size: 18px;">
                    3
                </div>
            </td>
            <td style="padding: 12px 0; vertical-align: top;">
                <strong style="color: #002D57; display: block; margin-bottom: 5px;">Service Delivery</strong>
                <span style="color: #666666;">Our technician will arrive at the scheduled time with all necessary tools and equipment.</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0; vertical-align: top;">
                <div style="width: 40px; height: 40px; background-color: #009FE3; color: #ffffff; border-radius: 50%; text-align: center; line-height: 40px; font-weight: bold; font-size: 18px;">
                    4
                </div>
            </td>
            <td style="padding: 12px 0; vertical-align: top;">
                <strong style="color: #002D57; display: block; margin-bottom: 5px;">Quality Check & Payment</strong>
                <span style="color: #666666;">After completion, we ensure quality and finalize payment with detailed invoice.</span>
            </td>
        </tr>
    </table>
</div>

<div class="divider" style="border-top: 1px solid #e0e0e0; margin: 30px 0;"></div>

<!-- Important Information -->
<div class="alert-info" style="background-color: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 20px 0; border-radius: 4px;">
    <p style="margin: 0 0 10px 0; color: #0c5460; font-weight: bold;">
        Important Information:
    </p>
    <ul style="margin: 0; padding-left: 20px; color: #0c5460;">
        <li>Please ensure someone is available at the location during the scheduled time</li>
        <li>Prepare the work area for easy access</li>
        <li>Final pricing may vary based on actual work completed</li>
        <li>Payment methods: Cash, Card, Bank Transfer</li>
    </ul>
</div>

<!-- Action Button -->
<div style="text-align: center; margin: 30px 0;">
    <a href="<?php echo SITE_URL; ?>?ref=<?php echo urlencode($bookingReference); ?>" class="email-button" style="display: inline-block; padding: 14px 30px; background: linear-gradient(135deg, #009FE3 0%, #007acc 100%); color: #ffffff !important; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
        View Booking Details
    </a>
</div>

<!-- Contact Information -->
<p class="email-text" style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0 0 15px 0;">
    If you have any questions or need to reschedule, please contact us:
</p>

<div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; text-align: center;">
    <p style="margin: 0 0 10px 0;">
        <strong style="color: #002D57;">Call/WhatsApp:</strong>
        <a href="tel:<?php echo WHATSAPP_NUMBER; ?>" style="color: #009FE3; text-decoration: none; font-size: 18px; font-weight: bold;">
            <?php echo PHONE_DISPLAY; ?>
        </a>
    </p>
    <p style="margin: 0;">
        <strong style="color: #002D57;">Email:</strong>
        <a href="mailto:<?php echo SUPPORT_EMAIL; ?>" style="color: #009FE3; text-decoration: none;">
            <?php echo SUPPORT_EMAIL; ?>
        </a>
    </p>
</div>

<p class="email-text" style="color: #333333; font-size: 16px; line-height: 1.6; margin: 30px 0 0 0;">
    Thank you for trusting Fast and Fine Technical Services FZE!
</p>

<p class="email-text" style="color: #666666; font-size: 14px; line-height: 1.6; margin: 10px 0 0 0;">
    Best regards,<br>
    <strong>Fast and Fine Team</strong>
</p>

<?php
$emailContent = ob_get_clean();

// Include base template
require __DIR__ . '/base.php';
?>
