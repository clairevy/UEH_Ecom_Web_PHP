<?php
/**
 * Delete Category AJAX Endpoint
 * Xóa danh mục
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
    
    $category_id = $_POST['category_id'] ?? '';
    
    if (empty($category_id)) {
        echo json_encode(['success' => false, 'message' => 'ID danh mục không hợp lệ']);
        exit;
    }
    
    $categoryModel = new CategoryModel();
    $result = $categoryModel->delete($category_id);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Xóa danh mục thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Xóa danh mục thất bại']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
