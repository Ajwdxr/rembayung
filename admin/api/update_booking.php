<?php

/**
 * Update Booking Status API
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/supabase.php';
require_once __DIR__ . '/../../includes/email.php';
require_once __DIR__ . '/../../includes/whatsapp.php';

// Check admin auth
if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$id = $input['id'] ?? '';
$status = $input['status'] ?? '';

// Validate
if (empty($id) || empty($status)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing id or status']);
    exit;
}

$validStatuses = ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'];
if (!in_array($status, $validStatuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

$supabase = new Supabase();

// Get booking details first (for email notification)
$bookingResult = $supabase->get('bookings', 'id=eq.' . urlencode($id) . '&select=*');
$booking = null;
if ($bookingResult['success'] && !empty($bookingResult['data'])) {
    $booking = $bookingResult['data'][0];
}

// Update in Supabase
$result = $supabase->update('bookings', 'id=eq.' . $id, ['status' => $status]);

if ($result['success']) {
    $emailSent = false;
    $whatsappSent = false;

    // Send notifications if we have booking data
    if ($booking && !empty($booking['email'])) {
        // Send email notification for status changes
        if (in_array($status, ['confirmed', 'cancelled', 'completed', 'no_show'])) {
            $emailResult = sendBookingStatusEmail($booking, $status);
            $emailSent = $emailResult['success'] ?? false;
        }

        // Send WhatsApp notification to admin for status changes
        $whatsappResult = sendBookingStatusNotification($booking, $status);
        $whatsappSent = $whatsappResult['success'] ?? false;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Booking updated',
        'notifications' => [
            'email' => $emailSent,
            'whatsapp' => $whatsappSent
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
}
