<?php
class Collection extends BaseModel {
    protected $table = 'collection';
    protected $primaryKey = 'collection_id';

    public function createCollection($data) {
        $this->db->query("INSERT INTO " . $this->table . " (collection_name, description, slug, is_active, created_at, updated_at) VALUES (:collection_name, :description, :slug, 1, NOW(), NOW())");
        $this->db->bind(':collection_name', $data['collection_name']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':slug', $this->generateSlug($data['collection_name']));
        return $this->db->execute();
    }

    public function updateCollection($id, $data) {
        $this->db->query("UPDATE " . $this->table . " SET collection_name = :collection_name, description = :description, slug = :slug, is_active = :is_active, updated_at = NOW() WHERE collection_id = :id");
        $this->db->bind(':collection_name', $data['collection_name']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':slug', $data['slug'] ?? $this->generateSlug($data['collection_name']));
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function findById($id) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE collection_id = :id");
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
        $this->db->query("UPDATE " . $this->table . " SET is_active = 0, updated_at = NOW() WHERE collection_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getAllCollections($onlyActive = true) {
        $sql = "SELECT * FROM " . $this->table;
        if ($onlyActive) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY collection_name ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // Get all products in a collection
    public function getProductsInCollection($collectionId) {
        $this->db->query("SELECT p.* FROM products p WHERE p.collection_id = :collection_id AND p.is_active = 1 ORDER BY p.name ASC");
        $this->db->bind(':collection_id', $collectionId);
        return $this->db->resultSet();
    }

    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    // =================== COLLECTION IMAGE METHODS ===================
    
    /**
     * Get all images for a collection
     */
    public function getCollectionImages($collectionId) {
        $sql = "SELECT i.*, iu.usage_id, iu.ref_type, iu.ref_id, iu.is_primary
                FROM images i 
                JOIN image_usages iu ON i.image_id = iu.image_id 
                WHERE iu.ref_type = 'collection' 
                AND iu.ref_id = :collection_id 
                ORDER BY iu.is_primary DESC, iu.created_at ASC";
        
        $this->db->query($sql);
        $this->db->bind(':collection_id', $collectionId);
        
        return $this->db->resultSet();
    }
    
    /**
     * Get collection cover (primary image)
     */
    public function getCollectionCover($collectionId) {
        $images = $this->getCollectionImages($collectionId);
        return $images ? $images[0] : null;
    }
    
    /**
     * Add cover to collection (replaces existing)
     */
    public function addCollectionCover($collectionId, $imagePath) {
        // Delete existing cover first
        $this->deleteAllCollectionImages($collectionId);
        
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
                         VALUES (:image_id, 'collection', :ref_id, 1, NOW())");
        $this->db->bind(':image_id', $imageId);
        $this->db->bind(':ref_id', $collectionId);
        
        return $this->db->execute() ? $imageId : false;
    }
    
    /**
     * Delete a specific collection image by usage_id
     */
    public function deleteCollectionImage($usageId) {
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
     * Delete all images for a collection
     */
    public function deleteAllCollectionImages($collectionId) {
        $this->db->query("SELECT usage_id FROM image_usages 
                         WHERE ref_type = 'collection' AND ref_id = :collection_id");
        $this->db->bind(':collection_id', $collectionId);
        $usages = $this->db->resultSet();
        
        foreach ($usages as $usage) {
            $this->deleteCollectionImage($usage->usage_id);
        }
        
        return true;
    }
    
    /**
     * Get collections with their covers (for display purposes)
     */
    public function getAllCollectionsWithCovers($onlyActive = true) {
        $collections = $this->getAllCollections($onlyActive);
        
        foreach ($collections as $collection) {
            $collection->cover_image = $this->getCollectionCover($collection->collection_id);
        }
        
        return $collections;
    }
    
    /**
     * Get single collection with cover
     */
    public function getCollectionWithCover($collectionId) {
        $collection = $this->findById($collectionId);
        if ($collection) {
            $collection->cover_image = $this->getCollectionCover($collectionId);
        }
        return $collection;
    }
    
    /**
     * Get collection by slug with cover
     */
    public function getCollectionBySlugWithCover($slug) {
        $collection = $this->findBySlug($slug);
        if ($collection) {
            $collection->cover_image = $this->getCollectionCover($collection->collection_id);
        }
        return $collection;
    }

    /**
     * Lấy tất cả bộ sưu tập với số lượng sản phẩm
     */
    public function getAllWithProductCount($onlyActive = true) {
        $sql = "SELECT c.*, 
                       COALESCE(pc.product_count, 0) as product_count
                FROM {$this->table} c
                LEFT JOIN (
                    SELECT collection_id, COUNT(*) as product_count
                    FROM products
                    WHERE is_active = 1
                    GROUP BY collection_id
                ) pc ON c.collection_id = pc.collection_id";
        
        if ($onlyActive) {
            $sql .= " WHERE c.is_active = 1";
        }
        
        $sql .= " ORDER BY c.collection_name ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Đếm tổng số bộ sưu tập
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }

    /**
     * Tạo bộ sưu tập mới
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (collection_name, slug, description, is_active, created_at) 
                VALUES (:name, :slug, :description, :is_active, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['collection_name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':description', $data['description'] ?? '');
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        
        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    /**
     * Cập nhật bộ sưu tập
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET collection_name = :name, slug = :slug, description = :description, 
                    is_active = :is_active, updated_at = NOW() 
                WHERE collection_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['collection_name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':description', $data['description'] ?? '');
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    /**
     * Xóa bộ sưu tập
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE collection_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}