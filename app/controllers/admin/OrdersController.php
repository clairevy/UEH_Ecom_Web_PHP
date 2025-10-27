<?php
/**
 * OrdersController - Admin Order Management
 * Tuân thủ chuẩn MVC và OOP
 */

// Load EmailHelper để gửi email (sửa path đúng từ admin/controllers/admin → helpers)
require_once __DIR__ . '/../../../helpers/email_helper.php';

class OrdersController extends BaseController {
    private $orderModel;

    public function __construct() {
        // Dependency Injection
        $this->orderModel = $this->model('Order');
    }

    /**
     * Hiển thị danh sách đơn hàng
     * Method: GET
     */
    public function index() {
        try {
            // Lấy tất cả orders từ Model
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

    /**
     * Cập nhật trạng thái thanh toán
     * Method: POST
     * Khi payment_status = 'paid' → GỬI EMAIL XÁC NHẬN
     */
    public function updatePayment() {
        // Chỉ chấp nhận POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method!';
            $this->redirect('index.php?url=orders');
            return;
        }

        try {
            $orderId = $_GET['id'] ?? null;
            $paymentStatus = $_POST['payment_status'] ?? null;

            // Validate input
            if (!$orderId || !$paymentStatus) {
                throw new Exception('Thiếu thông tin cần thiết');
            }

            // Cập nhật payment status trong database
            $updateResult = $this->orderModel->updatePaymentStatus($orderId, $paymentStatus);

            if (!$updateResult) {
                throw new Exception('Không thể cập nhật trạng thái thanh toán');
            }

            // NẾU payment_status = 'paid' → GỬI EMAIL
            if ($paymentStatus === 'paid') {
                $this->sendPaymentConfirmationEmail($orderId);
            }

            $_SESSION['success'] = 'Cập nhật trạng thái thanh toán thành công!';
            
            // Log success để debug
            error_log("Order #$orderId payment status updated to: $paymentStatus");
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            error_log('OrdersController::updatePayment Error: ' . $e->getMessage());
        }

        // Redirect với cache busting để tránh cache browser
        $this->redirect('index.php?url=orders&t=' . time());
    }

    /**
     * Cập nhật trạng thái đơn hàng
     * Method: POST
     */
    public function updateOrder() {
        // Chỉ chấp nhận POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method!';
            $this->redirect('index.php?url=orders');
            return;
        }

        try {
            $orderId = $_GET['id'] ?? null;
            $orderStatus = $_POST['order_status'] ?? null;

            // Validate input
            if (!$orderId || !$orderStatus) {
                throw new Exception('Thiếu thông tin cần thiết');
            }

            // Cập nhật order status trong database
            $updateResult = $this->orderModel->updateStatus($orderId, $orderStatus);

            if (!$updateResult) {
                throw new Exception('Không thể cập nhật trạng thái đơn hàng');
            }

            $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công!';
            
            // Log success
            error_log("Order #$orderId order status updated to: $orderStatus");
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            error_log('OrdersController::updateOrder Error: ' . $e->getMessage());
        }

        // Redirect với cache busting
        $this->redirect('index.php?url=orders&t=' . time());
    }

    /**
     * Hiển thị chi tiết đơn hàng
     * Method: GET
     * Hỗ trợ cả VIEW mode và EDIT mode (inline edit với dropdowns)
     */
    public function showDetails() {
        try {
            $orderId = $_GET['id'] ?? null;
            
            if (!$orderId) {
                throw new Exception('Không tìm thấy ID đơn hàng');
            }

            // Lấy thông tin đơn hàng từ Model
            $order = $this->orderModel->getOrderDetails($orderId);
            
            if (!$order) {
                throw new Exception('Đơn hàng không tồn tại');
            }

            // Lấy danh sách items trong đơn hàng
            $orderItems = $this->orderModel->getOrderItems($orderId);
            
            // Tính toán totals (Private helper method - OOP)
            $calculations = $this->calculateOrderTotals($orderItems, $order);
            
            // Prepare data cho View
            $data = [
                'title' => 'Chi Tiết Đơn Hàng',
                'pageTitle' => 'Chi Tiết Đơn Hàng #' . $orderId,
                'breadcrumb' => 'Home > Đơn Hàng > Chi Tiết',
                'order' => $order,
                'orderItems' => $orderItems,
                'calculations' => $calculations,
                'editMode' => true  // Admin luôn có thể edit (inline via dropdowns)
            ];

            // Render admin page with layout
            $this->renderAdminPage('admin/pages/order-details', $data);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            error_log('OrdersController::showDetails Error: ' . $e->getMessage());
            $this->redirect('index.php?url=orders');
        }
    }

    /**
     * Legacy method - giữ lại để tương thích ngược
     */
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
        
        $this->redirect('index.php?url=orders');
    }
    
    /**
     * Xóa đơn hàng (soft delete)
     */
    public function delete() {
        try {
            $orderId = $_GET['id'] ?? null;
            
            if (!$orderId) {
                throw new Exception('Không tìm thấy ID đơn hàng');
            }

            if ($this->orderModel->deleteById($orderId)) {
                $_SESSION['success'] = 'Xóa đơn hàng thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa đơn hàng!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        $this->redirect('index.php?url=orders');
    }

    // =================== PRIVATE HELPER METHODS (OOP Best Practice) ===================

    /**
     * Tính toán totals cho đơn hàng
     * @param array $orderItems
     * @param object $order
     * @return array
     */
    private function calculateOrderTotals($orderItems, $order) {
        $subtotal = 0;
        
        // Tính subtotal từ order items
        foreach ($orderItems as $item) {
            $subtotal += $item->total_price ?? ($item->quantity * $item->unit_price_snapshot);
        }
        
        // Lấy shipping fee và discount từ order
        $shippingFee = $order->shipping_fee ?? 0;
        $discountAmount = $order->discount_amount ?? 0;
        
        // Tính total
        $total = $subtotal + $shippingFee - $discountAmount;
        
        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discountAmount,
            'discount_code' => $order->discount_code ?? null,
            'total' => $total
        ];
    }

    /**
     * Gửi email xác nhận thanh toán cho khách hàng
     * @param int $orderId
     * @return void
     */
    private function sendPaymentConfirmationEmail($orderId) {
        try {
            // Lấy thông tin đơn hàng kèm email khách hàng
            $order = $this->orderModel->getOrderWithCustomerEmail($orderId);

            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            // Kiểm tra có email không
            $customerEmail = $order->customer_email ?? $order->email;
            if (!$customerEmail) {
                throw new Exception('Không tìm thấy email khách hàng');
            }

            // Gửi email xác nhận thanh toán
            $emailSent = EmailHelper::sendPaymentConfirmationEmail($order);

            if ($emailSent) {
                // Log thành công
                error_log("Payment confirmation email sent to: $customerEmail for order #$orderId");
                
                // Thêm thông báo cho admin
                $_SESSION['email_status'] = 'Email xác nhận đã được gửi đến khách hàng';
            } else {
                throw new Exception('Không thể gửi email xác nhận');
            }

        } catch (Exception $e) {
            // Log lỗi nhưng không dừng quá trình cập nhật
            error_log('Email sending error: ' . $e->getMessage());
            $_SESSION['email_warning'] = 'Cảnh báo: Không thể gửi email xác nhận - ' . $e->getMessage();
        }
    }

    /**
     * Redirect helper method
     */
    private function redirect($url) {
        header("Location: $url");
        exit;
    }
}
