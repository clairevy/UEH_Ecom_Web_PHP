<?php
/**
 * Approve Review AJAX Endpoint
 * Duyệt review
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
    require_once __DIR__ . '/../models/ReviewModel.php';
    
    // Lấy review_id
    $review_id = $_POST['review_id'] ?? null;
    
    if (!$review_id) {
        echo json_encode(['success' => false, 'message' => 'Review ID is required']);
        exit;
    }
    
    // Khởi tạo model
    $reviewModel = new ReviewModel();
    
    // Duyệt review
    $result = $reviewModel->approveReview($review_id);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Review approved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to approve review']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
