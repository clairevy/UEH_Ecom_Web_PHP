<?php
/**
 * Collection Model
 * Quản lý bộ sưu tập theo OOP MVC
 */

require_once __DIR__ . '/BaseModel.php';

class CollectionModel extends BaseModel {
    protected $table = 'collection'; // Đúng theo database
    protected $primaryKey = 'collection_id';

    /**
     * Lấy collections với thông tin chi tiết
     */
    public function getCollectionsWithDetails($limit = 10, $offset = 0) {
        // Đơn giản hóa - chỉ lấy từ collection table
        $sql = "SELECT c.*, 'Admin' as created_by_name, 0 as product_count
                FROM collection c
                WHERE c.is_active = 1
                ORDER BY c.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tìm kiếm collections
     */
    public function searchCollections($keyword, $limit = 10, $offset = 0) {
        $sql = "SELECT c.*, 'Admin' as created_by_name, 0 as product_count
                FROM collection c
                WHERE c.is_active = 1 
                AND (c.name LIKE :keyword OR c.description LIKE :keyword)
                ORDER BY c.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        $keyword = "%{$keyword}%";
        $stmt->bindParam(':keyword', $keyword);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tạo collection mới
     */
    public function createCollection($data) {
        $sql = "INSERT INTO collection (name, slug, description, cover_image, is_active, created_by, created_at) 
                VALUES (:name, :slug, :description, :cover_image, :is_active, :created_by, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':cover_image', $data['cover_image']);
        $stmt->bindParam(':is_active', $data['is_active'], PDO::PARAM_INT);
        $stmt->bindParam(':created_by', $data['created_by'], PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật collection
     */
    public function updateCollection($id, $data) {
        $sql = "UPDATE collection 
                SET name = :name, slug = :slug, description = :description, 
                    cover_image = :cover_image, is_active = :is_active, updated_at = NOW()
                WHERE collection_id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':cover_image', $data['cover_image']);
        $stmt->bindParam(':is_active', $data['is_active'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Xóa collection (soft delete)
     */
    public function deleteCollection($id) {
        $sql = "UPDATE collection SET is_active = 0, updated_at = NOW() WHERE collection_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Lấy collection theo slug
     */
    public function getBySlug($slug) {
        $sql = "SELECT * FROM collection WHERE slug = :slug AND is_active = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lấy collections đơn giản (không join với products)
     */
    public function getSimpleCollections($limit = 10, $offset = 0) {
        $sql = "SELECT * FROM collection 
                WHERE is_active = 1 
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
