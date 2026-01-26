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
$updateData = ['status' => $status];

// Auto-assign table if confirming and no table assigned
if ($status === 'confirmed' && $booking && empty($booking['table_id'])) {
    // 1. Get all active tables
    $query = 'status=eq.active&order=min_pax.asc';

    // Check for floor preference
    $preferredFloor = null;
    if (!empty($booking['floor_preference']) && $booking['floor_preference'] !== 'any') {
        if ($booking['floor_preference'] === 'ground') $preferredFloor = 1;
        if ($booking['floor_preference'] === 'upper') $preferredFloor = 2;
    }

    $tablesResult = $supabase->get('restaurant_tables', $query);

    // 2. Get occupied tables for this session
    $occupiedResult = $supabase->get(
        'bookings',
        'booking_date=eq.' . $booking['booking_date'] .
            '&session_id=eq.' . $booking['session_id'] .
            '&status=in.(confirmed,checked_in)' .
            '&table_id=not.is.null' .
            '&select=table_id'
    );

    $occupiedTableIds = [];
    if ($occupiedResult['success'] && !empty($occupiedResult['data'])) {
        foreach ($occupiedResult['data'] as $b) {
            $occupiedTableIds[] = $b['table_id'];
        }
    }

    // 3. Find best fit
    $assignedTableId = null;
    $pax = $booking['pax'];

    if ($tablesResult['success'] && !empty($tablesResult['data'])) {
        $tables = $tablesResult['data'];

        // Priority 1: Match Floor + Capacity (Ideal)
        if ($preferredFloor) {
            foreach ($tables as $table) {
                if (in_array($table['id'], $occupiedTableIds)) continue;
                if ($table['floor'] != $preferredFloor) continue; // Skip wrong floor

                if ($pax >= $table['min_pax'] && $pax <= $table['max_pax']) {
                    $assignedTableId = $table['id'];
                    break;
                }
            }
        }

        // Priority 2: Match Capacity (Any floor - if no pref or P1 failed)
        if (!$assignedTableId) {
            foreach ($tables as $table) {
                if (in_array($table['id'], $occupiedTableIds)) continue;

                if ($pax >= $table['min_pax'] && $pax <= $table['max_pax']) {
                    $assignedTableId = $table['id'];
                    break;
                }
            }
        }

        // Priority 3: Larger tables (Any floor)
        if (!$assignedTableId) {
            foreach ($tables as $table) {
                if (in_array($table['id'], $occupiedTableIds)) continue;
                if ($pax <= $table['max_pax']) {
                    $assignedTableId = $table['id'];
                    break;
                }
            }
        }
    }

    if ($assignedTableId) {
        $updateData['table_id'] = $assignedTableId;
    }
}

$result = $supabase->update('bookings', 'id=eq.' . $id, $updateData);

if ($result['success']) {
    $emailSent = false;
    $whatsappSent = false;

    // Send notifications if we have booking data
    if ($booking && !empty($booking['email'])) {

        // Enrich booking data with table info if confirmed
        if ($status === 'confirmed') {
            $tableId = $updateData['table_id'] ?? $booking['table_id'] ?? null;

            if ($tableId) {
                // Fetch table details
                $tableResult = $supabase->get('restaurant_tables', 'id=eq.' . $tableId);
                if ($tableResult['success'] && !empty($tableResult['data'])) {
                    $booking['table_name'] = $tableResult['data'][0]['name'];
                    $booking['table_id'] = $tableId; // Ensure updated ID is set
                }
            }
        }

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
