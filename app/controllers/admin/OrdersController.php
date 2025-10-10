<?php
/**
 * OrderController - Admin Order Management
 */
class OrdersController extends BaseController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = $this->model('Order');
    }

    public function index() {
        try {
            $orders = $this->orderModel->getAllOrders();

            $data = [
                'title' => 'Đơn Hàng',
                'orders' => $orders
            ];

            // Render admin page with layout
            $this->renderAdminPage('admin/pages/orders', $data);

        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $status = $_POST['status'];
                
                if ($this->orderModel->updateStatus($id, $status)) {
                    $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công!';
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật trạng thái!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        
        $this->index();
    }
}
