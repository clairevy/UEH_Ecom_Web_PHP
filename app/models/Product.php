<?php
class ProductModel extends BaseModel {
    protected $table = 'products';
    protected $primaryKey = 'product_id';

    // Create product with full info
    public function createProduct($data) {
        $this->db->query("INSERT INTO " . $this->table . " 
                         (name, description, base_price, sku, slug, collection_id, is_active, created_at, updated_at) 
                         VALUES (:name, :description, :base_price, :sku, :slug, :collection_id, :is_active, NOW(), NOW())");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':base_price', $data['base_price']);
        $this->db->bind(':sku', $data['sku'] ?? null);
        $this->db->bind(':slug', $data['slug'] ?? $this->generateSlug($data['name']));
        $this->db->bind(':collection_id', $data['collection_id'] ?? null);
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        return $this->db->execute();
    }

    public function createNewProduct($data) {
        $this->db->query("INSERT INTO " . $this->table . " (name, description, base_price, slug, is_active, created_at, updated_at) VALUES (:name, :description, :base_price, :slug, 1, NOW(), NOW())");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':base_price', $data['price']); // Mapping old 'price' to 'base_price'
        $this->db->bind(':slug', $this->generateSlug($data['name']));
        return $this->db->execute();
    }

    public function updateProductInfo($id, $data) {
        $this->db->query("UPDATE " . $this->table . " SET name = :name, description = :description, base_price = :base_price, updated_at = NOW() WHERE product_id = :id");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':base_price', $data['price'] ?? $data['base_price']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Update product with full info
    public function updateProductWithDetails($id, $data) {
        $this->db->query("UPDATE " . $this->table . " 
                         SET name = :name, description = :description, 
                             base_price = :base_price, sku = :sku, 
                             slug = :slug, collection_id = :collection_id,
                             is_active = :is_active, updated_at = NOW() 
                         WHERE product_id = :id");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':base_price', $data['base_price']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':collection_id', $data['collection_id']);
        $this->db->bind(':is_active', $data['is_active']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function findById($id) {
        $this->db->query("SELECT p.*, c.name as collection_name 
                         FROM " . $this->table . " p 
                         LEFT JOIN collection c ON p.collection_id = c.collection_id 
                         WHERE p.product_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function findBySlug($slug) {
        $this->db->query("SELECT p.*, c.name as collection_name 
                         FROM " . $this->table . " p 
                         LEFT JOIN collection c ON p.collection_id = c.collection_id 
                         WHERE p.slug = :slug AND p.is_active = 1");
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    public function deleteById($id) {
        $this->db->query("UPDATE " . $this->table . " SET is_active = 0, updated_at = NOW() WHERE product_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getAllProducts() {
        $this->db->query("SELECT p.*, c.name as collection_name 
                         FROM " . $this->table . " p 
                         LEFT JOIN collection c ON p.collection_id = c.collection_id 
                         WHERE p.is_active = 1 
                         ORDER BY p.created_at DESC");
        return $this->db->resultSet();
    }

    // Search products by name or description
    public function searchProducts($keyword) {
        $this->db->query("SELECT p.*, c.name as collection_name 
                         FROM " . $this->table . " p 
                         LEFT JOIN collection c ON p.collection_id = c.collection_id 
                         WHERE (p.name LIKE :keyword OR p.description LIKE :keyword) 
                         AND p.is_active = 1 
                         ORDER BY p.name ASC");
        $this->db->bind(':keyword', '%' . $keyword . '%');
        return $this->db->resultSet();
    }

    // Get products by collection
    public function getProductsByCollection($collectionId) {
        $this->db->query("SELECT p.*, c.name as collection_name 
                         FROM " . $this->table . " p 
                         LEFT JOIN collection c ON p.collection_id = c.collection_id 
                         WHERE p.collection_id = :collection_id AND p.is_active = 1 
                         ORDER BY p.name ASC");
        $this->db->bind(':collection_id', $collectionId);
        return $this->db->resultSet();
    }

    // Get products by price range
    public function getProductsByPriceRange($minPrice, $maxPrice) {
        $this->db->query("SELECT p.*, c.name as collection_name 
                         FROM " . $this->table . " p 
                         LEFT JOIN collection c ON p.collection_id = c.collection_id 
                         WHERE p.base_price BETWEEN :min_price AND :max_price 
                         AND p.is_active = 1 
                         ORDER BY p.base_price ASC");
        $this->db->bind(':min_price', $minPrice);
        $this->db->bind(':max_price', $maxPrice);
        return $this->db->resultSet();
    }

    // Get products with pagination
    public function getProductsWithPagination($limit = 12, $offset = 0) {
        $this->db->query("SELECT p.*, c.name as collection_name 
                         FROM " . $this->table . " p 
                         LEFT JOIN collection c ON p.collection_id = c.collection_id 
                         WHERE p.is_active = 1 
                         ORDER BY p.created_at DESC 
                         LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    // Count total active products
    public function countProducts() {
        $this->db->query("SELECT COUNT(*) as total FROM " . $this->table . " WHERE is_active = 1");
        $result = $this->db->single();
        return $result->total;
    }

    // Toggle product active status
    public function toggleActive($productId) {
        $this->db->query("UPDATE " . $this->table . " SET is_active = NOT is_active, updated_at = NOW() WHERE product_id = :id");
        $this->db->bind(':id', $productId);
        return $this->db->execute();
    }

    // Generate slug from product name
    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    // Get product variants (if using variants table)
    public function getProductVariants($productId) {
        $this->db->query("SELECT * FROM product_variants WHERE product_id = :product_id ORDER BY variant_id");
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet();
    }

    // Get product images
    public function getProductImages($productId) {
        $this->db->query("SELECT i.* FROM images i 
                         JOIN image_usages iu ON i.image_id = iu.image_id 
                         WHERE iu.entity_type = 'product' AND iu.entity_id = :product_id 
                         ORDER BY iu.sort_order");
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet();
    }
}