<?php

/**
 * Product Model - Clean CRUD Operations Only
 * Following Single Responsibility Principle
 *
 * MODIFIED TO USE STORED PROCEDURES
 */
class Product extends BaseModel {
    private static $imageTableColumns = null;
    protected $table = 'products';
    protected $primaryKey = 'product_id';


   
    // ============= CORE CRUD OPERATIONS =============
    
    /**
     * CREATE - Tạo sản phẩm mới
     */
    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " 
                (name, description, material, base_price, sku, slug, collection_id, is_active, created_at, updated_at) 
                VALUES 
                (:name, :description, :material, :base_price, :sku, :slug, :collection_id, :is_active, NOW(), NOW())";
        
        // Generate slug if not provided
        $slug = isset($data['slug']) ? $data['slug'] : $this->generateSlug($data['name']);
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? '');
        $this->db->bind(':material', $data['material'] ?? 'gold');
        $this->db->bind(':base_price', $data['base_price']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':slug', $slug);
        $this->db->bind(':collection_id', $data['collection_id']);
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    /**
     * CREATE - Tạo product variants
     */
    public function createVariants($productId, $variants) {
        if (empty($variants) || !is_array($variants)) {
            return false;
        }

        $sql = "INSERT INTO product_variants (product_id, size, color, price, stock, created_at, updated_at) 
                VALUES (:product_id, :size, :color, :price, :stock, NOW(), NOW())";

        $success = true;
        foreach ($variants as $variant) {
            // Validate variant data
            if (empty($variant['size']) || empty($variant['color']) || 
                !isset($variant['price']) || !isset($variant['stock'])) {
                continue;
            }

            $this->db->query($sql);
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':size', trim($variant['size']));
            $this->db->bind(':color', trim($variant['color']));
            $this->db->bind(':price', floatval($variant['price']));
            $this->db->bind(':stock', intval($variant['stock']));
            
            if (!$this->db->execute()) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * CREATE - Tạo sản phẩm với variants
     */
    public function createWithVariants($productData, $variants = []) {
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Create product first
            $productId = $this->create($productData);
            if (!$productId) {
                throw new Exception('Failed to create product');
            }

            // Create variants if provided
            if (!empty($variants)) {
                $variantResult = $this->createVariants($productId, $variants);
                if (!$variantResult) {
                    throw new Exception('Failed to create product variants');
                }
            }

            // Commit transaction
            $this->db->commit();
            return $productId;
            
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            error_log('Product createWithVariants failed: ' . $e->getMessage());
            return false;
        }
    }

   
    /**
     * READ - Lấy sản phẩm theo ID với stock từ variants
     */
    public function findById($id) {
        $sql = "SELECT p.*, c.collection_name,
                       (SELECT SUM(pv.stock) FROM product_variants pv WHERE pv.product_id = p.product_id) as total_stock
                FROM " . $this->table . " p 
                LEFT JOIN collection c ON p.collection_id = c.collection_id 
                WHERE p.product_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * READ - Lấy sản phẩm theo slug
     */
    public function findBySlug($slug) {
        $sql = "SELECT p.*, c.collection_name,
                       (SELECT SUM(pv.stock) FROM product_variants pv WHERE pv.product_id = p.product_id) as total_stock
                FROM " . $this->table . " p 
                LEFT JOIN collection c ON p.collection_id = c.collection_id 
                WHERE p.slug = :slug";
        $this->db->query($sql);
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }
    
    /**
     * Get primary image URL for a product
     */
    public function getPrimaryImageUrl($productId) {
        $sql = "SELECT i.file_path 
                FROM images i 
                JOIN image_usages iu ON i.image_id = iu.image_id 
                WHERE iu.ref_type = 'product' 
                AND iu.ref_id = :product_id 
                AND iu.is_primary = 1 
                LIMIT 1";
        
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $result = $this->db->single();
        
        // Return image URL or default fallback
        return $result ? $result->file_path : 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=100&h=100&fit=crop';
    }

    /**
     * UPDATE - Cập nhật sản phẩm (sử dụng SP)
     */
    public function update($id, $data) {
        $allowedFields = ['name', 'description', 'base_price', 'sku', 'slug', 'collection_id', 'is_active', 'main_image'];
        
        // If updating main_image only, use simple UPDATE
        if (count($data) === 1 && isset($data['main_image'])) {
            $sql = "UPDATE products SET main_image = :main_image WHERE product_id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->bind(':main_image', $data['main_image']);
            return $this->db->execute();
        }
        
        // For full update, use stored procedure
        $sql = "CALL sp_product_update(:id, :name, :description, :base_price, :sku, :slug, :collection_id, :is_active)";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        
        // Bind chỉ các fields có trong $data và allowed
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields) && $key !== 'main_image') {
                $this->db->bind(':' . $key, $value);
            }
        }
        
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
     * READ - Kiểm tra stock từ variant thực tế
     */
    public function checkStock($productId, $size, $color, $quantity) {
        // Check if specific variant has enough stock
        $sql = "SELECT pv.stock
                FROM product_variants pv 
                JOIN products p ON pv.product_id = p.product_id 
                WHERE pv.product_id = :product_id 
                AND pv.size = :size 
                AND pv.color = :color 
                AND p.is_active = 1";
        
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':size', $size);
        $this->db->bind(':color', $color);
        $result = $this->db->single();
        // Return true if variant exists and has enough stock
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

    /**
     * UPDATE - Cập nhật stock cho sản phẩm đơn giản (không có variant)
     */
    public function updateSimpleStock($productId, $newStock) {
        try {
            $sql = "UPDATE products SET stock_quantity = :new_stock WHERE product_id = :product_id";
            $this->db->query($sql);
            $this->db->bind(':new_stock', $newStock);
            $this->db->bind(':product_id', $productId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Update Simple Product Stock Error: " . $e->getMessage());
            return false;
        }
    }

    // ============= HELPER METHODS =============
    
    /**
     * Generate slug từ tên sản phẩm - with uniqueness check
     */
   private function generateSlug($name) {
        $baseSlug = strtolower(trim($name));
        $baseSlug = preg_replace('/[^a-z0-9-]/', '-', $baseSlug);
        $baseSlug = preg_replace('/-+/', '-', $baseSlug);
        $baseSlug = trim($baseSlug, '-');
        
        // Check for uniqueness
        $slug = $baseSlug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Check if slug exists in database
     */
    private function slugExists($slug) {
        $sql = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE slug = :slug";
        $this->db->query($sql);
        $this->db->bind(':slug', $slug);
        $result = $this->db->single();
        return $result && $result->count > 0;
    }
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }
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
     * Get available materials
     */
    public function getAvailableMaterials() {
        return ['gold', 'silver', 'diamond', 'pearl'];
    }
     public function addProductImage($productId, $imagePath, $isPrimary = false) {
        error_log("=== ADD PRODUCT IMAGE DEBUG ===");
        error_log("Product ID: " . $productId);
        error_log("Image Path: " . $imagePath);
        error_log("Is Primary: " . ($isPrimary ? 'Yes' : 'No'));
        
        try {
            // Get file info
            $fileExtension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            $mimeType = $this->getMimeTypeFromExtension($fileExtension);
            $fileName = basename($imagePath);
            
            // Get file size if file exists
            $projectRoot = dirname(__DIR__, 2);
            $fullPath = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $imagePath);
            $fileSize = (is_file($fullPath) && is_readable($fullPath)) ? filesize($fullPath) : null;
            
            error_log("File extension: " . $fileExtension);
            error_log("MIME type: " . $mimeType);
            error_log("File size: " . ($fileSize !== null ? $fileSize : 'UNKNOWN'));
            error_log("Full path: " . $fullPath);
            
            // 1. Insert vào images table (tự động map theo columns thực tế)
            $imageData = [
                'file_path' => $imagePath,
                'file_name' => $fileName,
                'file_type' => $mimeType,
                'file_size' => $fileSize,
                'alt_text' => $fileName,
            ];
            
            $availableColumns = $this->getImageTableColumns();
            $filteredData = [];
            foreach ($imageData as $column => $value) {
                if (in_array($column, $availableColumns, true)) {
                    // Nếu column là file_size nhưng chưa có giá trị, bỏ qua để tránh NULL không cần thiết
                    if ($column === 'file_size' && $value === null) {
                        continue;
                    }
                    $filteredData[$column] = $value;
                }
            }
            
            if (empty($filteredData)) {
                throw new Exception('Image table does not contain expected columns.');
            }
            
            $columns = array_keys($filteredData);
            $placeholders = array_map(function ($col) {
                return ':' . $col;
            }, $columns);
            $sql = "INSERT INTO images (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
            
            $this->db->query($sql);
            foreach ($filteredData as $column => $value) {
                $this->db->bind(':' . $column, $value);
            }
            
            if (!$this->db->execute()) {
                error_log('Failed to insert image into images table: ' . $imagePath);
                return false;
            }
            
            $imageId = $this->db->lastInsertId();
            error_log("Image inserted with ID: " . $imageId);
            
            // 2. Nếu set làm primary, clear primary cũ
            if ($isPrimary) {
                $this->clearPrimaryImages($productId);
            }
            
            // 3. Insert vào image_usages table
            $sql = "INSERT INTO image_usages (image_id, ref_type, ref_id, is_primary) 
                    VALUES (:image_id, 'product', :ref_id, :is_primary)";
            
            $this->db->query($sql);
            $this->db->bind(':image_id', $imageId);
            $this->db->bind(':ref_id', $productId);
            $this->db->bind(':is_primary', $isPrimary ? 1 : 0);
            
            if (!$this->db->execute()) {
                error_log('Failed to insert into image_usages table');
                return false;
            }
            
            $usageId = $this->db->lastInsertId();
            error_log("Image usage created with ID: " . $usageId);
            
            return $usageId;
            
        } catch (Exception $e) {
            error_log("addProductImage Exception: " . $e->getMessage());
            return false;
        }
    }
    
    private function getImageTableColumns() {
        if (self::$imageTableColumns === null) {
            $this->db->query('SHOW COLUMNS FROM images');
            $columns = $this->db->resultSet();
            self::$imageTableColumns = [];
            if ($columns) {
                foreach ($columns as $column) {
                    if (isset($column->Field)) {
                        self::$imageTableColumns[] = $column->Field;
                    }
                }
            }
        }
        return self::$imageTableColumns;
    }
    
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
     * Get MIME type from file extension
     */
    private function getMimeTypeFromExtension($extension) {
        $types = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        
        return isset($types[$extension]) ? $types[$extension] : 'image/jpeg';
    }
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
}
