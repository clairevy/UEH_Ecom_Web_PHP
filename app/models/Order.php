<?php

class Order extends BaseModel {
    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    public function createOrder($data) {
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Create cart service for stock validation
            $cartService = new CartService();
            
            // Validate order items stock
            $stockErrors = $cartService->validateCartStock($data['items']);
            if (!empty($stockErrors)) {
                throw new Exception(implode("\n", $stockErrors));
            }

        // Create order (include payment_method column)
        $this->db->query("INSERT INTO " . $this->table . " 
                         (user_id, full_name, email, phone, street, ward, province, country, 
                          order_status, shipping_fee, total_amount, discount_code, discount_amount, notes, created_at, updated_at) 
                         VALUES (:user_id, :full_name, :email, :phone, :street, :ward, :province, :country, 
                                 :order_status, :shipping_fee, :total_amount, :discount_code, :discount_amount, :notes, NOW(), NOW())");
        
        $this->db->bind(':user_id', $data['user_id'] ?? null);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':street', $data['street'] ?? null);
        $this->db->bind(':ward', $data['ward'] ?? null);
        $this->db->bind(':province', $data['province'] ?? null);
        $this->db->bind(':country', $data['country'] ?? 'Vietnam');
        $this->db->bind(':order_status', $data['order_status'] ?? 'pending');
        $this->db->bind(':shipping_fee', $data['shipping_fee'] ?? 0);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':discount_code', $data['discount_code'] ?? null);
        $this->db->bind(':discount_amount', $data['discount_amount'] ?? 0);
        $this->db->bind(':notes', $data['notes'] ?? null);
        
        return $this->db->execute();
    }

    /**
     * Get last inserted order ID
     */
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    /**
     * Create new order (simplified version for checkout)
     */
    // public function create($data) {
    //     try {
    //         $this->db->query("INSERT INTO " . $this->table . " 
    //                          (order_id, user_id, customer_name, customer_email, customer_phone,
    //                           shipping_address, payment_method, order_status, subtotal,
    //                           shipping_fee, tax_amount, total_amount, notes, created_at) 
    //                          VALUES (:order_id, :user_id, :customer_name, :customer_email, :customer_phone,
    //                                  :shipping_address, :payment_method, :order_status, :subtotal,
    //                                  :shipping_fee, :tax_amount, :total_amount, :notes, :created_at)");

    //         $this->db->bind(':order_id', $data['order_id']);
    //         $this->db->bind(':user_id', $data['user_id']);
    //         $this->db->bind(':customer_name', $data['customer_name']);
    //         $this->db->bind(':customer_email', $data['customer_email']);
    //         $this->db->bind(':customer_phone', $data['customer_phone']);
    //         $this->db->bind(':shipping_address', $data['shipping_address']);
    //         $this->db->bind(':payment_method', $data['payment_method']);
    //         $this->db->bind(':order_status', $data['order_status']);
    //         $this->db->bind(':subtotal', $data['subtotal']);
    //         $this->db->bind(':shipping_fee', $data['shipping_fee']);
    //         $this->db->bind(':tax_amount', $data['tax_amount']);
    //         $this->db->bind(':total_amount', $data['total_amount']);
    //         $this->db->bind(':notes', $data['notes']);
    //         $this->db->bind(':created_at', $data['created_at']);

    //         return $this->db->execute();

    //     } catch (Exception $e) {
    //         error_log("Create Order Error: " . $e->getMessage());
    //         return false;
    //     }
    // }

    /**
     * Find order by order ID
     */
    public function findByOrderId($orderId) {
        try {
            $this->db->query("SELECT * FROM " . $this->table . " WHERE order_id = :order_id");
            $this->db->bind(':order_id', $orderId);
            
            return $this->db->single();

        } catch (Exception $e) {
            error_log("Find Order Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateOrderStatus($id, $status) {
        $validStatuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $this->db->query("UPDATE " . $this->table . " SET order_status = :status, updated_at = NOW() WHERE order_id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getOrdersByUserId($userId) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY created_at DESC");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    public function findByStatus($status) {
        $this->db->query("SELECT o.*, u.name as user_name FROM " . $this->table . " o 
                         LEFT JOIN users u ON o.user_id = u.user_id 
                         WHERE o.order_status = :status 
                         ORDER BY o.created_at DESC");
        $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }

    public function deleteById($id) {
        // Soft delete by setting status to cancelled
        $this->db->query("UPDATE " . $this->table . " SET order_status = 'cancelled', updated_at = NOW() WHERE order_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function countOrders() {
        $this->db->query("SELECT COUNT(*) as total FROM " . $this->table);
        $result = $this->db->single();
        return $result->total;
    }

    public function countUserOrders($userId) {
        $this->db->query("SELECT COUNT(*) as total FROM " . $this->table . " WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        $result = $this->db->single();
        return $result->total;
    }


    public function getOrderDetails($orderId) {
        $this->db->query("SELECT o.*, u.name as user_name, u.email as user_email 
                         FROM " . $this->table . " o 
                         LEFT JOIN users u ON o.user_id = u.user_id 
                         WHERE o.order_id = :id");
        $this->db->bind(':id', $orderId);
        return $this->db->single();
    }

    // Get order items
    public function getOrderItems($orderId) {
        $this->db->query("SELECT oi.*, p.name as product_name, p.base_price, pv.size AS variant_size, pv.color AS variant_color, pv.price AS variant_price 
                         FROM order_items oi 
                         JOIN products p ON oi.product_id = p.product_id 
                         LEFT JOIN product_variants pv ON oi.variant_id = pv.variant_id 
                         WHERE oi.order_id = :order_id");
        $this->db->bind(':order_id', $orderId);
        $items = $this->db->resultSet();

        // Normalize variant display name for views (keep presentation data out of controllers)
        if (!empty($items)) {
            foreach ($items as $idx => $it) {
                $variantParts = [];
                if (!empty($it->variant_size)) {
                    $variantParts[] = 'Size: ' . $it->variant_size;
                }
                if (!empty($it->variant_color)) {
                    $variantParts[] = 'Color: ' . $it->variant_color;
                }

                $it->variant_name = !empty($variantParts) ? implode(' / ', $variantParts) : null;
                // Provide a unit_price_snapshot fallback if missing
                if (empty($it->unit_price_snapshot) && !empty($it->variant_price)) {
                    $it->unit_price_snapshot = $it->variant_price;
                }

                $items[$idx] = $it;
            }
        }

        return $items;
    }

    // Add item to order
    public function addOrderItem($orderData) {
        $this->db->query("INSERT INTO order_items 
                         (order_id, product_id, variant_id, quantity, unit_price_snapshot, total_price) 
                         VALUES (:order_id, :product_id, :variant_id, :quantity, :unit_price_snapshot, :total_price)");
        
        $this->db->bind(':order_id', $orderData['order_id']);
        $this->db->bind(':product_id', $orderData['product_id']);
        $this->db->bind(':variant_id', $orderData['variant_id'] ?? null);
        $this->db->bind(':quantity', $orderData['quantity']);


        $this->db->bind(':unit_price_snapshot', $orderData['unit_price']);

        $this->db->bind(':total_price', $orderData['total_price']);
        
        return $this->db->execute();
    }

    /**
     * Create payment record for order
     */
    public function createPayment($paymentData) {
        try {
            $this->db->query("INSERT INTO payments 
                             (order_id, payment_method, payment_status, amount, created_at) 
                             VALUES (:order_id, :payment_method, :payment_status, :amount, NOW())");
            
            $this->db->bind(':order_id', $paymentData['order_id']);
            $this->db->bind(':payment_method', $paymentData['payment_method']);
            $this->db->bind(':payment_status', $paymentData['payment_status']);
            $this->db->bind(':amount', $paymentData['amount']);
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Create Payment Error: " . $e->getMessage());
            return false;
        }
    }

    // Get orders with pagination
    public function getOrdersWithPagination($limit = 10, $offset = 0) {
        $this->db->query("SELECT o.*, u.name as user_name 
                         FROM " . $this->table . " o 
                         LEFT JOIN users u ON o.user_id = u.user_id 
                         ORDER BY o.created_at DESC 
                         LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    // Calculate total revenue
    public function calculateTotalRevenue() {
        $this->db->query("SELECT SUM(total_amount) as total_revenue 
                         FROM " . $this->table . " 
                         WHERE order_status IN ('paid', 'shipped', 'delivered')");
        $result = $this->db->single();
        return $result->total_revenue ?? 0;
    }

    // Get orders by date range
    public function getOrdersByDateRange($startDate, $endDate) {
        $this->db->query("SELECT o.*, u.name as user_name 
                         FROM " . $this->table . " o 
                         LEFT JOIN users u ON o.user_id = u.user_id 
                         WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date 
                         ORDER BY o.created_at DESC");
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        return $this->db->resultSet();
    }

    // Get revenue by date range
    public function getRevenueByDateRange($startDate, $endDate) {
        $this->db->query("SELECT DATE(created_at) as date, SUM(total_amount) as daily_revenue 
                         FROM " . $this->table . " 
                         WHERE order_status IN ('paid', 'shipped', 'delivered') 
                         AND DATE(created_at) BETWEEN :start_date AND :end_date 
                         GROUP BY DATE(created_at) 
                         ORDER BY date DESC");
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        return $this->db->resultSet();
    }

    // Get order statistics
    public function getOrderStatistics() {
        $this->db->query("SELECT 
                             order_status, 
                             COUNT(*) as count, 
                             SUM(total_amount) as total_amount 
                         FROM " . $this->table . " 
                         GROUP BY order_status");
        return $this->db->resultSet();
    }

    /**
     * Lấy tất cả đơn hàng với thông tin chi tiết
     */
    public function getAllOrders() {
        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email,
                       oi.product_id, oi.quantity, p.name as product_name, img.file_path as primary_image
                FROM {$this->table} o 
                LEFT JOIN users u ON o.user_id = u.user_id
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                LEFT JOIN products p ON oi.product_id = p.product_id
                LEFT JOIN image_usages iu ON p.product_id = iu.ref_id AND iu.ref_type = 'product' AND iu.is_primary = 1
                LEFT JOIN images img ON iu.image_id = img.image_id
                ORDER BY o.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Lấy đơn hàng gần đây
     */
    public function getRecentOrders($limit = 10) {
        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email
                FROM {$this->table} o 
                LEFT JOIN users u ON o.user_id = u.user_id
                ORDER BY o.created_at DESC 
                LIMIT :limit";
        $this->db->query($sql);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Đếm tổng số đơn hàng
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus($id, $status) {
        return $this->updateOrderStatus($id, $status);
    }

    /**
     * Cập nhật trạng thái thanh toán
     * @param int $id Order ID
     * @param string $status Payment status (paid, unpaid)
     * @return bool
     */
    public function updatePaymentStatus($id, $status) {
        $validStatuses = ['paid', 'unpaid'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $this->db->query("UPDATE " . $this->table . " 
                         SET payment_status = :status, updated_at = NOW() 
                         WHERE order_id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Lấy thông tin đơn hàng với email khách hàng
     * Dùng để gửi email confirmation
     * @param int $id Order ID
     * @return object|null
     */
    public function getOrderWithCustomerEmail($id) {
        $sql = "SELECT o.*, u.email as customer_email, u.name as customer_name
                FROM {$this->table} o 
                LEFT JOIN users u ON o.user_id = u.user_id
                WHERE o.order_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Lấy tổng doanh thu
     */
    public function getTotalRevenue() {
        return $this->calculateTotalRevenue();
    }
}