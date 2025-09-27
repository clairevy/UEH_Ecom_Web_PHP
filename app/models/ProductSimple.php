<?php

/**
 * Product Model - Chỉ CRUD cơ bản
 * Tuân thủ Single Responsibility Principle
 */
class ProductSimple extends BaseModel {
    protected $table = 'products';
    
    // ============= CRUD CƠ BẢN =============
    
    /**
     * CREATE - Tạo sản phẩm mới
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, description, base_price, collection_id, created_at) 
                VALUES (:name, :description, :base_price, :collection_id, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':base_price', $data['base_price']);
        $this->db->bind(':collection_id', $data['collection_id']);
        
        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }
    
    /**
     * READ - Lấy 1 sản phẩm theo ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE product_id = :id AND is_active = 1";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    /**
     * UPDATE - Cập nhật sản phẩm
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                name = :name,
                description = :description,
                base_price = :base_price,
                updated_at = NOW()
                WHERE product_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':base_price', $data['base_price']);
        
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
    
    // ============= METHODS CƠ BẢN KHÁC =============
    
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
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE is_active = 1";
        
        $this->db->query($sql);
        $result = $this->db->single();
        
        return $result->total;
    }
}