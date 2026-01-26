<?php

/**
 * Tables Management API
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/supabase.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$supabase = new Supabase();
$method = $_SERVER['REQUEST_METHOD'];

// Handle GET - List tables
if ($method === 'GET') {
    $query = 'select=*&order=floor.asc,name.asc';
    $result = $supabase->get('restaurant_tables', $query);

    if ($result['success']) {
        echo json_encode($result);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to fetch tables']);
    }
    exit;
}

// Handle POST - Create table
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $name = sanitize($input['name'] ?? '');
    $floor = (int)($input['floor'] ?? 0);
    $minPax = (int)($input['min_pax'] ?? 0);
    $maxPax = (int)($input['max_pax'] ?? 0);
    $status = sanitize($input['status'] ?? 'active');

    // Validation
    if (empty($name) || !in_array($floor, [1, 2]) || $minPax < 1 || $maxPax < $minPax) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit;
    }

    $data = [
        'name' => $name,
        'floor' => $floor,
        'min_pax' => $minPax,
        'max_pax' => $maxPax,
        'status' => $status
    ];

    $result = $supabase->insert('restaurant_tables', $data);

    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Table created successfully', 'data' => $result['data']]);
    } else {
        http_response_code(400); // 400 usually for duplicate key
        echo json_encode(['success' => false, 'error' => 'Failed to create table. Name might be duplicate.']);
    }
    exit;
}

// Handle DELETE
if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);

    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID required']);
        exit;
    }

    $result = $supabase->delete('restaurant_tables', 'id=eq.' . $id);

    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Table deleted']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to delete table']);
    }
    exit;
}

// Handle PUT/PATCH - Update
if ($method === 'PUT' || $method === 'PATCH') {
    $input = json_decode(file_get_contents('php://input'), true);

    $id = (int)($input['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID required']);
        exit;
    }

    $updateData = [];
    if (isset($input['name'])) $updateData['name'] = sanitize($input['name']);
    if (isset($input['floor'])) $updateData['floor'] = (int)$input['floor'];
    if (isset($input['min_pax'])) $updateData['min_pax'] = (int)$input['min_pax'];
    if (isset($input['max_pax'])) $updateData['max_pax'] = (int)$input['max_pax'];
    if (isset($input['status'])) $updateData['status'] = sanitize($input['status']);

    if (empty($updateData)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'No data to update']);
        exit;
    }

    $result = $supabase->update('restaurant_tables', 'id=eq.' . $id, $updateData);

    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Table updated']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to update table']);
    }
    exit;
}
