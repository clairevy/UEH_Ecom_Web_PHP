<?php
/**
 * Update Collection AJAX Endpoint
 * Cập nhật bộ sưu tập
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
    
    // Lấy dữ liệu
    $collection_id = $_POST['collection_id'] ?? null;
    $name = $_POST['name'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $description = $_POST['description'] ?? '';
    $cover_image = $_POST['cover_image'] ?? '';
    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
    
    // Validation
    if (!$collection_id) {
        echo json_encode(['success' => false, 'message' => 'Collection ID is required']);
        exit;
    }
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Tên bộ sưu tập không được để trống']);
        exit;
    }
    
    if (empty($slug)) {
        echo json_encode(['success' => false, 'message' => 'Slug không được để trống']);
        exit;
    }
    
    // Khởi tạo model
    $collectionModel = new CollectionModel();
    
    // Kiểm tra slug đã tồn tại chưa (trừ collection hiện tại)
    $existingCollection = $collectionModel->getBySlug($slug);
    if ($existingCollection && $existingCollection['collection_id'] != $collection_id) {
        echo json_encode(['success' => false, 'message' => 'Slug đã tồn tại']);
        exit;
    }
    
    // Cập nhật collection
    $data = [
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'cover_image' => $cover_image,
        'is_active' => $is_active
    ];
    
    $result = $collectionModel->updateCollection($collection_id, $data);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật bộ sưu tập thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật bộ sưu tập']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>
