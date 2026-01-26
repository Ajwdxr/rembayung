<?php

/**
 * Email Notification Helper using PHPMailer with Gmail SMTP
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'ajwadxara99@gmail.com');
define('SMTP_PASSWORD', 'wncb lyoi jqcj xjyy');
define('SMTP_FROM_EMAIL', 'ajwadxara99@gmail.com');
define('SMTP_FROM_NAME', 'Rembayung Restaurant');
define('EMAIL_NOTIFICATIONS_ENABLED', true);

/**
 * Get the email template wrapper
 */
function getEmailTemplate($content, $title = 'Rembayung')
{
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($title) . '</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif; background-color: #f5f5f5;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5; padding: 40px 20px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                        <!-- Header -->
                        <tr>
                            <td style="background: linear-gradient(135deg, #2C2C2C 0%, #1a1a1a 100%); padding: 30px 40px; text-align: center;">
                                <h1 style="margin: 0; color: #D4AF37; font-size: 32px; font-weight: 600; letter-spacing: 1px;">Rembayung</h1>
                                <p style="margin: 8px 0 0; color: #888888; font-size: 14px;">Authentic Kampung Cuisine</p>
                            </td>
                        </tr>
                        
                        <!-- Content -->
                        <tr>
                            <td style="padding: 40px;">
                                ' . $content . '
                            </td>
                        </tr>
                        
                        <!-- Footer -->
                        <tr>
                            <td style="background-color: #f8f8f8; padding: 30px 40px; text-align: center; border-top: 1px solid #eee;">
                                <p style="margin: 0 0 10px; color: #666; font-size: 14px;">
                                    <strong>Rembayung Restaurant</strong>
                                </p>
                                <p style="margin: 0 0 5px; color: #888; font-size: 13px;">
                                    Lot 2791, Jalan Daud, Kampung Baru, 50300 Kuala Lumpur
                                </p>
                                <p style="margin: 0 0 15px; color: #888; font-size: 13px;">
                                    📞 +60 3-1234 5678 | ✉️ hello@rembayung.my
                                </p>
                                <p style="margin: 0; color: #aaa; font-size: 12px;">
                                    © ' . date('Y') . ' Rembayung. All rights reserved.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';
}

/**
 * Send email using PHPMailer
 */
function sendEmail($to, $subject, $htmlContent, $plainText = '')
{
    if (!EMAIL_NOTIFICATIONS_ENABLED) {
        return ['success' => false, 'error' => 'Email notifications disabled'];
    }

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        // UTF-8 encoding for emoji support
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlContent;
        $mail->AltBody = $plainText ?: strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlContent));

        $mail->send();
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $mail->ErrorInfo];
    }
}

/**
 * Send booking confirmation email to user
 */
function sendBookingConfirmationEmail($booking)
{
    $bookingDate = date('l, d F Y', strtotime($booking['booking_date']));
    $timeSlot = $booking['time_slot'] ?? 'N/A';

    $content = '
        <div style="text-align: center; margin-bottom: 30px;">
            <table role="presentation" width="80" height="80" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto 20px;">
                <tr>
                    <td align="center" valign="middle" style="background: linear-gradient(135deg, #D4AF37 0%, #B8963E 100%); border-radius: 50%; width: 80px; height: 80px;">
                        <span style="font-size: 40px; line-height: 1;">📋</span>
                    </td>
                </tr>
            </table>
            <h2 style="margin: 0; color: #2C2C2C; font-size: 24px;">Booking Received!</h2>
            <p style="margin: 10px 0 0; color: #666;">Thank you for choosing Rembayung</p>
        </div>
        
        <p style="color: #444; font-size: 16px; line-height: 1.6;">
            Dear <strong>' . htmlspecialchars($booking['name']) . '</strong>,
        </p>
        
        <p style="color: #666; font-size: 15px; line-height: 1.6;">
            We have received your reservation request. Our team will review and confirm your booking shortly.
        </p>
        
        <div style="background: linear-gradient(135deg, #FFF8F0 0%, #FFF5E6 100%); border-radius: 12px; padding: 25px; margin: 25px 0; border-left: 4px solid #D4AF37;">
            <h3 style="margin: 0 0 15px; color: #8B4513; font-size: 16px; text-transform: uppercase; letter-spacing: 1px;">Booking Details</h3>
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 8px 0; color: #666; width: 40%;">📅 Date</td>
                    <td style="padding: 8px 0; color: #2C2C2C; font-weight: 600;">' . $bookingDate . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">🕐 Time</td>
                    <td style="padding: 8px 0; color: #2C2C2C; font-weight: 600;">' . htmlspecialchars($timeSlot) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">👥 Party Size</td>
                    <td style="padding: 8px 0; color: #2C2C2C; font-weight: 600;">' . $booking['pax'] . ' people</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">📱 Phone</td>
                    <td style="padding: 8px 0; color: #2C2C2C; font-weight: 600;">' . htmlspecialchars($booking['phone']) . '</td>
                </tr>
            </table>
        </div>
        
        <div style="background-color: #FEF3C7; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <p style="margin: 0; color: #92400E; font-size: 14px;">
                ⏳ <strong>Status:</strong> Pending Confirmation
            </p>
            <p style="margin: 8px 0 0; color: #92400E; font-size: 13px;">
                You will receive another email once your booking is confirmed.
            </p>
        </div>
        
        <p style="color: #666; font-size: 15px; margin-top: 25px;">
            We look forward to serving you!<br>
            <strong style="color: #8B4513;">The Rembayung Team</strong>
        </p>
    ';

    $htmlContent = getEmailTemplate($content, 'Booking Received - Rembayung');

    return sendEmail(
        $booking['email'],
        '📋 Booking Received - Rembayung Restaurant',
        $htmlContent
    );
}

/**
 * Send booking status update email to user
 */
function sendBookingStatusEmail($booking, $status)
{
    $bookingDate = date('l, d F Y', strtotime($booking['booking_date']));
    $timeSlot = $booking['time_slot'] ?? 'N/A';

    $statusConfig = [
        'confirmed' => [
            'icon' => '✅',
            'title' => 'Booking Confirmed!',
            'subtitle' => 'Your table is reserved',
            'color' => '#059669',
            'bgColor' => '#D1FAE5',
            'message' => 'Great news! Your reservation has been confirmed. We\'re excited to welcome you to Rembayung.'
        ],
        'cancelled' => [
            'icon' => '❌',
            'title' => 'Booking Cancelled',
            'subtitle' => 'Your reservation has been cancelled',
            'color' => '#DC2626',
            'bgColor' => '#FEE2E2',
            'message' => 'Your reservation has been cancelled. If you did not request this cancellation or would like to make a new booking, please contact us.'
        ],
        'completed' => [
            'icon' => '🎉',
            'title' => 'Thank You for Visiting!',
            'subtitle' => 'We hope you enjoyed your meal',
            'color' => '#7C3AED',
            'bgColor' => '#EDE9FE',
            'message' => 'Thank you for dining with us at Rembayung! We hope you had a wonderful experience and look forward to seeing you again.'
        ],
        'no_show' => [
            'icon' => '⚠️',
            'title' => 'Missed Reservation',
            'subtitle' => 'We missed you!',
            'color' => '#D97706',
            'bgColor' => '#FEF3C7',
            'message' => 'We noticed you weren\'t able to make it to your reservation. We hope everything is okay. Please feel free to make another booking when convenient.'
        ]
    ];

    $config = $statusConfig[$status] ?? $statusConfig['confirmed'];

    $content = '
        <div style="text-align: center; margin-bottom: 30px;">
            <table role="presentation" width="80" height="80" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto 20px;">
                <tr>
                    <td align="center" valign="middle" style="background-color: ' . $config['bgColor'] . '; border-radius: 50%; width: 80px; height: 80px;">
                        <span style="font-size: 40px; line-height: 1;">' . $config['icon'] . '</span>
                    </td>
                </tr>
            </table>
            <h2 style="margin: 0; color: ' . $config['color'] . '; font-size: 24px;">' . $config['title'] . '</h2>
            <p style="margin: 10px 0 0; color: #666;">' . $config['subtitle'] . '</p>
        </div>
        
        <p style="color: #444; font-size: 16px; line-height: 1.6;">
            Dear <strong>' . htmlspecialchars($booking['name']) . '</strong>,
        </p>
        
        <p style="color: #666; font-size: 15px; line-height: 1.6;">
            ' . $config['message'] . '
        </p>
        
        <div style="background: linear-gradient(135deg, #FFF8F0 0%, #FFF5E6 100%); border-radius: 12px; padding: 25px; margin: 25px 0; border-left: 4px solid #D4AF37;">
            <h3 style="margin: 0 0 15px; color: #8B4513; font-size: 16px; text-transform: uppercase; letter-spacing: 1px;">Booking Details</h3>
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 8px 0; color: #666; width: 40%;">📅 Date</td>
                    <td style="padding: 8px 0; color: #2C2C2C; font-weight: 600;">' . $bookingDate . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">🕐 Time</td>
                    <td style="padding: 8px 0; color: #2C2C2C; font-weight: 600;">' . htmlspecialchars($timeSlot) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">👥 Party Size</td>
                    <td style="padding: 8px 0; color: #2C2C2C; font-weight: 600;">' . $booking['pax'] . ' people</td>
                </tr>' . (!empty($booking['table_name']) ? '
                <tr>
                    <td style="padding: 8px 0; color: #666;">🪑 Table</td>
                    <td style="padding: 8px 0; color: #2C2C2C; font-weight: 600;">' . htmlspecialchars($booking['table_name']) . '</td>
                </tr>' : '') . (!empty($booking['floor_preference']) && $booking['floor_preference'] !== 'any' ? '
                <tr>
                    <td style="padding: 8px 0; color: #666;">📍 Floor</td>
                    <td style="padding: 8px 0; color: #2C2C2C; font-weight: 600;">' . ucfirst($booking['floor_preference']) . ' Floor</td>
                </tr>' : '') . '
            </table>
        </div>';

    if ($status === 'confirmed') {
        $content .= '
        <div style="background-color: #D1FAE5; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <p style="margin: 0; color: #065F46; font-size: 14px;">
                💡 <strong>Reminder:</strong> Please arrive 10 minutes before your reservation time.
            </p>
        </div>';
    }

    $content .= '
        <p style="color: #666; font-size: 15px; margin-top: 25px;">
            Warm regards,<br>
            <strong style="color: #8B4513;">The Rembayung Team</strong>
        </p>
    ';

    $htmlContent = getEmailTemplate($content, $config['title'] . ' - Rembayung');
    $subjectEmoji = ['confirmed' => '✅', 'cancelled' => '❌', 'completed' => '🎉', 'no_show' => '⚠️'];

    return sendEmail(
        $booking['email'],
        ($subjectEmoji[$status] ?? '📋') . ' ' . $config['title'] . ' - Rembayung Restaurant',
        $htmlContent
    );
}
