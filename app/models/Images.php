<?php
class Images extends BaseModel {
    protected $table = 'images';
    protected $primaryKey = 'image_id';

    // Add a new image record
    public function addImage($data) {
        $this->db->query("INSERT INTO " . $this->table . " (file_name, file_path, file_type, alt_text, created_at) VALUES (:file_name, :file_path, :file_type, :alt_text, NOW())");
        $this->db->bind(':file_name', $data['file_name']);
        $this->db->bind(':file_path', $data['file_path']);
        $this->db->bind(':file_type', $data['file_type']);
        $this->db->bind(':alt_text', $data['alt_text'] ?? null);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId(); // Return the new image_id
        }
        return false;
    }

    // Link an image to an entity (e.g., product, user)
    public function linkImageToEntity($imageData) {
        $this->db->query("INSERT INTO image_usages (image_id, ref_type, ref_id, is_primary, created_at) VALUES (:image_id, :ref_type, :ref_id, :is_primary, NOW())");
        $this->db->bind(':image_id', $imageData['image_id']);
        $this->db->bind(':ref_type', $imageData['ref_type']); // 'product', 'user_avatar', etc.
        $this->db->bind(':ref_id', $imageData['ref_id']);
        $this->db->bind(':is_primary', $imageData['is_primary'] ?? 0);
        return $this->db->execute();
    }

    // Get all images for a specific entity
    public function getImagesForEntity($ref_id, $ref_type) {
        $this->db->query("SELECT i.*, iu.is_primary FROM " . $this->table . " i 
                         JOIN image_usages iu ON i.image_id = iu.image_id 
                         WHERE iu.ref_id = :ref_id AND iu.ref_type = :ref_type 
                         ORDER BY iu.is_primary DESC, i.created_at DESC");
        $this->db->bind(':ref_id', $ref_id);
        $this->db->bind(':ref_type', $ref_type);
        return $this->db->resultSet();
    }

    // Get the primary image for an entity
    public function getPrimaryImageForEntity($ref_id, $ref_type) {
        $this->db->query("SELECT i.* FROM " . $this->table . " i 
                         JOIN image_usages iu ON i.image_id = iu.image_id 
                         WHERE iu.ref_id = :ref_id AND iu.ref_type = :ref_type AND iu.is_primary = 1");
        $this->db->bind(':ref_id', $ref_id);
        $this->db->bind(':ref_type', $ref_type);
        return $this->db->single();
    }

    // Unlink an image from an entity
    public function unlinkImageFromEntity($image_id, $ref_id, $ref_type) {
        $this->db->query("DELETE FROM image_usages WHERE image_id = :image_id AND ref_id = :ref_id AND ref_type = :ref_type");
        $this->db->bind(':image_id', $image_id);
        $this->db->bind(':ref_id', $ref_id);
        $this->db->bind(':ref_type', $ref_type);
        return $this->db->execute();
    }

    // Delete an image and all its usages
    public function deleteImage($image_id) {
        // First, delete all usages
        $this->db->query("DELETE FROM image_usages WHERE image_id = :image_id");
        $this->db->bind(':image_id', $image_id);
        $this->db->execute();

        // Then, delete the image record
        $this->db->query("DELETE FROM " . $this->table . " WHERE image_id = :image_id");
        $this->db->bind(':image_id', $image_id);
        return $this->db->execute();
    }

    // Find image by ID
    public function findById($image_id) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE image_id = :image_id");
        $this->db->bind(':image_id', $image_id);
        return $this->db->single();
    }
}