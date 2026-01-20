<?php
/**
 * Settings API
 * Manage restaurant settings
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
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

function handleGet($supabase) {
    $key = sanitize($_GET['key'] ?? '');
    
    if (empty($key)) {
        // Get all settings
        $result = $supabase->get('restaurant_settings', 'order=setting_key.asc');
        if ($result['success']) {
            echo json_encode(['success' => true, 'data' => $result['data'] ?? []]);
        } else {
            echo json_encode(['success' => true, 'data' => []]);
        }
    } else {
        // Get specific setting
        $result = $supabase->get('restaurant_settings', 'setting_key=eq.' . $key);
        if ($result['success'] && !empty($result['data'])) {
            echo json_encode(['success' => true, 'data' => $result['data'][0]]);
        } else {
            echo json_encode(['success' => true, 'data' => null]);
        }
    }
}

function handlePost($supabase) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $key = sanitize($input['setting_key'] ?? '');
    $value = $input['setting_value'] ?? '';
    
    if (empty($key)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Setting key is required']);
        return;
    }
    
    // Check if setting exists
    $existingResult = $supabase->get('restaurant_settings', 'setting_key=eq.' . $key);
    
    if ($existingResult['success'] && !empty($existingResult['data'])) {
        // Update existing
        $id = $existingResult['data'][0]['id'];
        $result = $supabase->update('restaurant_settings', 'id=eq.' . $id, [
            'setting_value' => $value,
            'updated_at' => date('c')
        ]);
    } else {
        // Insert new
        $result = $supabase->insert('restaurant_settings', [
            'setting_key' => $key,
            'setting_value' => $value
        ]);
    }
    
    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Setting saved']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save setting']);
    }
}
