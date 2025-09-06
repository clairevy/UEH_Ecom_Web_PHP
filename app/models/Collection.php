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
}