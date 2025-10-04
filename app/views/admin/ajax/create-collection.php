<?php
/**
 * Create Collection AJAX Endpoint
 * Tạo bộ sưu tập mới
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
    $name = $_POST['name'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $description = $_POST['description'] ?? '';
    $cover_image = $_POST['cover_image'] ?? '';
    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
    $created_by = 1; // Admin user ID - có thể lấy từ session
    
    // Validation
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
    
    // Kiểm tra slug đã tồn tại chưa
    $existingCollection = $collectionModel->getBySlug($slug);
    if ($existingCollection) {
        echo json_encode(['success' => false, 'message' => 'Slug đã tồn tại']);
        exit;
    }
    
    // Tạo collection
    $data = [
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
        'cover_image' => $cover_image,
        'is_active' => $is_active,
        'created_by' => $created_by
    ];
    
    $collectionId = $collectionModel->createCollection($data);
    
    if ($collectionId) {
        echo json_encode(['success' => true, 'message' => 'Tạo bộ sưu tập thành công', 'collection_id' => $collectionId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể tạo bộ sưu tập']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>
