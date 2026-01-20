<?php
/**
 * API to get calendar availability for a date range
 * Returns availability status for each date in the range
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/supabase.php';

$supabase = new Supabase();

// Get parameters
$startDate = sanitize($_GET['start'] ?? '');
$endDate = sanitize($_GET['end'] ?? '');

if (empty($startDate) || empty($endDate)) {
    // Default to current month
    $startDate = date('Y-m-01');
    $endDate = date('Y-m-t');
}

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
    exit;
}

// Get all active sessions with their capacity
$sessionsResult = $supabase->get('booking_sessions', 'is_active=eq.true');
$sessions = $sessionsResult['success'] ? ($sessionsResult['data'] ?? []) : [];

// Calculate total max pax per day (sum of all sessions)
$totalDailyCapacity = 0;
foreach ($sessions as $session) {
    $totalDailyCapacity += (int)($session['max_pax'] ?? 225);
}

// If no sessions, default to 225
if ($totalDailyCapacity === 0) {
    $totalDailyCapacity = 225;
}

// Get all bookings in the date range (pending and confirmed)
$bookingsResult = $supabase->get(
    'bookings',
    'booking_date=gte.' . $startDate . 
    '&booking_date=lte.' . $endDate . 
    '&status=in.(pending,confirmed)&select=booking_date,pax'
);

$bookings = $bookingsResult['success'] ? ($bookingsResult['data'] ?? []) : [];

// Group bookings by date
$bookedByDate = [];
foreach ($bookings as $booking) {
    $date = $booking['booking_date'];
    if (!isset($bookedByDate[$date])) {
        $bookedByDate[$date] = 0;
    }
    $bookedByDate[$date] += (int)$booking['pax'];
}

// Get closures in the date range
$closuresResult = $supabase->get(
    'restaurant_closures',
    'closure_date=gte.' . $startDate . '&closure_date=lte.' . $endDate
);

$closures = [];
if ($closuresResult['success'] && !empty($closuresResult['data'])) {
    foreach ($closuresResult['data'] as $closure) {
        $closures[$closure['closure_date']] = $closure['reason'] ?? 'Closed';
    }
}

// Get fixed weekly closure day from settings
$weeklyClosureDay = null;
$settingsResult = $supabase->get('restaurant_settings', 'setting_key=eq.weekly_closure_day');
if ($settingsResult['success'] && !empty($settingsResult['data'])) {
    $value = $settingsResult['data'][0]['setting_value'] ?? null;
    if ($value !== null && $value !== '') {
        $weeklyClosureDay = (int)$value;
    }
}

// Day names for display
$dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

// Build calendar data
$calendar = [];
$currentDate = new DateTime($startDate);
$end = new DateTime($endDate);

while ($currentDate <= $end) {
    $dateStr = $currentDate->format('Y-m-d');
    $dayOfWeek = (int)$currentDate->format('w'); // 0 = Sunday, 1 = Monday, etc.
    $bookedPax = $bookedByDate[$dateStr] ?? 0;
    $remainingPax = $totalDailyCapacity - $bookedPax;
    
    // Determine status
    if (isset($closures[$dateStr])) {
        // Specific closure date
        $status = 'closed';
        $statusLabel = $closures[$dateStr];
    } elseif ($weeklyClosureDay !== null && $dayOfWeek === $weeklyClosureDay) {
        // Fixed weekly closure day
        $status = 'closed';
        $statusLabel = 'Closed every ' . $dayNames[$weeklyClosureDay];
    } elseif ($remainingPax <= 0) {
        $status = 'full';
        $statusLabel = 'Fully Booked';
    } elseif ($remainingPax < ($totalDailyCapacity * 0.3)) {
        // Less than 30% available - almost full
        $status = 'limited';
        $statusLabel = $remainingPax . ' pax left';
    } else {
        $status = 'available';
        $statusLabel = 'Available';
    }
    
    $calendar[] = [
        'date' => $dateStr,
        'day' => (int)$currentDate->format('j'),
        'dayOfWeek' => (int)$currentDate->format('w'),
        'status' => $status,
        'statusLabel' => $statusLabel,
        'bookedPax' => $bookedPax,
        'remainingPax' => max($remainingPax, 0),
        'totalCapacity' => $totalDailyCapacity
    ];
    
    $currentDate->modify('+1 day');
}

echo json_encode([
    'success' => true,
    'startDate' => $startDate,
    'endDate' => $endDate,
    'totalDailyCapacity' => $totalDailyCapacity,
    'data' => $calendar
]);
