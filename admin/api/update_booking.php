<?php

/**
 * Update Booking Status API
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/supabase.php';

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

if (!in_array($status, ['pending', 'confirmed', 'cancelled'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Update in Supabase
$supabase = new Supabase();
$result = $supabase->update('bookings', 'id=eq.' . $id, ['status' => $status]);

if ($result['success']) {
    echo json_encode(['success' => true, 'message' => 'Booking updated']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
}
