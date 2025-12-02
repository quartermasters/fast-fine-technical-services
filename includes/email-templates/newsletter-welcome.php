<?php
/**
 * Fast and Fine Technical Services FZE - Newsletter Welcome Email
 *
 * This template is sent to new newsletter subscribers.
 *
 * Required variables:
 * - $subscriberEmail: Subscriber's email address
 *
 * Optional variables:
 * - $subscriberName: Subscriber's name (if provided)
 * - $promoCode: Special promotional code for new subscribers
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Prevent direct access
if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

// Set email variables for base template
$emailTitle = 'Welcome to Fast & Fine Newsletter';
$preheader = 'Thank you for subscribing! Get ready for exclusive offers and updates.';

// Build email content
ob_start();
?>

<h1 class="email-title" style="color: #002D57; font-size: 24px; font-weight: bold; margin: 0 0 20px 0;">
    Welcome to Fast & Fine Family!
</h1>

<div class="alert-success" style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 4px;">
    <p style="margin: 0; color: #155724; font-weight: bold;">
        ✓ Your subscription is confirmed!
    </p>
</div>

<p class="email-text" style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0 0 15px 0;">
    <?php if (!empty($subscriberName)): ?>
        Dear <strong><?php echo htmlspecialchars($subscriberName); ?></strong>,
    <?php else: ?>
        Hello!
    <?php endif; ?>
</p>

<p class="email-text" style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0 0 15px 0;">
    Thank you for subscribing to our newsletter! We're excited to have you as part of the Fast and Fine community. You'll now receive:
</p>

<!-- Benefits -->
<div style="margin: 30px 0;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="padding: 15px; background-color: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="vertical-align: top; width: 50px;">
                            <div style="width: 40px; height: 40px; background-color: #009FE3; color: #ffffff; border-radius: 50%; text-align: center; line-height: 40px; font-size: 20px;">
                                ✉️
                            </div>
                        </td>
                        <td style="padding-left: 15px; vertical-align: middle;">
                            <strong style="color: #002D57; display: block; margin-bottom: 5px;">Exclusive Offers</strong>
                            <span style="color: #666666; font-size: 14px;">Be the first to know about special discounts and promotions</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 10px;">
        <tr>
            <td style="padding: 15px; background-color: #f8f9fa; border-radius: 8px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="vertical-align: top; width: 50px;">
                            <div style="width: 40px; height: 40px; background-color: #009FE3; color: #ffffff; border-radius: 50%; text-align: center; line-height: 40px; font-size: 20px;">
                                💡
                            </div>
                        </td>
                        <td style="padding-left: 15px; vertical-align: middle;">
                            <strong style="color: #002D57; display: block; margin-bottom: 5px;">Expert Tips</strong>
                            <span style="color: #666666; font-size: 14px;">Maintenance tips and advice from our professional technicians</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 10px;">
        <tr>
            <td style="padding: 15px; background-color: #f8f9fa; border-radius: 8px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="vertical-align: top; width: 50px;">
                            <div style="width: 40px; height: 40px; background-color: #009FE3; color: #ffffff; border-radius: 50%; text-align: center; line-height: 40px; font-size: 20px;">
                                🎉
                            </div>
                        </td>
                        <td style="padding-left: 15px; vertical-align: middle;">
                            <strong style="color: #002D57; display: block; margin-bottom: 5px;">Latest Updates</strong>
                            <span style="color: #666666; font-size: 14px;">News about our services, projects, and company updates</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 10px;">
        <tr>
            <td style="padding: 15px; background-color: #f8f9fa; border-radius: 8px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="vertical-align: top; width: 50px;">
                            <div style="width: 40px; height: 40px; background-color: #009FE3; color: #ffffff; border-radius: 50%; text-align: center; line-height: 40px; font-size: 20px;">
                                🎁
                            </div>
                        </td>
                        <td style="padding-left: 15px; vertical-align: middle;">
                            <strong style="color: #002D57; display: block; margin-bottom: 5px;">Priority Booking</strong>
                            <span style="color: #666666; font-size: 14px;">Get priority access to our most popular services</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<?php if (!empty($promoCode)): ?>
<!-- Special Offer -->
<div style="background: linear-gradient(135deg, #FDB913 0%, #ffa500 100%); padding: 30px; border-radius: 10px; text-align: center; margin: 30px 0;">
    <h2 style="color: #ffffff; font-size: 22px; font-weight: bold; margin: 0 0 10px 0;">
        Welcome Gift!
    </h2>
    <p style="color: #ffffff; font-size: 16px; margin: 0 0 15px 0;">
        Use this exclusive promo code for 15% OFF your first booking
    </p>
    <div style="background-color: #ffffff; display: inline-block; padding: 15px 40px; border-radius: 50px; margin: 10px 0;">
        <span style="color: #002D57; font-size: 24px; font-weight: bold; letter-spacing: 2px;">
            <?php echo htmlspecialchars($promoCode); ?>
        </span>
    </div>
    <p style="color: #ffffff; font-size: 14px; margin: 15px 0 0 0;">
        Valid for 30 days from subscription date
    </p>
</div>
<?php endif; ?>

<div class="divider" style="border-top: 1px solid #e0e0e0; margin: 30px 0;"></div>

<!-- Our Services -->
<h2 style="color: #002D57; font-size: 20px; font-weight: bold; margin: 0 0 20px 0; text-align: center;">
    Our Popular Services
</h2>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td style="width: 33.33%; padding: 10px; vertical-align: top; text-align: center;">
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; height: 100%;">
                <div style="font-size: 36px; margin-bottom: 10px;">🛠️</div>
                <strong style="color: #002D57; display: block; margin-bottom: 5px;">Carpentry</strong>
                <span style="color: #666666; font-size: 13px;">Professional woodwork & repairs</span>
            </div>
        </td>
        <td style="width: 33.33%; padding: 10px; vertical-align: top; text-align: center;">
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; height: 100%;">
                <div style="font-size: 36px; margin-bottom: 10px;">❄️</div>
                <strong style="color: #002D57; display: block; margin-bottom: 5px;">AC Services</strong>
                <span style="color: #666666; font-size: 13px;">Installation & maintenance</span>
            </div>
        </td>
        <td style="width: 33.33%; padding: 10px; vertical-align: top; text-align: center;">
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; height: 100%;">
                <div style="font-size: 36px; margin-bottom: 10px;">💧</div>
                <strong style="color: #002D57; display: block; margin-bottom: 5px;">Plumbing</strong>
                <span style="color: #666666; font-size: 13px;">Expert leak fixes & installations</span>
            </div>
        </td>
    </tr>
</table>

<!-- CTA Button -->
<div style="text-align: center; margin: 30px 0;">
    <a href="<?php echo SITE_URL; ?>#services" class="email-button" style="display: inline-block; padding: 14px 30px; background: linear-gradient(135deg, #009FE3 0%, #007acc 100%); color: #ffffff !important; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
        View All Services
    </a>
</div>

<div class="divider" style="border-top: 1px solid #e0e0e0; margin: 30px 0;"></div>

<!-- Stay Connected -->
<h3 style="color: #002D57; font-size: 18px; font-weight: bold; margin: 0 0 15px 0; text-align: center;">
    Stay Connected
</h3>

<p style="color: #666666; text-align: center; margin: 0 0 20px 0;">
    Follow us on social media for daily updates, tips, and behind-the-scenes content
</p>

<div style="text-align: center; margin: 20px 0;">
    <?php if (FACEBOOK_URL): ?>
    <a href="<?php echo FACEBOOK_URL; ?>" style="display: inline-block; width: 40px; height: 40px; background-color: #1877f2; color: #ffffff; text-align: center; line-height: 40px; border-radius: 50%; margin: 0 5px; text-decoration: none; font-size: 20px;">
        f
    </a>
    <?php endif; ?>

    <?php if (INSTAGRAM_URL): ?>
    <a href="<?php echo INSTAGRAM_URL; ?>" style="display: inline-block; width: 40px; height: 40px; background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); color: #ffffff; text-align: center; line-height: 40px; border-radius: 50%; margin: 0 5px; text-decoration: none; font-size: 20px;">
        📷
    </a>
    <?php endif; ?>

    <?php if (LINKEDIN_URL): ?>
    <a href="<?php echo LINKEDIN_URL; ?>" style="display: inline-block; width: 40px; height: 40px; background-color: #0077b5; color: #ffffff; text-align: center; line-height: 40px; border-radius: 50%; margin: 0 5px; text-decoration: none; font-size: 20px;">
        in
    </a>
    <?php endif; ?>

    <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', WHATSAPP_NUMBER); ?>" style="display: inline-block; width: 40px; height: 40px; background-color: #25D366; color: #ffffff; text-align: center; line-height: 40px; border-radius: 50%; margin: 0 5px; text-decoration: none; font-size: 20px;">
        💬
    </a>
</div>

<div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; text-align: center; margin: 30px 0;">
    <p style="margin: 0 0 10px 0; color: #666666;">
        Need immediate assistance?
    </p>
    <p style="margin: 0;">
        <a href="tel:<?php echo WHATSAPP_NUMBER; ?>" style="color: #009FE3; text-decoration: none; font-size: 20px; font-weight: bold;">
            <?php echo PHONE_DISPLAY; ?>
        </a>
    </p>
</div>

<p class="email-text" style="color: #333333; font-size: 16px; line-height: 1.6; margin: 30px 0 0 0; text-align: center;">
    Thank you for choosing Fast and Fine!
</p>

<p style="color: #999999; font-size: 12px; text-align: center; margin: 20px 0 0 0;">
    You're receiving this email because you subscribed to our newsletter at <?php echo SITE_URL; ?><br>
    <a href="<?php echo SITE_URL; ?>/unsubscribe?email=<?php echo urlencode($subscriberEmail); ?>" style="color: #999999; text-decoration: underline;">
        Unsubscribe
    </a> | <a href="<?php echo SITE_URL; ?>#contact" style="color: #999999; text-decoration: underline;">
        Contact Us
    </a>
</p>

<?php
$emailContent = ob_get_clean();

// Include base template
require __DIR__ . '/base.php';
?>
