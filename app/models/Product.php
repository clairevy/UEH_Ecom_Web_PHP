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
     * READ - Lấy sản phẩm theo ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE product_id = :id AND is_active = 1";
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