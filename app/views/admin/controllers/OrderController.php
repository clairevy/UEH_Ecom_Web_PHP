<?php
/**
 * Order Controller
 * Xử lý quản lý đơn hàng
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/OrderModel.php';

class OrderController extends BaseController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
    }

    /**
     * Hiển thị danh sách đơn hàng
     */
    public function index() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $orders = $this->orderModel->getOrdersWithDetails($limit, $offset);
            $totalOrders = $this->orderModel->count();
            $totalPages = ceil($totalOrders / $limit);

            $this->data = [
                'pageTitle' => 'JEWELLERY Admin - Đơn hàng',
                'currentPage' => 'orders',
                'baseUrl' => '../',
                'orders' => $orders,
                'currentPageNum' => $page,
                'totalPages' => $totalPages
            ];

            $this->render('orders/index');
        } catch (Exception $e) {
            $this->data = [
                'pageTitle' => 'JEWELLERY Admin - Đơn hàng',
                'currentPage' => 'orders',
                'baseUrl' => '../',
                'error' => $e->getMessage()
            ];
            $this->render('orders/index');
        }
    }

    /**
     * Cập nhật trạng thái đơn hàng (AJAX)
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = $_POST['order_id'] ?? null;
            $status = $_POST['status'] ?? null;
            
            if ($order_id && $status) {
                try {
                    $result = $this->orderModel->updateOrderStatus($order_id, $status);
                    $this->jsonResponse(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
                } catch (Exception $e) {
                    $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
                }
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], 400);
            }
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Method không được phép'], 405);
        }
    }
}
?>
