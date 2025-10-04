<?php
/**
 * Order Model
 * Quản lý đơn hàng
 */

require_once __DIR__ . '/BaseModel.php';

class OrderModel extends BaseModel {
    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    /**
     * Lấy đơn hàng với thông tin chi tiết
     */
    public function getOrdersWithDetails($limit = 10, $offset = 0) {
        $sql = "SELECT o.*, 
                       u.name as customer_name,
                       COUNT(oi.order_item_id) as item_count
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.user_id
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                GROUP BY o.order_id
                ORDER BY o.created_at DESC
                LIMIT {$limit} OFFSET {$offset}";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy đơn hàng gần đây
     */
    public function getRecentOrders($limit = 5) {
        $sql = "SELECT o.*, 
                       u.name as customer_name,
                       p.name as product_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.user_id
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                LEFT JOIN products p ON oi.product_id = p.product_id
                ORDER BY o.created_at DESC
                LIMIT {$limit}";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Thống kê đơn hàng
     */
    public function getOrderStats() {
        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    COUNT(CASE WHEN order_status = 'pending' THEN 1 END) as pending_orders,
                    COUNT(CASE WHEN order_status = 'delivered' THEN 1 END) as delivered_orders,
                    COALESCE(SUM(CASE WHEN order_status = 'delivered' THEN total_amount ELSE 0 END), 0) as total_revenue
                FROM orders";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Cập nhật trạng thái đơn hàng (sử dụng procedure)
     */
    public function updateOrderStatus($order_id, $status) {
        $sql = "CALL sp_UpdateOrderStatus(:order_id, :status)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lấy chi tiết đơn hàng (sử dụng procedure)
     */
    public function getOrderDetails($order_id) {
        $sql = "CALL sp_GetOrderDetails(:order_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
