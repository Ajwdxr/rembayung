<?php
/**
 * Public API to get active sessions and time slots
 * Used by the booking form
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/supabase.php';

$supabase = new Supabase();

// Get active sessions
$sessionsResult = $supabase->get('booking_sessions', 'is_active=eq.true&order=display_order.asc');

if (!$sessionsResult['success']) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch sessions']);
    exit;
}

$sessions = $sessionsResult['data'] ?? [];

// Get active time slots
$slotsResult = $supabase->get('session_time_slots', 'is_active=eq.true&order=display_order.asc');
$slots = $slotsResult['success'] ? ($slotsResult['data'] ?? []) : [];

// Group slots by session
$slotsBySession = [];
foreach ($slots as $slot) {
    $sessionId = $slot['session_id'];
    if (!isset($slotsBySession[$sessionId])) {
        $slotsBySession[$sessionId] = [];
    }
    $slotsBySession[$sessionId][] = [
        'id' => $slot['id'],
        'time_label' => $slot['time_label'],
        'time_value' => $slot['time_value']
    ];
}

// Build response with sessions and their time slots
$response = [];
foreach ($sessions as $session) {
    $response[] = [
        'id' => $session['id'],
        'name' => $session['name'],
        'description' => $session['description'],
        'time_slots' => $slotsBySession[$session['id']] ?? []
    ];
}

echo json_encode(['success' => true, 'data' => $response]);
