<?php
/**
 * Delete Collection AJAX Endpoint
 * Xóa bộ sưu tập
 */

// Thiết lập
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Set JSON header
header('Content-Type: application/json');

// Kiểm tra method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Include model
    require_once __DIR__ . '/../models/CollectionModel.php';
    
    // Lấy collection_id
    $collection_id = $_POST['collection_id'] ?? null;
    
    if (!$collection_id) {
        echo json_encode(['success' => false, 'message' => 'Collection ID is required']);
        exit;
    }
    
    // Khởi tạo model
    $collectionModel = new CollectionModel();
    
    // Xóa collection
    $result = $collectionModel->deleteCollection($collection_id);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Collection deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete collection']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
