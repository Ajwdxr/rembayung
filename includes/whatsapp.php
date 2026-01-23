<?php

/**
 * WhatsApp Notification Helper using CallMeBot
 * 
 * Free WhatsApp API for admin notifications
 * Documentation: https://www.callmebot.com/blog/free-api-whatsapp-messages/
 */

// CallMeBot Configuration
define('CALLMEBOT_PHONE', '601110638176');
define('CALLMEBOT_APIKEY', '7160882');
define('WHATSAPP_NOTIFICATIONS_ENABLED', true);

/**
 * Send WhatsApp message via CallMeBot
 * 
 * @param string $message The message to send
 * @return array Result with success status and any error message
 */
function sendWhatsApp($message)
{
    if (!WHATSAPP_NOTIFICATIONS_ENABLED) {
        return ['success' => false, 'error' => 'WhatsApp notifications disabled'];
    }

    // URL encode the message
    $encodedMessage = urlencode($message);

    // Build the API URL
    $url = "https://api.callmebot.com/whatsapp.php?phone=" . CALLMEBOT_PHONE
        . "&text=" . $encodedMessage
        . "&apikey=" . CALLMEBOT_APIKEY;

    // Make the request
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['success' => false, 'error' => 'cURL error: ' . $error];
    }

    // CallMeBot returns 200 on success
    if ($httpCode >= 200 && $httpCode < 300) {
        return ['success' => true, 'response' => $response];
    }

    return ['success' => false, 'error' => 'HTTP ' . $httpCode . ': ' . $response];
}

/**
 * Send booking notification to admin
 * 
 * @param array $booking Booking data array
 * @return array Result with success status
 */
function sendBookingNotification($booking)
{
    $message = "🍽️ *New Booking Alert!*\n\n";
    $message .= "👤 *Name:* " . ($booking['name'] ?? 'N/A') . "\n";
    $message .= "📅 *Date:* " . date('d M Y', strtotime($booking['booking_date'] ?? 'now')) . "\n";
    $message .= "🕐 *Time:* " . ($booking['time_slot'] ?? 'N/A') . "\n";
    $message .= "👥 *Pax:* " . ($booking['pax'] ?? 'N/A') . " people\n";
    $message .= "📱 *Phone:* " . ($booking['phone'] ?? 'N/A') . "\n";
    $message .= "📧 *Email:* " . ($booking['email'] ?? 'N/A') . "\n";

    if (!empty($booking['special_requests'])) {
        $message .= "📝 *Notes:* " . $booking['special_requests'] . "\n";
    }

    $message .= "\n✅ Status: Pending\n";
    $message .= "🔗 View in admin panel";

    return sendWhatsApp($message);
}

/**
 * Send booking status update notification
 * 
 * @param array $booking Booking data array
 * @param string $newStatus New status (confirmed, cancelled, etc.)
 * @return array Result with success status
 */
function sendBookingStatusNotification($booking, $newStatus)
{
    $statusEmoji = [
        'confirmed' => '✅',
        'cancelled' => '❌',
        'completed' => '🎉',
        'no_show' => '⚠️'
    ];

    $emoji = $statusEmoji[$newStatus] ?? '📋';

    $message = "{$emoji} *Booking {$newStatus}!*\n\n";
    $message .= "👤 " . ($booking['name'] ?? 'N/A') . "\n";
    $message .= "📅 " . date('d M Y', strtotime($booking['booking_date'] ?? 'now')) . "\n";
    $message .= "🕐 " . ($booking['time_slot'] ?? 'N/A') . "\n";
    $message .= "👥 " . ($booking['pax'] ?? 'N/A') . " pax";

    return sendWhatsApp($message);
}
