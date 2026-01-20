<?php
/**
 * Booking Submit API
 * Updated to support session-based time slots
 */

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/supabase.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$supabase = new Supabase();

// Collect and sanitize input
$data = [
    'booking_date' => sanitize($_POST['booking_date'] ?? ''),
    'session_id' => sanitize($_POST['session_id'] ?? ''),
    'time_slot_id' => sanitize($_POST['time_slot_id'] ?? ''),
    'pax' => (int)($_POST['pax'] ?? 0),
    'name' => sanitize($_POST['name'] ?? ''),
    'phone' => sanitize($_POST['phone'] ?? ''),
    'email' => sanitize($_POST['email'] ?? ''),
    'special_requests' => sanitize($_POST['special_requests'] ?? ''),
    'status' => 'pending'
];

// Keep time_slot for backward compatibility - fetch from time_slot_id
if (!empty($data['time_slot_id'])) {
    $slotResult = $supabase->get('session_time_slots', 'id=eq.' . $data['time_slot_id'] . '&select=time_label');
    if ($slotResult['success'] && !empty($slotResult['data'])) {
        $data['time_slot'] = $slotResult['data'][0]['time_label'] ?? 'custom';
    } else {
        $data['time_slot'] = 'custom';
    }
} else {
    $data['time_slot'] = sanitize($_POST['time_slot'] ?? '');
}

// Validate required fields
$errors = [];

if (empty($data['booking_date'])) $errors[] = 'Date is required';
if (empty($data['session_id'])) $errors[] = 'Session is required';
if (empty($data['time_slot_id'])) $errors[] = 'Time slot is required';
if ($data['pax'] < MIN_PAX || $data['pax'] > MAX_PAX) $errors[] = 'Party size must be between ' . MIN_PAX . ' and ' . MAX_PAX;
if (empty($data['name'])) $errors[] = 'Name is required';
if (empty($data['phone'])) $errors[] = 'Phone is required';
if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';

// Validate date is in the future
$bookingDate = strtotime($data['booking_date']);
$today = strtotime('today');
if ($bookingDate < $today) {
    $errors[] = 'Booking date must be in the future';
}

// Validate session exists and is active
if (!empty($data['session_id'])) {
    $sessionResult = $supabase->get('booking_sessions', 'id=eq.' . $data['session_id'] . '&is_active=eq.true');
    if (!$sessionResult['success'] || empty($sessionResult['data'])) {
        $errors[] = 'Invalid session selected';
    }
}

// Validate time slot exists and is active
if (!empty($data['time_slot_id'])) {
    $slotResult = $supabase->get('session_time_slots', 'id=eq.' . $data['time_slot_id'] . '&is_active=eq.true');
    if (!$slotResult['success'] || empty($slotResult['data'])) {
        $errors[] = 'Invalid time slot selected';
    }
}

// Check session capacity (remaining pax)
if (!empty($data['session_id']) && !empty($data['booking_date'])) {
    // Get session max_pax
    $sessionMaxResult = $supabase->get('booking_sessions', 'id=eq.' . $data['session_id'] . '&select=max_pax');
    $maxPax = 225; // default
    if ($sessionMaxResult['success'] && !empty($sessionMaxResult['data'])) {
        $maxPax = (int)($sessionMaxResult['data'][0]['max_pax'] ?? 225);
    }
    
    // Get current booked pax for this session/date
    $bookedResult = $supabase->get(
        'bookings',
        'session_id=eq.' . $data['session_id'] . 
        '&booking_date=eq.' . $data['booking_date'] . 
        '&status=in.(pending,confirmed)&select=pax'
    );
    
    $bookedPax = 0;
    if ($bookedResult['success'] && !empty($bookedResult['data'])) {
        foreach ($bookedResult['data'] as $booking) {
            $bookedPax += (int)$booking['pax'];
        }
    }
    
    $remainingPax = $maxPax - $bookedPax;
    
    if ($data['pax'] > $remainingPax) {
        if ($remainingPax <= 0) {
            $errors[] = 'Sorry, this session is fully booked. Please select a different date or session.';
        } else {
            $errors[] = 'Sorry, only ' . $remainingPax . ' pax available for this session. Please reduce party size or choose another session.';
        }
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Insert into Supabase
$result = $supabase->insert('bookings', $data);

if ($result['success']) {
    echo json_encode([
        'success' => true,
        'message' => 'Booking submitted successfully',
        'data' => $result['data']
    ]);
} else {
    http_response_code(500);
    // Include detailed error for debugging
    $errorMsg = 'Failed to submit booking.';
    if (isset($result['data']['message'])) {
        $errorMsg .= ' ' . $result['data']['message'];
    } elseif (isset($result['data']['error'])) {
        $errorMsg .= ' ' . $result['data']['error'];
    }
    echo json_encode([
        'success' => false,
        'message' => $errorMsg,
        'debug' => $result['data'] ?? null
    ]);
}
