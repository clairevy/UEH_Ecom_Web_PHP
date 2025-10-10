<?php
/**
 * AdminService - Business Logic for Admin Operations
 * Xử lý logic chung cho admin panel
 */
class AdminService {
    private $productModel;
    private $categoryModel;
    private $orderModel;
    private $userModel;
    private $reviewModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->orderModel = new Order();
        $this->userModel = new User();
        $this->reviewModel = new Review();
    }
    
    /**
     * Lấy thống kê tổng quan cho dashboard
     */
    public function getDashboardStats() {
        return [
            'total_products' => $this->productModel->getTotalCount(),
            'total_categories' => $this->categoryModel->getTotalCount(),
            'total_orders' => $this->orderModel->getTotalCount(),
            'total_customers' => $this->userModel->getTotalCount(),
            'total_reviews' => $this->reviewModel->getTotalCount(),
            'total_revenue' => $this->orderModel->getTotalRevenue()
        ];
    }
    
    /**
     * Lấy dữ liệu cho dashboard
     */
    public function getDashboardData() {
        return [
            'stats' => $this->getDashboardStats(),
            'recentOrders' => $this->orderModel->getRecentOrders(5),
            'bestSellers' => $this->productModel->getBestSellers(3)
        ];
    }
    
    /**
     * Xử lý flash messages
     */
    public function getFlashMessage() {
        $message = '';
        if (isset($_SESSION['success'])) {
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">' . 
                      $_SESSION['success'] . 
                      '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . 
                      $_SESSION['error'] . 
                      '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['error']);
        }
        return $message;
    }
    
    /**
     * Set flash message
     */
    public function setFlashMessage($type, $message) {
        $_SESSION[$type] = $message;
    }
}
