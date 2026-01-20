<?php
/**
 * Session Management API
 * Handles CRUD operations for booking sessions and time slots
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../../includes/config.php';
    require_once __DIR__ . '/../../includes/supabase.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to load dependencies: ' . $e->getMessage()]);
    exit;
}

// Check admin auth
if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$supabase = new Supabase();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($supabase);
        break;
    case 'POST':
        handlePost($supabase);
        break;
    case 'PATCH':
        handlePatch($supabase);
        break;
    case 'DELETE':
        handleDelete($supabase);
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

/**
 * GET - Fetch all sessions with their time slots
 */
function handleGet($supabase) {
    // Get all sessions
    $sessionsResult = $supabase->get('booking_sessions', 'order=display_order.asc');
    
    if (!$sessionsResult['success']) {
        // Check if it's a "table not found" error (404 from Supabase)
        if ($sessionsResult['code'] === 404 || strpos(json_encode($sessionsResult['data'] ?? ''), 'relation') !== false) {
            echo json_encode([
                'success' => true, 
                'data' => [],
                'message' => 'Database tables not created yet. Please run the SQL migration in Supabase.'
            ]);
            return;
        }
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch sessions: ' . json_encode($sessionsResult['data'])]);
        return;
    }
    
    $sessions = $sessionsResult['data'] ?? [];
    
    // Get all time slots
    $slotsResult = $supabase->get('session_time_slots', 'order=display_order.asc');
    $slots = $slotsResult['success'] ? ($slotsResult['data'] ?? []) : [];
    
    // Group slots by session
    $slotsBySession = [];
    foreach ($slots as $slot) {
        $sessionId = $slot['session_id'];
        if (!isset($slotsBySession[$sessionId])) {
            $slotsBySession[$sessionId] = [];
        }
        $slotsBySession[$sessionId][] = $slot;
    }
    
    // Attach slots to sessions
    foreach ($sessions as &$session) {
        $session['time_slots'] = $slotsBySession[$session['id']] ?? [];
    }
    
    echo json_encode(['success' => true, 'data' => $sessions]);
}

/**
 * POST - Create session or time slot
 */
function handlePost($supabase) {
    $input = json_decode(file_get_contents('php://input'), true);
    $type = $input['type'] ?? '';
    
    if ($type === 'session') {
        $data = [
            'name' => sanitize($input['name'] ?? ''),
            'description' => sanitize($input['description'] ?? ''),
            'max_pax' => (int)($input['max_pax'] ?? 225),
            'display_order' => (int)($input['display_order'] ?? 0),
            'is_active' => (bool)($input['is_active'] ?? true)
        ];
        
        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Session name is required']);
            return;
        }
        
        $result = $supabase->insert('booking_sessions', $data);
    } elseif ($type === 'time_slot') {
        $data = [
            'session_id' => $input['session_id'] ?? '',
            'time_value' => $input['time_value'] ?? '',
            'time_label' => sanitize($input['time_label'] ?? ''),
            'max_bookings' => (int)($input['max_bookings'] ?? 10),
            'display_order' => (int)($input['display_order'] ?? 0),
            'is_active' => (bool)($input['is_active'] ?? true)
        ];
        
        if (empty($data['session_id']) || empty($data['time_value']) || empty($data['time_label'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Session ID, time value, and time label are required']);
            return;
        }
        
        $result = $supabase->insert('session_time_slots', $data);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid type. Must be "session" or "time_slot"']);
        return;
    }
    
    if ($result['success']) {
        echo json_encode(['success' => true, 'data' => $result['data']]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create']);
    }
}

/**
 * PATCH - Update session or time slot
 */
function handlePatch($supabase) {
    $input = json_decode(file_get_contents('php://input'), true);
    $type = $input['type'] ?? '';
    $id = $input['id'] ?? '';
    
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID is required']);
        return;
    }
    
    if ($type === 'session') {
        $data = [];
        if (isset($input['name'])) $data['name'] = sanitize($input['name']);
        if (isset($input['description'])) $data['description'] = sanitize($input['description']);
        if (isset($input['max_pax'])) $data['max_pax'] = (int)$input['max_pax'];
        if (isset($input['display_order'])) $data['display_order'] = (int)$input['display_order'];
        if (isset($input['is_active'])) $data['is_active'] = (bool)$input['is_active'];
        
        $result = $supabase->update('booking_sessions', 'id=eq.' . $id, $data);
    } elseif ($type === 'time_slot') {
        $data = [];
        if (isset($input['time_value'])) $data['time_value'] = $input['time_value'];
        if (isset($input['time_label'])) $data['time_label'] = sanitize($input['time_label']);
        if (isset($input['max_bookings'])) $data['max_bookings'] = (int)$input['max_bookings'];
        if (isset($input['display_order'])) $data['display_order'] = (int)$input['display_order'];
        if (isset($input['is_active'])) $data['is_active'] = (bool)$input['is_active'];
        
        $result = $supabase->update('session_time_slots', 'id=eq.' . $id, $data);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid type']);
        return;
    }
    
    if ($result['success']) {
        echo json_encode(['success' => true, 'data' => $result['data']]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update']);
    }
}

/**
 * DELETE - Delete session or time slot
 */
function handleDelete($supabase) {
    $input = json_decode(file_get_contents('php://input'), true);
    $type = $input['type'] ?? '';
    $id = $input['id'] ?? '';
    
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID is required']);
        return;
    }
    
    if ($type === 'session') {
        $result = $supabase->delete('booking_sessions', 'id=eq.' . $id);
    } elseif ($type === 'time_slot') {
        $result = $supabase->delete('session_time_slots', 'id=eq.' . $id);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid type']);
        return;
    }
    
    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete']);
    }
}
