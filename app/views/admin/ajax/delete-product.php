<?php
/**
 * Delete Product AJAX Endpoint
 * Xóa sản phẩm
 */

require_once __DIR__ . '/../controllers/ProductController.php';

// Chỉ cho phép POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method không được phép']);
    exit;
}

try {
    $controller = new ProductController();
    $controller->delete();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
