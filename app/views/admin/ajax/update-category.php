<?php
/**
 * Update Category AJAX Endpoint
 * Cập nhật danh mục
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
    $name = $_POST['name'] ?? '';
    $slug = $_POST['slug'] ?? '';
    
    if (empty($category_id) || empty($name) || empty($slug)) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        exit;
    }
    
    $categoryModel = new CategoryModel();
    
    // Cập nhật danh mục
    $sql = "UPDATE categories SET name = :name, slug = :slug WHERE category_id = :category_id";
    $stmt = $categoryModel->conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':slug', $slug);
    $stmt->bindParam(':category_id', $category_id);
    $result = $stmt->execute();
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật danh mục thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cập nhật danh mục thất bại']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
