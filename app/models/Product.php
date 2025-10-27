<?php

/**
 * Product Model - Clean CRUD Operations Only
 * Following Single Responsibility Principle
 *
 * MODIFIED TO USE STORED PROCEDURES
 */
class Product extends BaseModel {
    protected $table = 'products';
    protected $primaryKey = 'product_id';

    // ============= CORE CRUD OPERATIONS =============
    
    /**
     * CREATE - Tạo sản phẩm mới (sử dụng SP)
     */
    public function create($data) {
        $sql = "CALL sp_product_create(:name, :description, :base_price, :sku, :slug, :collection_id, :is_active)";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':base_price', $data['base_price']);
        $this->db->bind(':sku', $data['sku'] ?? null);
        $this->db->bind(':slug', $data['slug'] ?? $this->generateSlug($data['name']));
        $this->db->bind(':collection_id', $data['collection_id'] ?? null);
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        
        // Giả định db->execute() chạy sp và db->lastInsertId() vẫn hoạt động trên cùng connection
        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    
    /**
     * READ - Lấy sản phẩm theo ID với stock từ variants (sử dụng SP)
     */
    public function findById($id) {
        $sql = "CALL sp_product_findById(:id)";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * READ - Lấy sản phẩm theo slug (sử dụng SP)
     */
    public function findBySlug($slug) {
        $sql = "CALL sp_product_findBySlug(:slug)";
        $this->db->query($sql);
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }
    
    /**
     * UPDATE - Cập nhật sản phẩm (sử dụng SP)
     */
    public function update($id, $data) {
        $sql = "CALL sp_product_update(:id, :name, :description, :base_price, :sku, :slug, :collection_id, :is_active)";
        
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
     * DELETE - Xóa sản phẩm (soft delete) (sử dụng SP)
     */
    public function delete($id) {
        $sql = "CALL sp_product_delete(:id)";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    // ============= BASIC UTILITY METHODS =============
    
    /**
     * Lấy tất cả sản phẩm với pagination (sử dụng SP)
     */
    public function getAll($limit = 10, $offset = 0) {
        $sql = "CALL sp_product_getAll(:limit, :offset)";
        
        $this->db->query($sql);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }
    
    /**
     * Đếm tổng số sản phẩm (sử dụng SP)
     */
    public function count($conditions = []) {
        $sql = "CALL sp_product_count(:category_id)";
        $this->db->query($sql);
        
        $categoryId = $conditions['category_id'] ?? null;
        $this->db->bind(':category_id', $categoryId);
        
        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }
    
    /**
     * UPDATE - Cập nhật stock từ variant (sử dụng SP)
     */
    public function updateStock($productId, $size, $color, $quantity) {
        try {
            $sql = "CALL sp_product_updateStock(:product_id, :size, :color, :quantity)";
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
     * READ - Kiểm tra stock từ variant (sử dụng SP)
     * (Loại bỏ logic debug)
     */
    public function checkStock($productId, $size, $color, $quantity) {
        $sql = "CALL sp_product_checkStock(:product_id, :size, :color)";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':size', $size);
        $this->db->bind(':color', $color);
        $result = $this->db->single();
        
        // Logic kiểm tra số lượng vẫn nằm ở PHP
        return $result && $result->stock >= $quantity;
    }
    
    /**
     * READ - Lấy tất cả variants của sản phẩm (sử dụng SP)
     */
    public function getVariants($productId) {
        $sql = "CALL sp_product_getVariants(:product_id)";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet();
    }
    
    /**
     * READ - Lấy total stock của sản phẩm (sử dụng SP)
     */
    public function getTotalStock($productId) {
        $sql = "CALL sp_product_getTotalStock(:product_id)";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $result = $this->db->single();
        return $result ? (int)$result->total_stock : 0;
    }

    /**
     * Lấy tất cả variants có sẵn cho sản phẩm (sử dụng SP)
     */
    public function getAvailableVariants($productId) {
        $sql = "CALL sp_product_getAvailableVariants(:product_id)";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet();
    }

    // ============= HELPER METHODS =============
    
    /**
     * Generate slug từ tên sản phẩm
     * (Phương thức này là logic PHP, không liên quan đến DB nên giữ nguyên)
     */
    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
}
