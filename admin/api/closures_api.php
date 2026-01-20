<?php
/**
 * Closures API
 * CRUD operations for restaurant closure dates
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

function handleGet($supabase) {
    $result = $supabase->get('restaurant_closures', 'order=closure_date.asc');
    
    if ($result['success']) {
        echo json_encode(['success' => true, 'data' => $result['data'] ?? []]);
    } else {
        // If table doesn't exist, return empty array
        echo json_encode(['success' => true, 'data' => [], 'message' => 'No closures table found']);
    }
}

function handlePost($supabase) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $data = [
        'closure_date' => $input['closure_date'] ?? '',
        'reason' => sanitize($input['reason'] ?? '')
    ];
    
    if (empty($data['closure_date'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Date is required']);
        return;
    }
    
    $result = $supabase->insert('restaurant_closures', $data);
    
    if ($result['success']) {
        echo json_encode(['success' => true, 'data' => $result['data']]);
    } else {
        http_response_code(500);
        $errorMsg = 'Failed to add closure';
        if (strpos(json_encode($result['data']), 'duplicate') !== false) {
            $errorMsg = 'A closure already exists for this date';
        }
        echo json_encode(['success' => false, 'message' => $errorMsg]);
    }
}

function handlePatch($supabase) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? '';
    
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID is required']);
        return;
    }
    
    $data = [];
    if (isset($input['closure_date'])) $data['closure_date'] = $input['closure_date'];
    if (isset($input['reason'])) $data['reason'] = sanitize($input['reason']);
    
    $result = $supabase->update('restaurant_closures', 'id=eq.' . $id, $data);
    
    if ($result['success']) {
        echo json_encode(['success' => true, 'data' => $result['data']]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update closure']);
    }
}

function handleDelete($supabase) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? '';
    
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID is required']);
        return;
    }
    
    $result = $supabase->delete('restaurant_closures', 'id=eq.' . $id);
    
    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete closure']);
    }
}
