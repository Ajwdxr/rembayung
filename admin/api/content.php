<?php
/**
 * Content Management API
 * Handles CRUD operations for About, Menu, and Gallery content with file uploads
 */
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/supabase.php';

// Check admin authentication
if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$supabase = new Supabase();

// Define upload paths
$uploadPaths = [
    'about' => __DIR__ . '/../../assets/uploads/about/',
    'menu' => __DIR__ . '/../../assets/uploads/menu/',
    'gallery' => __DIR__ . '/../../assets/uploads/gallery/',
    'hero' => __DIR__ . '/../../assets/uploads/hero/'
];

// Allowed file types
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$maxFileSize = 5 * 1024 * 1024; // 5MB

/**
 * Handle file upload
 */
function handleFileUpload($file, $uploadPath, $prefix = '') {
    global $allowedTypes, $maxFileSize;
    
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'filename' => null];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'File upload error: ' . $file['error']];
    }
    
    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: JPG, PNG, WebP, GIF'];
    }
    
    // Validate file size
    if ($file['size'] > $maxFileSize) {
        return ['success' => false, 'error' => 'File too large. Maximum size: 5MB'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . uniqid() . '_' . time() . '.' . strtolower($extension);
    $targetPath = $uploadPath . $filename;
    
    // Create directory if not exists
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'error' => 'Failed to save file'];
}

/**
 * Delete old file
 */
function deleteOldFile($filename, $uploadPath) {
    if ($filename && file_exists($uploadPath . $filename)) {
        unlink($uploadPath . $filename);
    }
}

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Handle create/update with file upload
        $type = $_POST['type'] ?? '';
        $id = $_POST['id'] ?? '';
        
        if ($type === 'about') {
            $data = [
                'title' => sanitize($_POST['title'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'quote' => $_POST['quote'] ?? '',
                'quote_author' => sanitize($_POST['quote_author'] ?? ''),
                'display_order' => (int)($_POST['display_order'] ?? 0),
                'is_active' => true,
                'updated_at' => date('c')
            ];
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload = handleFileUpload($_FILES['image'], $uploadPaths['about'], 'about_');
                if (!$upload['success']) {
                    echo json_encode(['success' => false, 'error' => $upload['error']]);
                    exit;
                }
                if ($upload['filename']) {
                    // Delete old file if updating
                    if ($id) {
                        $existing = $supabase->get('about_content', 'id=eq.' . $id);
                        if ($existing['success'] && !empty($existing['data'])) {
                            deleteOldFile($existing['data'][0]['image_filename'], $uploadPaths['about']);
                        }
                    }
                    $data['image_filename'] = $upload['filename'];
                }
            }
            
            if ($id) {
                // Update
                $result = $supabase->update('about_content', 'id=eq.' . $id, $data);
            } else {
                // Insert
                $result = $supabase->insert('about_content', $data);
            }
            
            echo json_encode(['success' => $result['success'], 'data' => $result['data']]);
            
        } elseif ($type === 'menu') {
            $data = [
                'name' => sanitize($_POST['name'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'price' => sanitize($_POST['price'] ?? ''),
                'category' => sanitize($_POST['category'] ?? 'main'),
                'is_featured' => isset($_POST['is_featured']) && $_POST['is_featured'] === '1',
                'display_order' => (int)($_POST['display_order'] ?? 0),
                'is_active' => true,
                'updated_at' => date('c')
            ];
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload = handleFileUpload($_FILES['image'], $uploadPaths['menu'], 'menu_');
                if (!$upload['success']) {
                    echo json_encode(['success' => false, 'error' => $upload['error']]);
                    exit;
                }
                if ($upload['filename']) {
                    // Delete old file if updating
                    if ($id) {
                        $existing = $supabase->get('menu_items', 'id=eq.' . $id);
                        if ($existing['success'] && !empty($existing['data'])) {
                            deleteOldFile($existing['data'][0]['image_filename'], $uploadPaths['menu']);
                        }
                    }
                    $data['image_filename'] = $upload['filename'];
                }
            }
            
            if ($id) {
                $result = $supabase->update('menu_items', 'id=eq.' . $id, $data);
            } else {
                $result = $supabase->insert('menu_items', $data);
            }
            
            echo json_encode(['success' => $result['success'], 'data' => $result['data']]);
            
        } elseif ($type === 'gallery') {
            $data = [
                'alt_text' => sanitize($_POST['alt_text'] ?? ''),
                'category' => sanitize($_POST['category'] ?? 'ambiance'),
                'display_order' => (int)($_POST['display_order'] ?? 0),
                'is_active' => true,
                'updated_at' => date('c')
            ];
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload = handleFileUpload($_FILES['image'], $uploadPaths['gallery'], 'gallery_');
                if (!$upload['success']) {
                    echo json_encode(['success' => false, 'error' => $upload['error']]);
                    exit;
                }
                if ($upload['filename']) {
                    // Delete old file if updating
                    if ($id) {
                        $existing = $supabase->get('gallery_images', 'id=eq.' . $id);
                        if ($existing['success'] && !empty($existing['data'])) {
                            deleteOldFile($existing['data'][0]['image_filename'], $uploadPaths['gallery']);
                        }
                    }
                    $data['image_filename'] = $upload['filename'];
                }
            } elseif (!$id) {
                // New gallery item requires image
                echo json_encode(['success' => false, 'error' => 'Image is required for gallery items']);
                exit;
            }
            
            if ($id) {
                $result = $supabase->update('gallery_images', 'id=eq.' . $id, $data);
            } else {
                $result = $supabase->insert('gallery_images', $data);
            }
            
            echo json_encode(['success' => $result['success'], 'data' => $result['data']]);
            
        } elseif ($type === 'hero') {
            $data = [
                'title' => sanitize($_POST['title'] ?? ''),
                'subtitle' => $_POST['subtitle'] ?? '',
                'tagline' => $_POST['tagline'] ?? '',
                'is_active' => isset($_POST['is_active']) && $_POST['is_active'] === '1',
                'updated_at' => date('c')
            ];
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload = handleFileUpload($_FILES['image'], $uploadPaths['hero'], 'hero_');
                if (!$upload['success']) {
                    echo json_encode(['success' => false, 'error' => $upload['error']]);
                    exit;
                }
                if ($upload['filename']) {
                    // Delete old file if updating
                    if ($id) {
                        $existing = $supabase->get('hero_content', 'id=eq.' . $id);
                        if ($existing['success'] && !empty($existing['data'])) {
                            deleteOldFile($existing['data'][0]['image_filename'], $uploadPaths['hero']);
                        }
                    }
                    $data['image_filename'] = $upload['filename'];
                }
            } elseif (!$id) {
                // New hero item requires image
                echo json_encode(['success' => false, 'error' => 'Image is required for hero content']);
                exit;
            }
            
            if ($id) {
                $result = $supabase->update('hero_content', 'id=eq.' . $id, $data);
            } else {
                $result = $supabase->insert('hero_content', $data);
            }
            
            echo json_encode(['success' => $result['success'], 'data' => $result['data']]);
            
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid content type']);
        }
        break;
        
    case 'DELETE':
        // Handle delete
        $input = json_decode(file_get_contents('php://input'), true);
        $table = $input['table'] ?? '';
        $id = $input['id'] ?? '';
        
        if (!$table || !$id) {
            echo json_encode(['success' => false, 'error' => 'Missing table or id']);
            exit;
        }
        
        // Get the item to delete associated file
        $existing = $supabase->get($table, 'id=eq.' . $id);
        
        if ($existing['success'] && !empty($existing['data'])) {
            $item = $existing['data'][0];
            
            // Determine upload path based on table
            $uploadPath = '';
            if ($table === 'about_content') {
                $uploadPath = $uploadPaths['about'];
            } elseif ($table === 'menu_items') {
                $uploadPath = $uploadPaths['menu'];
            } elseif ($table === 'gallery_images') {
                $uploadPath = $uploadPaths['gallery'];
            } elseif ($table === 'hero_content') {
                $uploadPath = $uploadPaths['hero'];
            }
            
            // Delete the file
            if ($uploadPath && isset($item['image_filename'])) {
                deleteOldFile($item['image_filename'], $uploadPath);
            }
        }
        
        // Delete from database
        $result = $supabase->delete($table, 'id=eq.' . $id);
        echo json_encode(['success' => $result['success']]);
        break;
        
    case 'GET':
        // Fetch content
        $type = $_GET['type'] ?? '';
        $id = $_GET['id'] ?? '';
        
        $tableMap = [
            'about' => 'about_content',
            'menu' => 'menu_items',
            'gallery' => 'gallery_images',
            'hero' => 'hero_content'
        ];
        
        if (!isset($tableMap[$type])) {
            echo json_encode(['success' => false, 'error' => 'Invalid type']);
            exit;
        }
        
        $query = $id ? 'id=eq.' . $id : 'order=display_order.asc';
        $result = $supabase->get($tableMap[$type], $query);
        echo json_encode($result);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
