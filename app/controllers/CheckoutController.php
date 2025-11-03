<?php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../../helpers/session_helper.php';

class CheckoutController extends BaseController {
    private $productModel;
    private $orderModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->orderModel = new Order();
        SessionHelper::start();
    }

    /**
     * Show checkout page
     */
    public function index() {
        $cartItems = [];
        $cartSummary = [];
        
        // Check if this is buy now checkout
        if (isset($_SESSION['buy_now_item'])) {
            $cartItems = [$this->getBuyNowItem()];
            $cartSummary = $this->calculateBuyNowSummary($_SESSION['buy_now_item']);
        } else {
            // Regular cart checkout
            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                header('Location: /Ecom_website/cart');
                exit;
            }
            $cartItems = $this->getCartItems();
            $cartSummary = $this->calculateCartSummary($cartItems);
        }

        // Get user info if logged in
        $userInfo = null;
        if (SessionHelper::isLoggedIn()) {
            $userInfo = SessionHelper::getUser();
        }

        $this->view('customer/pages/checkout', [
            'title' => 'Thanh toán',
            'cartItems' => $cartItems,
            'cartSummary' => $cartSummary,
            'userInfo' => $userInfo
        ]);
    }

    /**
     * Process checkout
     */
    public function process() {
        try {
            // Validate cart or buy now item
            if (!isset($_SESSION['cart']) && !isset($_SESSION['buy_now_item'])) {
                $this->jsonResponse(false, 'Không có sản phẩm để thanh toán');
                return;
            }

            if (!isset($_SESSION['buy_now_item']) && (empty($_SESSION['cart']))) {
                $this->jsonResponse(false, 'Giỏ hàng trống');
                return;
            }

            // Validate form data
            $errors = $this->validateCheckoutData($_POST);
            if (!empty($errors)) {
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ', $errors);
                return;
            }

            // Get customer info
            $customerInfo = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone' => trim($_POST['phone']),
                'address' => trim($_POST['address']),
                'province' => trim($_POST['province']),
                'notes' => trim($_POST['notes'] ?? '')
            ];

            // Get payment method
            $paymentMethod = $_POST['payment_method'] ?? 'cod';

            // Calculate order totals
            if (isset($_SESSION['buy_now_item'])) {
                // Buy now checkout
                $cartItems = [$this->getBuyNowItem()];
                $cartSummary = $this->calculateBuyNowSummary($_SESSION['buy_now_item']);
            } else {
                // Regular cart checkout
                $cartItems = $this->getCartItems();
                $cartSummary = $this->calculateCartSummary($cartItems);
            }

            // Validate stock availability
            foreach ($cartItems as $item) {
                if ($item['quantity'] > $item['product']->stock_quantity) {
                    $this->jsonResponse(false, "Sản phẩm '{$item['product']->name}' không đủ số lượng trong kho");
                    return;
                }
            }

            // Create order (pass items and payment method to model which will handle items & stock)
            $itemsForOrder = [];
            foreach ($cartItems as $item) {
                $itemsForOrder[] = [
                    'product_id' => $item['product_id'] ?? ($item['product']->product_id ?? null),
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? null,
                    'color' => $item['color'] ?? null,
                    'unit_price' => $item['price'] ?? ($item['product']->base_price ?? 0)
                ];
            }

            $orderData = [
                'user_id' => SessionHelper::isLoggedIn() ? SessionHelper::getUserId() : null,
                'full_name' => $customerInfo['name'],
                'email' => $customerInfo['email'],
                'phone' => $customerInfo['phone'],
                'street' => $customerInfo['address'],
                'ward' => '',
                'province' => $customerInfo['province'],
                'country' => 'Vietnam',
                'payment_method' => $paymentMethod,
                'order_status' => 'pending',
                'shipping_fee' => $cartSummary['shipping'],
                'total_amount' => $cartSummary['total'],
                'discount_code' => null,
                'discount_amount' => 0,
                'items' => $itemsForOrder
            ];

            $orderId = $this->orderModel->createOrder($orderData);

            if ($orderId) {
                // Clear appropriate session data after successful order
                if (isset($_SESSION['buy_now_item'])) {
                    unset($_SESSION['buy_now_item']);
                } else {
                    unset($_SESSION['cart']);
                }

                $this->jsonResponse(true, 'Đặt hàng thành công!', [
                    'order_id' => $orderId,
                    'redirect' => '/Ecom_website/order-success/' . $orderId
                ]);
            } else {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi tạo đơn hàng');
            }

        } catch (Exception $e) {
            error_log("Checkout Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Show order success page
     */
    public function orderSuccess() {
        $orderId = $_GET['order_id'] ?? null;
        
        if (!$orderId) {
            header('Location: /Ecom_website/');
            exit;
        }

        // Get order details (simplified - in real app would fetch from database)
        $this->view('customer/pages/order-success', [
            'title' => 'Đặt hàng thành công',
            'order_id' => $orderId
        ]);
    }

    /**
     * Create order in database
     */
    private function createOrder($customerInfo, $cartItems, $cartSummary, $paymentMethod) {
        try {
            // Generate order ID
            $orderId = 'ORD' . date('Ymd') . rand(1000, 9999);

            // Prepare order data to match database structure
            $orderData = [
                'user_id' => SessionHelper::isLoggedIn() ? SessionHelper::getUserId() : null,
                'full_name' => $customerInfo['name'],
                'email' => $customerInfo['email'],
                'phone' => $customerInfo['phone'],
                'street' => $customerInfo['address'], // For now, put full address in street
                'ward' => '', // Would need separate fields in form
                'province' => $customerInfo['province'],
                'country' => 'Vietnam',
                'order_status' => 'pending',
                'shipping_fee' => $cartSummary['shipping'],
                'total_amount' => $cartSummary['total'],
                'discount_code' => null,
                'discount_amount' => 0
            ];

            // Create order using proper database method
            error_log("Creating order with data: " . json_encode($orderData));
            $created = $this->orderModel->createOrder($orderData);
            error_log("Order creation result: " . ($created ? 'SUCCESS' : 'FAILED'));

            if ($created) {
                // Get the actual order ID from database (auto-increment primary key)
                $actualOrderId = $this->orderModel->getLastInsertId();
                error_log("Actual order ID: " . $actualOrderId);
                
                // Create order items
                foreach ($cartItems as $item) {
                    $itemData = [
                        'order_id' => $actualOrderId,
                        'product_id' => $item['product']->product_id,
                        'variant_id' => null, // Set if using variants
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['product']->base_price,
                        'total_price' => $item['subtotal'] ?? ($item['quantity'] * $item['product']->base_price)
                    ];
                    
                    error_log("Creating order item: " . json_encode($itemData));
                    $itemCreated = $this->orderModel->addOrderItem($itemData);
                    error_log("Order item creation result: " . ($itemCreated ? 'SUCCESS' : 'FAILED'));
                }

                // Update product stock (simplified)
                foreach ($cartItems as $item) {
                    $newStock = $item['product']->stock_quantity - $item['quantity'];
                    // In real app: $this->productModel->updateStock($item['product']->product_id, $newStock);
                }

                return $actualOrderId;
            }

            return false;

        } catch (Exception $e) {
            error_log("Create Order Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate checkout form data
     */
    private function validateCheckoutData($data) {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Họ tên là bắt buộc';
        }

        if (empty($data['email'])) {
            $errors['email'] = 'Email là bắt buộc';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }

        if (empty($data['phone'])) {
            $errors['phone'] = 'Số điện thoại là bắt buộc';
        } elseif (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        }

        if (empty($data['address'])) {
            $errors['address'] = 'Địa chỉ giao hàng là bắt buộc';
        }

        if (empty($data['province'])) {
            $errors['province'] = 'Tỉnh/Thành phố là bắt buộc';
        }

    $allowedPaymentMethods = ['cod', 'bank_transfer', 'momo', 'vnpay', 'qr_code'];
        if (empty($data['payment_method']) || !in_array($data['payment_method'], $allowedPaymentMethods)) {
            $errors['payment_method'] = 'Phương thức thanh toán không hợp lệ';
        }

        return $errors;
    }

    /**
     * Get cart items with product details
     */
    private function getCartItems() {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return [];
        }

        $cartItems = [];
        foreach ($_SESSION['cart'] as $cartKey => $cartItem) {
            $product = $this->productModel->findById($cartItem['product_id']);
            if ($product) {
                $cartItems[] = [
                    'cart_key' => $cartKey,
                    'product' => $product,
                    'quantity' => $cartItem['quantity'],
                    'size' => $cartItem['size'],
                    'color' => $cartItem['color'],
                    'subtotal' => $product->base_price * $cartItem['quantity']
                ];
            }
        }

        return $cartItems;
    }

    /**
     * Calculate cart summary
     */
    private function calculateCartSummary($cartItems) {
        $subtotal = 0;
        $totalQuantity = 0;

        foreach ($cartItems as $item) {
            $subtotal += $item['subtotal'];
            $totalQuantity += $item['quantity'];
        }

        $shipping = $subtotal >= 500000 ? 0 : 30000; // Free shipping over 500k VND
        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $shipping + $tax;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'totalQuantity' => $totalQuantity
        ];
    }

    /**
     * Get buy now item formatted for checkout
     */
    private function getBuyNowItem() {
        $buyNowData = $_SESSION['buy_now_item'];
        $product = $buyNowData['product'];
        
        return [
            'product_id' => $buyNowData['product_id'],
            'product' => $product,
            'quantity' => $buyNowData['quantity'],
            'price' => $buyNowData['price'],
            'size' => $buyNowData['size'],
            'color' => $buyNowData['color'],
            'subtotal' => $buyNowData['price'] * $buyNowData['quantity']
        ];
    }

    /**
     * Calculate buy now summary
     */
    private function calculateBuyNowSummary($buyNowItem) {
        $subtotal = $buyNowItem['price'] * $buyNowItem['quantity'];
        $shipping = $subtotal >= 500000 ? 0 : 30000; // Free shipping over 500k VND
        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $shipping + $tax;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'totalQuantity' => $buyNowItem['quantity']
        ];
    }

    /**
     * Send JSON response
     */
    // private function jsonResponse($success, $message, $data = null) {
    //     header('Content-Type: application/json');
    //     $response = [
    //         'success' => $success,
    //         'message' => $message
    //     ];
        
    //     if ($data !== null) {
    //         $response['data'] = $data;
    //     }
        
    //     echo json_encode($response, JSON_UNESCAPED_UNICODE);
    //     exit;
    // }
}
?>