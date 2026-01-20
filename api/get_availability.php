<?php
/**
 * API to get session availability (remaining pax) for a specific date
 * Returns remaining capacity for each session on the given date
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/supabase.php';

$supabase = new Supabase();

// Get parameters
$date = sanitize($_GET['date'] ?? '');
$sessionId = sanitize($_GET['session_id'] ?? '');

if (empty($date)) {
    echo json_encode(['success' => false, 'message' => 'Date is required']);
    exit;
}

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
    exit;
}

// Get all active sessions with their capacity
$sessionsResult = $supabase->get('booking_sessions', 'is_active=eq.true&order=display_order.asc');

if (!$sessionsResult['success']) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch sessions']);
    exit;
}

$sessions = $sessionsResult['data'] ?? [];

// If specific session requested, filter
if (!empty($sessionId)) {
    $sessions = array_filter($sessions, fn($s) => $s['id'] === $sessionId);
}

// Get bookings for the date (pending and confirmed only)
$bookingsResult = $supabase->get(
    'bookings', 
    'booking_date=eq.' . $date . '&status=in.(pending,confirmed)&select=session_id,pax'
);

$bookings = $bookingsResult['success'] ? ($bookingsResult['data'] ?? []) : [];

// Calculate booked pax per session
$bookedBySession = [];
foreach ($bookings as $booking) {
    $sid = $booking['session_id'];
    if (!isset($bookedBySession[$sid])) {
        $bookedBySession[$sid] = 0;
    }
    $bookedBySession[$sid] += (int)$booking['pax'];
}

// Build availability response
$availability = [];
foreach ($sessions as $session) {
    $maxPax = (int)($session['max_pax'] ?? 225);
    $bookedPax = $bookedBySession[$session['id']] ?? 0;
    $remainingPax = max($maxPax - $bookedPax, 0);
    
    $availability[] = [
        'session_id' => $session['id'],
        'session_name' => $session['name'],
        'max_pax' => $maxPax,
        'booked_pax' => $bookedPax,
        'remaining_pax' => $remainingPax,
        'is_available' => $remainingPax > 0
    ];
}

echo json_encode([
    'success' => true,
    'date' => $date,
    'data' => $availability
]);
