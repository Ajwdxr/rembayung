<?php

/**
 * API to get table status for a specific date and session
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

// 1. Fetch all tables
$tablesResult = $supabase->get('restaurant_tables', 'status=eq.active&order=floor.asc,name.asc');

if (!$tablesResult['success']) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch tables']);
    exit;
}

$tables = $tablesResult['data'] ?? [];

// 2. Fetch bookings for this date/session to see which tables are taken
$bookedTableIds = [];

if (!empty($sessionId)) {
    $bookingsQuery = 'booking_date=eq.' . $date .
        '&session_id=eq.' . $sessionId .
        '&status=in.(pending,confirmed)' .
        '&select=table_id';

    $bookingsResult = $supabase->get('bookings', $bookingsQuery);

    if ($bookingsResult['success'] && !empty($bookingsResult['data'])) {
        foreach ($bookingsResult['data'] as $booking) {
            if (!empty($booking['table_id'])) {
                $bookedTableIds[] = $booking['table_id'];
            }
        }
    }
}

// 3. Map status to tables
$tablesWithStatus = [];
foreach ($tables as $table) {
    $isBooked = in_array($table['id'], $bookedTableIds);

    $tablesWithStatus[] = [
        'id' => $table['id'],
        'name' => $table['name'],
        'floor' => (int)$table['floor'],
        'min_pax' => (int)$table['min_pax'],
        'max_pax' => (int)$table['max_pax'],
        'status' => $isBooked ? 'booked' : 'available'
    ];
}

echo json_encode([
    'success' => true,
    'date' => $date,
    'session_id' => $sessionId,
    'data' => $tablesWithStatus
]);
