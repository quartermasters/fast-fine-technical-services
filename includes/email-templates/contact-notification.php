<?php
/**
 * Fast and Fine Technical Services FZE - Contact Form Notification Email
 *
 * This template is used to notify admins when a customer submits the contact form.
 *
 * Required variables:
 * - $contactName: Customer name
 * - $contactEmail: Customer email
 * - $contactPhone: Customer phone number
 * - $serviceInterest: Service they're interested in
 * - $message: Customer message
 *
 * Optional variables:
 * - $urgency: Urgency level
 * - $preferredContact: Preferred contact method
 * - $submissionDate: Date/time of submission
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Prevent direct access
if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

// Set email variables for base template
$emailTitle = 'New Contact Form Submission';
$preheader = 'New inquiry from ' . $contactName;
$submissionDate = $submissionDate ?? date('Y-m-d H:i:s');

// Build email content
ob_start();
?>

<h1 class="email-title" style="color: #002D57; font-size: 24px; font-weight: bold; margin: 0 0 20px 0;">
    New Contact Form Submission
</h1>

<div class="alert-info" style="background-color: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 20px 0; border-radius: 4px;">
    <p style="margin: 0; color: #0c5460; font-weight: bold;">
        A new customer inquiry has been received through the website contact form
    </p>
</div>

<!-- Customer Information -->
<div class="info-box" style="background-color: #f8f9fa; border-left: 4px solid #009FE3; padding: 20px; margin: 20px 0;">
    <h2 class="info-box-title" style="color: #002D57; font-size: 18px; font-weight: bold; margin: 0 0 15px 0;">
        Customer Information
    </h2>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; width: 140px;">
                <strong style="color: #002D57;">Name:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <?php echo htmlspecialchars($contactName); ?>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <strong style="color: #002D57;">Email:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>" style="color: #009FE3; text-decoration: none;">
                    <?php echo htmlspecialchars($contactEmail); ?>
                </a>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <strong style="color: #002D57;">Phone:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <a href="tel:<?php echo htmlspecialchars($contactPhone); ?>" style="color: #009FE3; text-decoration: none;">
                    <?php echo htmlspecialchars($contactPhone); ?>
                </a>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <strong style="color: #002D57;">Service Interest:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <?php echo htmlspecialchars($serviceInterest); ?>
            </td>
        </tr>
        <?php if (!empty($urgency)): ?>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <strong style="color: #002D57;">Urgency:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <span style="<?php
                    echo $urgency === 'emergency' ? 'color: #dc3545; font-weight: bold;' :
                         ($urgency === 'priority' ? 'color: #ffc107; font-weight: bold;' : 'color: #28a745;');
                ?>">
                    <?php echo htmlspecialchars(ucfirst($urgency)); ?>
                </span>
            </td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($preferredContact)): ?>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <strong style="color: #002D57;">Preferred Contact:</strong>
            </td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                <?php echo htmlspecialchars($preferredContact); ?>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <td style="padding: 8px 0;">
                <strong style="color: #002D57;">Submitted:</strong>
            </td>
            <td style="padding: 8px 0;">
                <?php echo htmlspecialchars($submissionDate); ?>
            </td>
        </tr>
    </table>
</div>

<!-- Message Content -->
<div class="info-box" style="background-color: #ffffff; border: 2px solid #009FE3; padding: 20px; margin: 20px 0;">
    <h2 class="info-box-title" style="color: #002D57; font-size: 18px; font-weight: bold; margin: 0 0 15px 0;">
        Customer Message
    </h2>
    <p style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0; white-space: pre-wrap;">
        <?php echo htmlspecialchars($message); ?>
    </p>
</div>

<!-- Quick Actions -->
<div style="margin: 30px 0;">
    <h2 style="color: #002D57; font-size: 20px; font-weight: bold; margin: 0 0 15px 0;">
        Quick Actions
    </h2>

    <table role="presentation" cellspacing="10" cellpadding="0" border="0">
        <tr>
            <td>
                <a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>?subject=Re: Your inquiry about <?php echo urlencode($serviceInterest); ?>" style="display: inline-block; padding: 12px 24px; background-color: #009FE3; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px;">
                    Reply via Email
                </a>
            </td>
            <td>
                <a href="tel:<?php echo htmlspecialchars($contactPhone); ?>" style="display: inline-block; padding: 12px 24px; background-color: #28a745; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px;">
                    Call Customer
                </a>
            </td>
            <td>
                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $contactPhone); ?>?text=<?php echo urlencode('Hello ' . $contactName . ', thank you for contacting Fast and Fine Technical Services. '); ?>" style="display: inline-block; padding: 12px 24px; background-color: #25D366; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px;">
                    WhatsApp
                </a>
            </td>
        </tr>
    </table>
</div>

<?php if (!empty($urgency) && $urgency === 'emergency'): ?>
<!-- Emergency Alert -->
<div class="alert-warning" style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
    <p style="margin: 0; color: #856404; font-weight: bold;">
        ⚠️ This is an EMERGENCY request. Please respond immediately!
    </p>
</div>
<?php endif; ?>

<div class="divider" style="border-top: 1px solid #e0e0e0; margin: 30px 0;"></div>

<!-- Response Guidelines -->
<div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px;">
    <h3 style="color: #002D57; font-size: 16px; font-weight: bold; margin: 0 0 10px 0;">
        Response Guidelines
    </h3>
    <ul style="color: #666666; margin: 0; padding-left: 20px;">
        <li>Respond within 1-2 hours during business hours</li>
        <li>For emergency requests, respond immediately</li>
        <li>Provide a clear quote and timeline</li>
        <li>Confirm availability for the requested service</li>
        <li>Update CRM system with customer details</li>
    </ul>
</div>

<p style="color: #666666; font-size: 14px; margin: 30px 0 0 0; text-align: center;">
    This is an automated notification from the Fast and Fine website contact form
</p>

<?php
$emailContent = ob_get_clean();

// Include base template
require __DIR__ . '/base.php';
?>
