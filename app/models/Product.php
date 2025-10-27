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
        
        // Generate unique slug (không truyền excludeId vì đây là tạo mới)
        $slug = isset($data['slug']) && !empty($data['slug']) 
                ? $data['slug'] 
                : $this->generateSlug($data['name'], null);
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':material', $data['material'] ?? 'gold');
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':base_price', $data['base_price']);
        $this->db->bind(':sku', $data['sku'] ?? null);
        $this->db->bind(':slug', $slug);
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
     * UPDATE - Cập nhật sản phẩm (Flexible - chỉ update fields được truyền)
     * OOP Best Practice: Partial Update
     */
    public function update($id, $data) {
        // Build dynamic UPDATE query chỉ với fields có trong $data
        $setClauses = [];
        $allowedFields = ['name', 'material', 'description', 'base_price', 'sku', 'slug', 'collection_id', 'is_active', 'main_image'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $setClauses[] = "$key = :$key";
            }
        }
        
        // Nếu không có field nào để update, return false
        if (empty($setClauses)) {
            return false;
        }
        
        // Luôn update updated_at
        $setClauses[] = "updated_at = NOW()";
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE product_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        
        // Bind chỉ các fields có trong $data
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $this->db->bind(':' . $key, $value);
            }
        }
        
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
     * Generate slug từ tên sản phẩm với unique check
     * OOP Best Practice: Ensure uniqueness
     * @param string $name
     * @param int|null $excludeId ID của product hiện tại (khi update)
     * @return string
     */
    private function generateSlug($name, $excludeId = null) {
        // Clean and normalize
        $slug = strtolower(trim($name));
        
        // Remove Vietnamese accents
        $slug = $this->removeVietnameseAccents($slug);
        
        // Replace non-alphanumeric with dash
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        
        // Remove multiple dashes
        $slug = preg_replace('/-+/', '-', $slug);
        
        $slug = trim($slug, '-');
        
        // If slug is empty after cleaning, generate random
        if (empty($slug)) {
            $slug = 'product-' . uniqid();
        }
        
        // Check for duplicates and add number if needed
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Kiểm tra slug đã tồn tại chưa
     * @param string $slug
     * @param int|null $excludeId
     * @return bool
     */
    private function slugExists($slug, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = :slug";
        
        if ($excludeId) {
            $sql .= " AND product_id != :id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':slug', $slug);
        
        if ($excludeId) {
            $this->db->bind(':id', $excludeId);
        }
        
        $result = $this->db->single();
        return $result && $result->count > 0;
    }
    
    /**
     * Remove Vietnamese accents (Helper)
     */
    private function removeVietnameseAccents($str) {
        $vietnameseMap = [
            'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
            'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
            'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
            'đ' => 'd',
            'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
            'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
            'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
            'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
            'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
            'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
            'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
        ];
        
        return strtr($str, $vietnameseMap);
    }

    // =================== PRODUCT IMAGE METHODS (Database Schema Compliant) ===================
    
    /**
     * Lấy tất cả ảnh của sản phẩm từ images + image_usages tables
     * Tuân thủ database schema: images JOIN image_usages
     * @param int $productId
     * @return array
     */
    public function getProductImages($productId) {
        $sql = "SELECT i.*, iu.usage_id, iu.is_primary, iu.created_at as usage_created_at
                FROM images i 
                JOIN image_usages iu ON i.image_id = iu.image_id 
                WHERE iu.ref_type = 'product' 
                AND iu.ref_id = :product_id 
                ORDER BY iu.is_primary DESC, iu.created_at ASC";
        
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        
        return $this->db->resultSet();
    }
    
    /**
     * Lấy ảnh chính (primary) của sản phẩm
     * @param int $productId
     * @return object|null
     */
    public function getProductPrimaryImage($productId) {
        $sql = "SELECT i.*, iu.usage_id, iu.is_primary
                FROM images i 
                JOIN image_usages iu ON i.image_id = iu.image_id 
                WHERE iu.ref_type = 'product' 
                AND iu.ref_id = :product_id 
                AND iu.is_primary = 1
                LIMIT 1";
        
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        
        $primaryImage = $this->db->single();
        
        // If no primary image, get first available image
        if (!$primaryImage) {
            $images = $this->getProductImages($productId);
            return $images ? $images[0] : null;
        }
        
        return $primaryImage;
    }

    /**
     * Thêm ảnh cho sản phẩm vào images + image_usages
     * @param int $productId
     * @param string $imagePath
     * @param bool $isPrimary
     * @return int|false Image usage ID hoặc false
     */
    public function addProductImage($productId, $imagePath, $isPrimary = false) {
        // 1. Insert vào images table
        $sql = "INSERT INTO images (file_path, file_name, alt_text, created_at) 
                VALUES (:path, :name, :alt, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':path', $imagePath);
        $this->db->bind(':name', basename($imagePath));
        $this->db->bind(':alt', basename($imagePath));
        
        if (!$this->db->execute()) {
            return false;
        }
        
        $imageId = $this->db->lastInsertId();
        
        // 2. Nếu set làm primary, clear primary cũ
        if ($isPrimary) {
            $this->clearPrimaryImages($productId);
        }
        
        // 3. Insert vào image_usages table
        $sql = "INSERT INTO image_usages (image_id, ref_type, ref_id, is_primary, created_at) 
                VALUES (:image_id, 'product', :ref_id, :is_primary, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':image_id', $imageId);
        $this->db->bind(':ref_id', $productId);
        $this->db->bind(':is_primary', $isPrimary ? 1 : 0);
        
        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    /**
     * Set ảnh làm primary image
     * @param int $usageId
     * @param int $productId
     * @return bool
     */
    public function setImagePrimary($usageId, $productId) {
        // 1. Clear all primary flags
        $this->clearPrimaryImages($productId);
        
        // 2. Set specified image as primary
        $sql = "UPDATE image_usages 
                SET is_primary = 1 
                WHERE usage_id = :usage_id 
                AND ref_type = 'product' 
                AND ref_id = :product_id";
        
        $this->db->query($sql);
        $this->db->bind(':usage_id', $usageId);
        $this->db->bind(':product_id', $productId);
        
        return $this->db->execute();
    }

    /**
     * Clear all primary flags cho product (helper method)
     * @param int $productId
     */
    private function clearPrimaryImages($productId) {
        $sql = "UPDATE image_usages 
                SET is_primary = 0 
                WHERE ref_type = 'product' 
                AND ref_id = :product_id";
        
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $this->db->execute();
    }

    /**
     * Xóa ảnh của sản phẩm
     * @param int $usageId
     * @return bool
     */
    public function deleteProductImage($usageId) {
        // Get image_id first
        $sql = "SELECT image_id FROM image_usages WHERE usage_id = :usage_id";
        $this->db->query($sql);
        $this->db->bind(':usage_id', $usageId);
        $imageRecord = $this->db->single();
        
        if (!$imageRecord) return false;
        
        // Delete from image_usages
        $sql = "DELETE FROM image_usages WHERE usage_id = :usage_id";
        $this->db->query($sql);
        $this->db->bind(':usage_id', $usageId);
        $result = $this->db->execute();
        
        // Check if image is used elsewhere
        $sql = "SELECT COUNT(*) as count FROM image_usages WHERE image_id = :image_id";
        $this->db->query($sql);
        $this->db->bind(':image_id', $imageRecord->image_id);
        $usageCount = $this->db->single();
        
        // If not used elsewhere, delete from images table
        if ($usageCount && $usageCount->count == 0) {
            $sql = "DELETE FROM images WHERE image_id = :image_id";
            $this->db->query($sql);
            $this->db->bind(':image_id', $imageRecord->image_id);
            $this->db->execute();
        }
        
        return $result;
    }

    /**
     * Xóa tất cả ảnh của sản phẩm
     * @param int $productId
     * @return bool
     */
    public function deleteAllProductImages($productId) {
        $sql = "SELECT usage_id FROM image_usages 
                WHERE ref_type = 'product' AND ref_id = :product_id";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $usages = $this->db->resultSet();
        
        foreach ($usages as $usage) {
            $this->deleteProductImage($usage->usage_id);
        }
        
        return true;
    }

}