<?php
/**
 * Create Category AJAX Endpoint
 * Tạo danh mục mới
 */

// Thiết lập
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Chỉ cho phép POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method không được phép']);
    exit;
}

try {
    require_once __DIR__ . '/../models/CategoryModel.php';
    
    $name = $_POST['name'] ?? '';
    $slug = $_POST['slug'] ?? '';
    
    if (empty($name) || empty($slug)) {
        echo json_encode(['success' => false, 'message' => 'Tên và slug không được để trống']);
        exit;
    }
    
    $categoryModel = new CategoryModel();
    $result = $categoryModel->createCategory($name, $slug);
    
    echo json_encode(['success' => true, 'message' => 'Tạo danh mục thành công', 'data' => $result]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
