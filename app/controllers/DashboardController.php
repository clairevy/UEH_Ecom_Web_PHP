<?php
/**
 * DashboardController - Admin Dashboard
 * Hiển thị thống kê tổng quan cho admin
 */
class DashboardController extends BaseController {
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
     * Dashboard chính - hiển thị thống kê
     */
    public function index() {
        try {
            // Lấy thống kê tổng quan
            $stats = [
                'total_products' => $this->productModel->getTotalCount(),
                'total_categories' => $this->categoryModel->getTotalCount(),
                'total_orders' => $this->orderModel->getTotalCount(),
                'total_customers' => $this->userModel->getTotalCount(),
                'total_reviews' => $this->reviewModel->getTotalCount()
            ];

            // Lấy dữ liệu cho biểu đồ và bảng
            $recentOrders = $this->orderModel->getRecentOrders(5);
            $bestSellers = $this->productModel->getBestSellers(3);

            $data = [
                'title' => 'Dashboard',
                'stats' => $stats,
                'recentOrders' => $recentOrders,
                'bestSellers' => $bestSellers
            ];

            $this->view('admin/index', $data);

        } catch (Exception $e) {
            // Handle error
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }
}
