<?php
/**
 * Fast and Fine Technical Services FZE - Base Email Template
 *
 * This is the base HTML email template used for all transactional emails.
 * It provides a professional, responsive design with company branding.
 *
 * Variables available:
 * - $emailTitle: Main title for the email
 * - $emailContent: Main content HTML
 * - $emailFooter: Optional footer content (default: company info)
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Prevent direct access
if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

// Default values
$emailTitle = $emailTitle ?? 'Notification';
$emailContent = $emailContent ?? '';
$emailFooter = $emailFooter ?? null;
$preheader = $preheader ?? 'Fast and Fine Technical Services FZE';
?>
<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <title><?php echo htmlspecialchars($emailTitle); ?></title>
    <!--[if mso]>
    <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    <style>
        td,th,div,p,a,h1,h2,h3,h4,h5,h6 {font-family: "Segoe UI", sans-serif; mso-line-height-rule: exactly;}
    </style>
    <![endif]-->
    <style>
        /* Reset styles */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }

        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
        }

        /* Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #002D57 0%, #004d8c 100%);
            padding: 30px 20px;
            text-align: center;
        }

        .email-logo {
            color: #ffffff;
            font-size: 28px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }

        .email-logo-accent {
            color: #009FE3;
        }

        /* Content */
        .email-content {
            background-color: #ffffff;
            padding: 40px 30px;
        }

        .email-title {
            color: #002D57;
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 20px 0;
            line-height: 1.3;
        }

        .email-text {
            color: #333333;
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 15px 0;
        }

        /* Info box */
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #009FE3;
            padding: 20px;
            margin: 20px 0;
        }

        .info-box-title {
            color: #002D57;
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }

        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: bold;
            color: #002D57;
            width: 140px;
            flex-shrink: 0;
        }

        .info-value {
            color: #333333;
            flex: 1;
        }

        /* Button */
        .email-button {
            display: inline-block;
            padding: 14px 30px;
            background: linear-gradient(135deg, #009FE3 0%, #007acc 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(0, 159, 227, 0.3);
        }

        .email-button:hover {
            background: linear-gradient(135deg, #007acc 0%, #006bb3 100%);
        }

        /* Alert boxes */
        .alert-success {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .alert-info {
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        /* Footer */
        .email-footer {
            background-color: #002D57;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
            font-size: 14px;
        }

        .footer-text {
            color: #cccccc;
            margin: 10px 0;
        }

        .footer-links {
            margin: 15px 0;
        }

        .footer-link {
            color: #009FE3;
            text-decoration: none;
            margin: 0 10px;
        }

        .social-links {
            margin: 20px 0;
        }

        .social-link {
            display: inline-block;
            width: 36px;
            height: 36px;
            background-color: #009FE3;
            color: #ffffff;
            text-align: center;
            line-height: 36px;
            border-radius: 50%;
            margin: 0 5px;
            text-decoration: none;
            font-size: 18px;
        }

        /* Divider */
        .divider {
            border-top: 1px solid #e0e0e0;
            margin: 30px 0;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }

            .email-content {
                padding: 20px !important;
            }

            .email-title {
                font-size: 20px !important;
            }

            .info-label {
                width: 120px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <!-- Preheader text (hidden) -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        <?php echo htmlspecialchars($preheader); ?>
    </div>

    <!-- Email container -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f4;">
        <tr>
            <td style="padding: 20px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" class="email-container" style="max-width: 600px; width: 100%;">

                    <!-- Header -->
                    <tr>
                        <td class="email-header" style="background: linear-gradient(135deg, #002D57 0%, #004d8c 100%); padding: 30px 20px; text-align: center;">
                            <a href="<?php echo SITE_URL; ?>" class="email-logo" style="color: #ffffff; font-size: 28px; font-weight: bold; text-decoration: none;">
                                Fast <span class="email-logo-accent" style="color: #009FE3;">&</span> Fine
                            </a>
                            <div style="color: #cccccc; font-size: 14px; margin-top: 10px;">
                                Technical Services FZE
                            </div>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td class="email-content" style="background-color: #ffffff; padding: 40px 30px;">
                            <?php echo $emailContent; ?>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="email-footer" style="background-color: #002D57; color: #ffffff; padding: 30px 20px; text-align: center; font-size: 14px;">
                            <?php if ($emailFooter): ?>
                                <?php echo $emailFooter; ?>
                            <?php else: ?>
                                <p class="footer-text" style="color: #cccccc; margin: 10px 0;">
                                    <strong>Fast and Fine Technical Services FZE</strong><br>
                                    <?php echo BUSINESS_ADDRESS; ?>
                                </p>

                                <p class="footer-text" style="color: #cccccc; margin: 10px 0;">
                                    Phone: <a href="tel:<?php echo WHATSAPP_NUMBER; ?>" style="color: #009FE3; text-decoration: none;"><?php echo PHONE_DISPLAY; ?></a><br>
                                    Email: <a href="mailto:<?php echo ADMIN_EMAIL; ?>" style="color: #009FE3; text-decoration: none;"><?php echo ADMIN_EMAIL; ?></a>
                                </p>

                                <div class="footer-links" style="margin: 15px 0;">
                                    <a href="<?php echo SITE_URL; ?>" class="footer-link" style="color: #009FE3; text-decoration: none; margin: 0 10px;">Website</a>
                                    <a href="<?php echo SITE_URL; ?>#services" class="footer-link" style="color: #009FE3; text-decoration: none; margin: 0 10px;">Services</a>
                                    <a href="<?php echo SITE_URL; ?>#contact" class="footer-link" style="color: #009FE3; text-decoration: none; margin: 0 10px;">Contact</a>
                                </div>

                                <p class="footer-text" style="color: #999999; font-size: 12px; margin: 20px 0 0 0;">
                                    &copy; <?php echo date('Y'); ?> Fast and Fine Technical Services FZE. All rights reserved.
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
