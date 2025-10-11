<?php

/**
 * Product Model - Clean CRUD Operations Only
 * Following Single Responsibility Principle
 */
class Product extends BaseModel {
    protected $table = 'products';
    protected $primaryKey = 'product_id';

    // Các trường dữ liệu trong bảng
    protected $fillable = [
        'name',
        'description',
        'material',
        'base_price',
        'sku',
        'slug',
        'collection_id',
        'is_active',
        'main_image'
    ];

    // ============= CORE CRUD OPERATIONS =============
    
    /**
     * CREATE - Tạo sản phẩm mới
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
                (name, material, description, base_price, sku, slug, collection_id, is_active, created_at, updated_at) 
                VALUES (:name, :material, :description, :base_price, :sku, :slug, :collection_id, :is_active, NOW(), NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':material', $data['material'] ?? 'gold');
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
     * READ - Lấy sản phẩm theo material
     */
    public function findByMaterial($material) {
        $sql = "SELECT * FROM {$this->table} WHERE material = :material AND is_active = 1 ORDER BY created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':material', $material);
        return $this->db->resultSet();
    }
    
    /**
     * UPDATE - Cập nhật sản phẩm
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET name = :name, material = :material, description = :description, base_price = :base_price, 
                    sku = :sku, slug = :slug, collection_id = :collection_id,
                    is_active = :is_active, updated_at = NOW() 
                WHERE product_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':material', $data['material'] ?? 'gold');
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
     * Lấy tất cả sản phẩm với thông tin chi tiết
     */
    public function getAllWithDetails() {
        $sql = "SELECT p.*, 
                       c.collection_name,
                       cat.name as category_name,
                       COALESCE(SUM(oi.quantity), 0) as total_sold,
                       COALESCE(COUNT(oi.order_id), 0) as order_count
                FROM {$this->table} p 
                LEFT JOIN collection c ON p.collection_id = c.collection_id 
                LEFT JOIN product_categories pc ON p.product_id = pc.product_id
                LEFT JOIN categories cat ON pc.category_id = cat.category_id
                LEFT JOIN order_items oi ON p.product_id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.order_id AND o.order_status IN ('paid', 'shipped', 'delivered')
                GROUP BY p.product_id
                ORDER BY p.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Lấy sản phẩm bán chạy nhất
     */
    public function getBestSellers($limit = 10) {
        $sql = "SELECT p.*, 
                       img.file_path as primary_image,
                       COALESCE(SUM(oi.quantity), 0) as total_sold, 
                       COALESCE(COUNT(oi.order_id), 0) as order_count
                FROM {$this->table} p 
                LEFT JOIN image_usages iu ON p.product_id = iu.ref_id AND iu.ref_type = 'product' AND iu.is_primary = 1
                LEFT JOIN images img ON iu.image_id = img.image_id
                LEFT JOIN order_items oi ON p.product_id = oi.product_id 
                LEFT JOIN orders o ON oi.order_id = o.order_id AND o.order_status IN ('paid', 'shipped', 'delivered')
                WHERE p.is_active = 1 
                GROUP BY p.product_id 
                ORDER BY total_sold DESC, order_count DESC 
                LIMIT :limit";
        $this->db->query($sql);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Đếm tổng số sản phẩm
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }

    /**
     * Lấy tổng doanh thu từ sản phẩm
     */
    public function getTotalRevenue() {
        $sql = "SELECT SUM(oi.total_price) as total_revenue 
                FROM order_items oi 
                JOIN orders o ON oi.order_id = o.order_id 
                WHERE o.order_status IN ('paid', 'shipped', 'delivered')";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (float)$result->total_revenue : 0;
    }

    /**
     * Lấy danh sách materials có sẵn
     */
    public function getAvailableMaterials() {
        return ['gold', 'silver', 'diamond', 'pearl'];
    }
    
    /**
     * Lấy thống kê theo material
     */
    public function getMaterialStats() {
        $sql = "SELECT material, COUNT(*) as count, AVG(base_price) as avg_price 
                FROM {$this->table} 
                WHERE is_active = 1 
                GROUP BY material 
                ORDER BY count DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

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