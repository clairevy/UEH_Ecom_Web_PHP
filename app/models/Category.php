<?php
class Category extends BaseModel {
    protected $table = 'categories';
    protected $primaryKey = 'category_id';

    public function createCategory($data) {
        $this->db->query("INSERT INTO " . $this->table . " (name, parent_id, slug, is_active, created_at) VALUES (:name, :parent_id, :slug, 1, NOW())");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':parent_id', $data['parent_id'] ?? null);
        $this->db->bind(':slug', $this->generateSlug($data['name']));
        return $this->db->execute();
    }

    public function updateCategory($id, $data) {
        $this->db->query("UPDATE " . $this->table . " SET name = :name, parent_id = :parent_id, slug = :slug, is_active = :is_active WHERE category_id = :id");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':parent_id', $data['parent_id'] ?? null);
        $this->db->bind(':slug', $data['slug'] ?? $this->generateSlug($data['name']));
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function findById($id) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE category_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function findBySlug($slug) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE slug = :slug AND is_active = 1");
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    public function deleteById($id) {
        // Soft delete by deactivating
        $this->db->query("UPDATE " . $this->table . " SET is_active = 0 WHERE category_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getAllCategories($onlyActive = true) {
        $sql = "SELECT * FROM " . $this->table;
        if ($onlyActive) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY name ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // Get all active categories (alias for getAllCategories with active filter)
    public function getAllActiveCategories() {
        return $this->getAllCategories(true);
    }

    // Get parent categories
    public function getParentCategories() {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE parent_id IS NULL AND is_active = 1 ORDER BY name ASC");
        return $this->db->resultSet();
    }

    // Get child categories of a specific parent
    public function getChildCategories($parentId) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE parent_id = :parent_id AND is_active = 1 ORDER BY name ASC");
        $this->db->bind(':parent_id', $parentId);
        return $this->db->resultSet();
    }

    // Assign a product to a category
    public function assignProductToCategory($productId, $categoryId) {
        $this->db->query("INSERT INTO product_categories (product_id, category_id) VALUES (:product_id, :category_id)");
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':category_id', $categoryId);
        return $this->db->execute();
    }

    // Remove a product from a category
    public function removeProductFromCategory($productId, $categoryId) {
        $this->db->query("DELETE FROM product_categories WHERE product_id = :product_id AND category_id = :category_id");
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':category_id', $categoryId);
        return $this->db->execute();
    }

    // Get all categories for a product
    public function getCategoriesForProduct($productId) {
        $this->db->query("SELECT c.* FROM " . $this->table . " c 
                         JOIN product_categories pc ON c.category_id = pc.category_id 
                         WHERE pc.product_id = :product_id AND c.is_active = 1");
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet();
    }

    private function generateSlug($name, $id = null) {
        // Convert to lowercase and clean
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Check for duplicates and add number if needed
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $id)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugExists($slug, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = :slug";
        if ($excludeId) {
            $sql .= " AND category_id != :id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':slug', $slug);
        if ($excludeId) {
            $this->db->bind(':id', $excludeId);
        }
        
        $result = $this->db->single();
        return $result && $result->count > 0;
    }

    // =================== CATEGORY IMAGE METHODS ===================
    
    /**
     * Get all images for a category
     */
    public function getCategoryImages($categoryId) {
        $sql = "SELECT i.*, iu.usage_id, iu.ref_type, iu.ref_id, iu.is_primary
                FROM images i 
                JOIN image_usages iu ON i.image_id = iu.image_id 
                WHERE iu.ref_type = 'category' 
                AND iu.ref_id = :category_id 
                ORDER BY iu.is_primary DESC, iu.created_at ASC";
        
        $this->db->query($sql);
        $this->db->bind(':category_id', $categoryId);
        
        return $this->db->resultSet();
    }
    
    /**
     * Get category banner (primary image)
     */
    public function getCategoryBanner($categoryId) {
        $images = $this->getCategoryImages($categoryId);
        return $images ? $images[0] : null;
    }
    
    /**
     * Add banner to category (replaces existing)
     */
    public function addCategoryBanner($categoryId, $imagePath) {
        // Delete existing banner first (categories usually have 1 banner)
        $this->deleteAllCategoryImages($categoryId);
        
        // Insert into images table first
        $this->db->query("INSERT INTO images (file_path, file_name, alt_text, created_at) VALUES (:path, :name, :alt, NOW())");
        $this->db->bind(':path', $imagePath);
        $this->db->bind(':name', basename($imagePath));
        $this->db->bind(':alt', basename($imagePath));
        
        if (!$this->db->execute()) {
            return false;
        }
        
        $imageId = $this->db->lastInsertId();
        
        // Insert into image_usages table
        $this->db->query("INSERT INTO image_usages (image_id, ref_type, ref_id, is_primary, created_at) 
                         VALUES (:image_id, 'category', :ref_id, 1, NOW())");
        $this->db->bind(':image_id', $imageId);
        $this->db->bind(':ref_id', $categoryId);
        
        return $this->db->execute() ? $imageId : false;
    }
    
    /**
     * Delete a specific category image by usage_id
     */
    public function deleteCategoryImage($usageId) {
        // Get image_id first
        $this->db->query("SELECT image_id FROM image_usages WHERE usage_id = :usage_id");
        $this->db->bind(':usage_id', $usageId);
        $imageRecord = $this->db->single();
        
        if (!$imageRecord) return false;
        
        // Delete from image_usages
        $this->db->query("DELETE FROM image_usages WHERE usage_id = :usage_id");
        $this->db->bind(':usage_id', $usageId);
        $result1 = $this->db->execute();
        
        // Check if image is used elsewhere
        $this->db->query("SELECT COUNT(*) as count FROM image_usages WHERE image_id = :image_id");
        $this->db->bind(':image_id', $imageRecord->image_id);
        $usage_count = $this->db->single();
        
        // If not used elsewhere, delete from images table
        if ($usage_count->count == 0) {
            $this->db->query("DELETE FROM images WHERE image_id = :image_id");
            $this->db->bind(':image_id', $imageRecord->image_id);
            $this->db->execute();
        }
        
        return $result1;
    }
    
    /**
     * Delete all images for a category
     */
    public function deleteAllCategoryImages($categoryId) {
        $this->db->query("SELECT usage_id FROM image_usages 
                         WHERE ref_type = 'category' AND ref_id = :category_id");
        $this->db->bind(':category_id', $categoryId);
        $usages = $this->db->resultSet();
        
        foreach ($usages as $usage) {
            $this->deleteCategoryImage($usage->usage_id);
        }
        
        return true;
    }
    
    /**
     * Get categories with their banners (for display purposes)
     */
    public function getAllCategoriesWithBanners($onlyActive = true) {
        $categories = $this->getAllCategories($onlyActive);
        
        foreach ($categories as $category) {
            $category->banner_image = $this->getCategoryBanner($category->category_id);
        }
        
        return $categories;
    }
    
    /**
     * Get single category with banner
     */
    public function getCategoryWithBanner($categoryId) {
        $category = $this->findById($categoryId);
        if ($category) {
            $category->banner_image = $this->getCategoryBanner($categoryId);
        }
        return $category;
    }
    
    /**
     * Get category by slug with banner
     */
    public function getCategoryBySlugWithBanner($slug) {
        $category = $this->findBySlug($slug);
        if ($category) {
            $category->banner_image = $this->getCategoryBanner($category->category_id);
        }
        return $category;
    }

    /**
     * Lấy tất cả danh mục với số lượng sản phẩm
     */
    public function getAllWithProductCount($onlyActive = true) {
        $sql = "SELECT c.*, 
                       COALESCE(pc.product_count, 0) as product_count
                FROM {$this->table} c
                LEFT JOIN (
                    SELECT category_id, COUNT(*) as product_count
                    FROM product_categories
                    GROUP BY category_id
                ) pc ON c.category_id = pc.category_id";
        
        if ($onlyActive) {
            $sql .= " WHERE c.is_active = 1";
        }
        
        $sql .= " ORDER BY c.name ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Đếm tổng số danh mục
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }

    /**
     * Tạo danh mục mới
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, description, image_url, icon_url, parent_id, slug, is_active, created_at) 
                VALUES (:name, :description, :image_url, :icon_url, :parent_id, :slug, :is_active, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? '');
        $this->db->bind(':image_url', $data['image_url'] ?? '');
        $this->db->bind(':icon_url', $data['icon_url'] ?? '');
        $this->db->bind(':parent_id', $data['parent_id'] ?? null);
        $this->db->bind(':slug', $this->generateSlug($data['name']));
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        
        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    /**
     * Cập nhật danh mục
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET name = :name, description = :description, image_url = :image_url, 
                    icon_url = :icon_url, parent_id = :parent_id, slug = :slug, is_active = :is_active 
                WHERE category_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? '');
        $this->db->bind(':image_url', $data['image_url'] ?? '');
        $this->db->bind(':icon_url', $data['icon_url'] ?? '');
        $this->db->bind(':parent_id', $data['parent_id'] ?? null);
        $this->db->bind(':slug', $data['slug'] ?? $this->generateSlug($data['name']));
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    /**
     * Xóa danh mục
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE category_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}