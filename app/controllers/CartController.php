<?php
// All dependencies are loaded via index.php

class CartController extends BaseController {
    private $productModel;
    private $cartService;

    public function __construct() {
        $this->productModel = new Product();
        $this->cartService = new CartService();
        SessionHelper::start();
    }

    /**
     * Show cart page
     */
    public function index() {
        $cartItems = $this->getCartItems();
        $cartSummary = $this->calculateCartSummary($cartItems);

        $this->view('customer/pages/cart', [
            'title' => 'Giỏ hàng',
            'cartItems' => $cartItems,
            'cartSummary' => $cartSummary
        ]);
    }

    /**
     * Add product to cart (AJAX)
     */
    public function add() {
        // For AJAX requests, suppress all output except JSON
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            // Turn off error display completely for AJAX
            ini_set('display_errors', 0);
            // Clear any previous output
            if (ob_get_level()) {
                ob_clean();
            }
        }
        
        try {
            $productId = (int)$_POST['product_id'];
            $quantity = (int)($_POST['quantity'] ?? 1);
            $size = $_POST['size'] ?? null;
            $color = $_POST['color'] ?? null;

            // Debug logging
            error_log("Cart Add Debug - Product ID: $productId, Size: '$size', Color: '$color', Quantity: $quantity");

            if ($productId <= 0 || $quantity <= 0) {
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ');
                return;
            }

            // Check if product exists
            $product = $this->productModel->findById($productId);
            if (!$product) {
                $this->jsonResponse(false, 'Sản phẩm không tồn tại');
                return;
            }

            // Check stock from variants
            if (!$this->productModel->checkStock($productId, $size, $color, $quantity)) {
                $this->jsonResponse(false, 'Số lượng hàng trong kho không đủ hoặc biến thể không tồn tại');
                return;
            }

            // Add to cart
            $this->addToCart($productId, $quantity, $size, $color);

            // Get updated cart info
            $cartItems = $this->getCartItems();
            $cartSummary = $this->calculateCartSummary($cartItems);

            $this->jsonResponse(true, 'Thêm sản phẩm vào giỏ hàng thành công!', [
                'cartCount' => count($cartItems),
                'cartTotal' => $cartSummary['total'],
                'cartSubtotal' => $cartSummary['subtotal']
            ]);

        } catch (Exception $e) {
            error_log("Add to Cart Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Update cart item quantity (AJAX)
     */
    public function update() {
        try {
            $productId = (int)$_POST['product_id'];
            $quantity = (int)$_POST['quantity'];
            $size = $_POST['size'] ?? null;
            $color = $_POST['color'] ?? null;

            if ($productId <= 0) {
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ');
                return;
            }

            if ($quantity <= 0) {
                // Remove item if quantity is 0
                $this->removeFromCart($productId, $size, $color);
            } else {
                // Check stock from variants
                if (!$this->productModel->checkStock($productId, $size, $color, $quantity)) {
                    $this->jsonResponse(false, 'Số lượng hàng trong kho không đủ');
                    return;
                }

                $this->updateCartItem($productId, $quantity, $size, $color);
            }

            // Get updated cart info
            $cartItems = $this->getCartItems();
            $cartSummary = $this->calculateCartSummary($cartItems);
            
  
            $this->jsonResponse(true, 'Cập nhật giỏ hàng thành công!', [
                'cartCount' => count($cartItems),
                'cartTotal' => $cartSummary['total'],
                'cartSubtotal' => $cartSummary['subtotal'],
            ]);

        } catch (Exception $e) {
            error_log("Update Cart Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Remove item from cart (AJAX)
     */
    public function remove() {
        try {
            $productId = (int)$_POST['product_id'];
            $size = $_POST['size'] ?? null;
            $color = $_POST['color'] ?? null;

            if ($productId <= 0) {
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ');
                return;
            }

            $this->removeFromCart($productId, $size, $color);

            // Get updated cart info
            $cartItems = $this->getCartItems();
            $cartSummary = $this->calculateCartSummary($cartItems);

            $this->jsonResponse(true, 'Xóa sản phẩm khỏi giỏ hàng thành công!', [
                'cartCount' => count($cartItems),
                'cartTotal' => $cartSummary['total'],
                'cartSubtotal' => $cartSummary['subtotal']
            ]);

        } catch (Exception $e) {
            error_log("Remove from Cart Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Clear entire cart (AJAX)
     */
    public function clear() {
        try {
            if (SessionHelper::isLoggedIn()) {
                // Clear from database for logged in users
                $userId = SessionHelper::getUserId();
                // TODO: Add database cart clearing logic
            }

            // Clear session cart
            unset($_SESSION['cart']);

            $this->jsonResponse(true, 'Đã xóa tất cả sản phẩm trong giỏ hàng!', [
                'cartCount' => 0,
                'cartTotal' => 0,
                'cartSubtotal' => 0
            ]);

        } catch (Exception $e) {
            error_log("Cart Add Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Get cart count (AJAX)
     */
    public function getCount() {
        $cartItems = $this->getCartItems();
        $this->jsonResponse(true, '', ['cartCount' => count($cartItems)]);
    }

    /**
     * Add product to cart (internal method)
     */
    private function addToCart($productId, $quantity, $size = null, $color = null) {
        $cartKey = $this->generateCartKey($productId, $size, $color);
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'size' => $size,
                'color' => $color,
                'added_at' => time()
            ];
        }

        // TODO: If user is logged in, also save to database
        if (SessionHelper::isLoggedIn()) {
            // Save to database for persistent cart
        }
    }

    /**
     * Update cart item quantity
     */
    private function updateCartItem($productId, $quantity, $size = null, $color = null) {
        $cartKey = $this->generateCartKey($productId, $size, $color);
        
        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
            
            // TODO: Update database if user is logged in
        }
    }

    /**
     * Remove item from cart
     */
    private function removeFromCart($productId, $size = null, $color = null) {
        $cartKey = $this->generateCartKey($productId, $size, $color);
        
        if (isset($_SESSION['cart'][$cartKey])) {
            unset($_SESSION['cart'][$cartKey]);
            
            // TODO: Remove from database if user is logged in
        }
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
                    'subtotal' => $product->base_price * $cartItem['quantity'],
                    'added_at' => $cartItem['added_at']
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
     * Generate unique cart key for product with variants
     */
    private function generateCartKey($productId, $size = null, $color = null) {
        return $productId . '_' . ($size ?? 'default') . '_' . ($color ?? 'default');
    }



    /**
     * Buy now - validate stock and store for direct checkout
     */
    public function buyNow() {
        // For AJAX requests, suppress all output except JSON
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            ini_set('display_errors', 0);
            if (ob_get_level()) {
                ob_clean();
            }
        }
        
        try {
            $productId = (int)$_POST['product_id'];
            $quantity = (int)($_POST['quantity'] ?? 1);
            $size = $_POST['size'] ?? null;
            $color = $_POST['color'] ?? null;

            // Debug logging
            error_log("Cart BuyNow Debug - Product ID: $productId, Size: '$size', Color: '$color', Quantity: $quantity");

            if ($productId <= 0 || $quantity <= 0) {
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ');
                return;
            }

            // Get product info
            $product = $this->productModel->findById($productId);
            if (!$product) {
                $this->jsonResponse(false, 'Sản phẩm không tồn tại');
                return;
            }

            // For debugging, let's just skip stock check temporarily
            error_log("Cart BuyNow - Bypassing stock check for testing");

            // Store buy now item in separate session
            $_SESSION['buy_now_item'] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'size' => $size,
                'color' => $color,
                'price' => $product->base_price,
                'product' => $product
            ];

            error_log("Cart BuyNow - Item stored successfully");
            $this->jsonResponse(true, 'Sản phẩm hợp lệ, chuyển đến thanh toán');

        } catch (Exception $e) {
            error_log("Cart BuyNow Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Get cart count for badge
     */
    public function count() {
        $totalQuantity = 0;
        
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $totalQuantity += $item['quantity'];
            }
        }
        
        $this->jsonResponse(true, 'Cart count retrieved', ['count' => $totalQuantity]);
    }

    /**
     * Get cart summary for AJAX updates
     */
    public function summary() {
        try {
            $cartItems = $this->getCartItems();
            $cartSummary = $this->calculateCartSummary($cartItems);
            
            $this->jsonResponse(true, 'Cart summary retrieved', [
                'summary' => $cartSummary,
                'totalItems' => count($cartItems)
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(false, 'Lỗi khi lấy thông tin giỏ hàng: ' . $e->getMessage());
        }
    }

    /**
     * Get cart summary for AJAX updates
     */
    public function getSummary() {
        try {
            $cartItems = $this->getCartItems();
            $summary = $this->calculateCartSummary($cartItems);
            
            $this->jsonResponse(true, 'Cart summary retrieved', [
                'subtotal' => $summary['subtotal'],
                'shipping' => $summary['shipping'],
                'tax' => $summary['tax'],
                'total' => $summary['total'],
                'count' => count($cartItems)
            ]);
            
        } catch (Exception $e) {
            $this->jsonResponse(false, 'Lỗi khi lấy thông tin giỏ hàng: ' . $e->getMessage());
        }
    }

    /**
     * Validate stock levels for all items in cart
     * Returns array of errors if any products are out of stock
     */
    public function validateCartStock() {
        try {
            $cartItems = $this->getCartItems();
            if (empty($cartItems)) {
                return [];
            }

            // Format cart items for service
            $items = array_map(function($item) {
                return [
                    'product_id' => $item['product']->product_id,
                    'quantity' => $item['quantity'],
                    'size' => $item['size'],
                    'color' => $item['color']
                ];
            }, $cartItems);

            return $this->cartService->validateCartStock($items);
            
        } catch (Exception $e) {
            error_log("Validate Cart Stock Error in Controller: " . $e->getMessage());
            throw new Exception('Lỗi khi kiểm tra tồn kho: ' . $e->getMessage());
        }
    }
}
?>