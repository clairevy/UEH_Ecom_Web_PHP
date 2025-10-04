<?php
/**
 * Product Model
 * Quản lý sản phẩm
 */

require_once __DIR__ . '/BaseModel.php';

class ProductModel extends BaseModel {
    protected $table = 'products';
    protected $primaryKey = 'product_id';

    /**
     * Lấy sản phẩm với thông tin category
     */
    public function getProductsWithCategories($limit = 10, $offset = 0) {
        $sql = "SELECT p.*, 
                       GROUP_CONCAT(c.name) as category_names,
                       COUNT(pv.variant_id) as variant_count,
                       COALESCE(SUM(pv.stock), 0) as total_stock
                FROM products p
                LEFT JOIN product_categories pc ON p.product_id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.category_id
                LEFT JOIN product_variants pv ON p.product_id = pv.product_id
                WHERE p.is_active = 1
                GROUP BY p.product_id
                ORDER BY p.created_at DESC
                LIMIT {$limit} OFFSET {$offset}";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy sản phẩm bán chạy (sử dụng procedure)
     */
    public function getTopSellingProducts($limit = 5) {
        $sql = "CALL sp_GetTopSellingProducts(NULL, NULL, :limit)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tìm kiếm sản phẩm
     */
    public function searchProducts($keyword, $category_id = null, $limit = 10, $offset = 0) {
        $sql = "SELECT p.*, 
                       GROUP_CONCAT(c.name) as category_names
                FROM products p
                LEFT JOIN product_categories pc ON p.product_id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.category_id
                WHERE p.is_active = 1";
        
        $params = [];
        
        if ($keyword) {
            $sql .= " AND (p.name LIKE :keyword OR p.description LIKE :keyword)";
            $params[':keyword'] = "%{$keyword}%";
        }
        
        if ($category_id) {
            $sql .= " AND pc.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
        
        $sql .= " GROUP BY p.product_id ORDER BY p.created_at DESC LIMIT {$limit} OFFSET {$offset}";
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Thêm sản phẩm mới (sử dụng procedure)
     */
    public function addProduct($name, $description, $base_price, $sku, $slug, $collection_id = null, $category_ids = null) {
        $sql = "CALL sp_AddProduct(:name, :description, :base_price, :sku, :slug, :collection_id, :category_ids)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':base_price', $base_price);
        $stmt->bindParam(':sku', $sku);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':collection_id', $collection_id);
        $stmt->bindParam(':category_ids', $category_ids);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
