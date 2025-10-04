<?php
/**
 * Update Order Status AJAX Endpoint
 * Cập nhật trạng thái đơn hàng
 */

require_once __DIR__ . '/../controllers/OrderController.php';

// Chỉ cho phép POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method không được phép']);
    exit;
}

try {
    $controller = new OrderController();
    $controller->updateStatus();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
