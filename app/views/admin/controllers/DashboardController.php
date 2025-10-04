<?php
/**
 * Dashboard Controller
 * Xử lý trang dashboard
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class DashboardController extends BaseController {
    private $productModel;
    private $orderModel;
    private $categoryModel;
    private $userModel;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
        $this->categoryModel = new CategoryModel();
        $this->userModel = new UserModel();
    }

    /**
     * Hiển thị dashboard
     */
    public function index() {
        try {
            // Lấy thống kê
            $orderStats = $this->orderModel->getOrderStats();
            $userStats = $this->userModel->getUserStats();
            $categories = $this->categoryModel->getCategoriesWithProductCount();
            $recentOrders = $this->orderModel->getRecentOrders(5);
            $topProducts = $this->productModel->getTopSellingProducts(3);

            $this->data = [
                'pageTitle' => 'JEWELLERY Admin - Dashboard',
                'currentPage' => 'dashboard',
                'baseUrl' => './',
                'orderStats' => $orderStats,
                'userStats' => $userStats,
                'categories' => $categories,
                'recentOrders' => $recentOrders,
                'topProducts' => $topProducts
            ];

            $this->render('dashboard');
        } catch (Exception $e) {
            $this->data = [
                'pageTitle' => 'JEWELLERY Admin - Dashboard',
                'currentPage' => 'dashboard',
                'baseUrl' => './',
                'error' => $e->getMessage()
            ];
            $this->render('dashboard');
        }
    }
}
?>
