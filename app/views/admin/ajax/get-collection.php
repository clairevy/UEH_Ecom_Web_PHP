<?php
/**
 * Get Collection AJAX Endpoint
 * Lấy thông tin bộ sưu tập
 */

// Thiết lập
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Set JSON header
header('Content-Type: application/json');

try {
    // Include model
    require_once __DIR__ . '/../models/CollectionModel.php';
    
    // Lấy collection_id
    $collection_id = $_GET['id'] ?? null;
    
    if (!$collection_id) {
        echo json_encode(['success' => false, 'message' => 'Collection ID is required']);
        exit;
    }
    
    // Khởi tạo model
    $collectionModel = new CollectionModel();
    
    // Lấy thông tin collection
    $collection = $collectionModel->getById($collection_id);
    
    if ($collection) {
        // Lấy sản phẩm trong collection
        $products = $collectionModel->getCollectionProducts($collection_id);
        $collection['products'] = $products;
        
        echo json_encode(['success' => true, 'data' => $collection]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Collection not found']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
