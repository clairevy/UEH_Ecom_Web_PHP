<?php

/**
 * Product Model - Clean CRUD Operations Only
 * Following Single Responsibility Principle
 */
class Product extends BaseModel {
    protected $table = 'products';
    protected $primaryKey = 'product_id';

    // ============= CORE CRUD OPERATIONS =============
    
    /**
     * CREATE - Tạo sản phẩm mới
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
                (name, description, base_price, sku, slug, collection_id, is_active, created_at, updated_at) 
                VALUES (:name, :description, :base_price, :sku, :slug, :collection_id, :is_active, NOW(), NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':base_price', $data['base_price']);
        $this->db->bind(':sku', $data['sku'] ?? null);
        $this->db->bind(':slug', $data['slug'] ?? $this->generateSlug($data['name']));
        $this->db->bind(':collection_id', $data['collection_id'] ?? null);
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        
        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    
    /**
     * READ - Lấy sản phẩm theo ID với stock từ variants
     */
    public function findById($id) {
        $sql = "SELECT p.*, 
                       COALESCE(SUM(pv.stock), 0) as stock_quantity,
                       COUNT(pv.variant_id) as variant_count
                FROM {$this->table} p 
                LEFT JOIN product_variants pv ON p.product_id = pv.product_id
                WHERE p.product_id = :id AND p.is_active = 1
                GROUP BY p.product_id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * READ - Lấy sản phẩm theo slug
     */
    public function findBySlug($slug) {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug AND is_active = 1";
        $this->db->query($sql);
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }
    
    /**
     * UPDATE - Cập nhật sản phẩm
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET name = :name, description = :description, base_price = :base_price, 
                    sku = :sku, slug = :slug, collection_id = :collection_id,
                    is_active = :is_active, updated_at = NOW() 
                WHERE product_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':base_price', $data['base_price']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':collection_id', $data['collection_id']);
        $this->db->bind(':is_active', $data['is_active']);
        
        return $this->db->execute();
    }
    
    /**
     * DELETE - Xóa sản phẩm (soft delete)
     */
    public function delete($id) {
        $sql = "UPDATE {$this->table} SET is_active = 0, updated_at = NOW() WHERE product_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    // ============= BASIC UTILITY METHODS =============
    
    /**
     * Lấy tất cả sản phẩm với pagination
     */
    public function getAll($limit = 10, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 
                ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }
    
    /**
     * Đếm tổng số sản phẩm
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE is_active = 1";
        
        if (!empty($conditions['category_id'])) {
            $sql .= " AND EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = {$this->table}.product_id AND pc.category_id = :category_id)";
        }
        
        $this->db->query($sql);
        
        if (!empty($conditions['category_id'])) {
            $this->db->bind(':category_id', $conditions['category_id']);
        }
        
        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }
    
    /**
     * UPDATE - Cập nhật stock từ variant
     */
    public function updateStock($productId, $size, $color, $quantity) {
        try {
            $sql = "UPDATE product_variants 
                    SET stock = stock - :quantity 
                    WHERE product_id = :product_id AND size = :size AND color = :color AND stock >= :quantity";
            $this->db->query($sql);
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':size', $size);
            $this->db->bind(':color', $color);
            $this->db->bind(':quantity', $quantity);
            
            $result = $this->db->execute();
            return $result && $this->db->rowCount() > 0; // Kiểm tra có row nào được update không
        } catch (Exception $e) {
            error_log("Update Variant Stock Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * READ - Kiểm tra stock từ variant
     */
    public function checkStock($productId, $size, $color, $quantity) {
        // Debug: First check all variants for this product
        $debugSql = "SELECT variant_id, size, color, stock, HEX(size) as size_hex, HEX(color) as color_hex FROM product_variants WHERE product_id = :product_id";
        $this->db->query($debugSql);
        $this->db->bind(':product_id', $productId);
        $allVariants = $this->db->resultSet();
        
        error_log("CheckStock Debug - All variants for product $productId:");
        foreach ($allVariants as $variant) {
            error_log("  ID: {$variant->variant_id}, Size: '{$variant->size}' (hex: {$variant->size_hex}), Color: '{$variant->color}' (hex: {$variant->color_hex}), Stock: {$variant->stock}");
        }
        
        error_log("CheckStock Debug - Looking for Size: '$size' (hex: " . bin2hex($size) . "), Color: '$color' (hex: " . bin2hex($color) . ")");
        
        $sql = "SELECT stock FROM product_variants 
                WHERE product_id = :product_id 
                AND BINARY size = BINARY :size  
                AND BINARY color = BINARY :color";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':size', $size);
        $this->db->bind(':color', $color);
        $result = $this->db->single();
        
        // Debug logging
        error_log("CheckStock Debug - Found stock: " . ($result ? $result->stock : 'NULL'));
        
        return $result && $result->stock >= $quantity;
    }
    
    /**
     * READ - Lấy tất cả variants của sản phẩm
     */
    public function getVariants($productId) {
        $sql = "SELECT * FROM product_variants WHERE product_id = :product_id AND stock > 0 ORDER BY size, color";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet();
    }
    
    /**
     * READ - Lấy total stock của sản phẩm
     */
    public function getTotalStock($productId) {
        $sql = "SELECT SUM(stock) as total_stock FROM product_variants WHERE product_id = :product_id";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $result = $this->db->single();
        return $result ? (int)$result->total_stock : 0;
    }

    /**
     * Lấy tất cả variants có sẵn cho sản phẩm
     */
    public function getAvailableVariants($productId) {
        $sql = "SELECT * FROM product_variants 
                WHERE product_id = :product_id AND stock > 0 
                ORDER BY color ASC, size ASC";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet();
    }

    // ============= HELPER METHODS =============
    
    /**
     * Generate slug từ tên sản phẩm
     */
    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

}