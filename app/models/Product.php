<?php

class Product extends BaseModel {
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
      $this->db->query("SELECT p.*
                         FROM " . $this->table . " p 
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

    // Get products by price range
    public function getProductsByPriceRange($minPrice, $maxPrice) {
        $this->db->query("SELECT p.*, c.collection_name 
                         FROM " . $this->table . " p 
                         LEFT JOIN collection c ON p.collection_id = c.collection_id 
                         WHERE p.base_price BETWEEN :min_price AND :max_price 
                         AND p.is_active = 1 
                         ORDER BY p.base_price ASC");
        $this->db->bind(':min_price', $minPrice);
        $this->db->bind(':max_price', $maxPrice);
        return $this->db->resultSet();
    }

     // Toggle product active status
    // public function toggleActive($productId) {
    //     $this->db->query("UPDATE " . $this->table . " SET is_active = NOT is_active, updated_at = NOW() WHERE product_id = :id");
    //     $this->db->bind(':id', $productId);
    //     return $this->db->execute();
    // }

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
        $this->db->query("SELECT i.*, iu.usage_id, iu.ref_type, iu.ref_id, iu.is_primary FROM images i 
                         JOIN image_usages iu ON i.image_id = iu.image_id 
                         WHERE iu.ref_type = 'product' AND iu.ref_id = :product_id 
                         ORDER BY iu.is_primary DESC, iu.created_at ASC");
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet();
    }

    // === METHODS FOR PAGINATION AND FILTERING ===

    // Get all products with pagination
    public function getAllProductsPaginated($limit = 12, $offset = 0) {
        $this->db->query("SELECT p.*, c.collection_name
                         FROM " . $this->table . " p 
                         LEFT JOIN collection c ON p.collection_id = c.collection_id 
                         WHERE p.is_active = 1 
                         ORDER BY p.created_at DESC
                         LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    // Get total product count
    public function getTotalProductCount() {
        $this->db->query("SELECT COUNT(*) as count FROM " . $this->table . " WHERE is_active = 1");
        $result = $this->db->single();
        return $result ? (int)$result->count : 0;
    }

    // Get products by category with filters
    public function getProductsByCategory($categoryId, $limit = 12, $offset = 0, $minPrice = null, $maxPrice = null, $sortBy = 'newest') {
        $sql = "SELECT p.*, c.collection_name
                FROM " . $this->table . " p 
                JOIN product_categories pc ON p.product_id = pc.product_id
                LEFT JOIN collection c ON p.collection_id = c.collection_id 
                WHERE pc.category_id = :category_id AND p.is_active = 1";

        // Add price filter
        if ($minPrice !== null) {
            $sql .= " AND p.base_price >= :min_price";
        }
        if ($maxPrice !== null) {
            $sql .= " AND p.base_price <= :max_price";
        }

        // Add sorting
        switch ($sortBy) {
            case 'price_low':
                $sql .= " ORDER BY p.base_price ASC";
                break;
            case 'price_high':
                $sql .= " ORDER BY p.base_price DESC";
                break;
            case 'name':
                $sql .= " ORDER BY p.name ASC";
                break;
            case 'oldest':
                $sql .= " ORDER BY p.created_at ASC";
                break;
            default: // newest
                $sql .= " ORDER BY p.created_at DESC";
        }

        $sql .= " LIMIT :limit OFFSET :offset";

        $this->db->query($sql);
        $this->db->bind(':category_id', $categoryId);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        
        if ($minPrice !== null) {
            $this->db->bind(':min_price', $minPrice);
        }
        if ($maxPrice !== null) {
            $this->db->bind(':max_price', $maxPrice);
        }

        return $this->db->resultSet();
    }

    // Get total products by category
    public function getTotalProductsByCategory($categoryId, $minPrice = null, $maxPrice = null) {
        $sql = "SELECT COUNT(*) as count 
                FROM " . $this->table . " p 
                JOIN product_categories pc ON p.product_id = pc.product_id
                WHERE pc.category_id = :category_id AND p.is_active = 1";

        if ($minPrice !== null) {
            $sql .= " AND p.base_price >= :min_price";
        }
        if ($maxPrice !== null) {
            $sql .= " AND p.base_price <= :max_price";
        }

        $this->db->query($sql);
        $this->db->bind(':category_id', $categoryId);
        
        if ($minPrice !== null) {
            $this->db->bind(':min_price', $minPrice);
        }
        if ($maxPrice !== null) {
            $this->db->bind(':max_price', $maxPrice);
        }

        $result = $this->db->single();
        return $result ? (int)$result->count : 0;
    }

    // Get price range by category
    // public function getPriceRangeByCategory($categoryId) {
    //     $this->db->query("SELECT MIN(p.base_price) as min_price, MAX(p.base_price) as max_price
    //                      FROM " . $this->table . " p 
    //                      JOIN product_categories pc ON p.product_id = pc.product_id
    //                      WHERE pc.category_id = :category_id AND p.is_active = 1");
    //     $this->db->bind(':category_id', $categoryId);
    //     return $this->db->single();
    // }

    // Search products with pagination
    // public function searchProductsPaginated($keyword, $limit = 12, $offset = 0) {
    //     $this->db->query("SELECT p.*, c.collection_name
    //                      FROM " . $this->table . " p 
    //                      LEFT JOIN collection c ON p.collection_id = c.collection_id 
    //                      WHERE (p.name LIKE :keyword OR p.description LIKE :keyword) 
    //                      AND p.is_active = 1 
    //                      ORDER BY p.name ASC
    //                      LIMIT :limit OFFSET :offset");
    //     $this->db->bind(':keyword', '%' . $keyword . '%');
    //     $this->db->bind(':limit', $limit);
    //     $this->db->bind(':offset', $offset);
    //     return $this->db->resultSet();
    // }

    // Get total search results
    // public function getTotalSearchResults($keyword) {
    //     $this->db->query("SELECT COUNT(*) as count FROM " . $this->table . " 
    //                      WHERE (name LIKE :keyword OR description LIKE :keyword) 
    //                      AND is_active = 1");
    //     $this->db->bind(':keyword', '%' . $keyword . '%');
    //     $result = $this->db->single();
    //     return $result ? (int)$result->count : 0;
    // }

    // Get filtered products (without category)
    // public function getFilteredProducts($limit = 12, $offset = 0, $minPrice = null, $maxPrice = null, $sortBy = 'newest') {
    //     $sql = "SELECT p.*, c.collection_name
    //             FROM " . $this->table . " p 
    //             LEFT JOIN collection c ON p.collection_id = c.collection_id 
    //             WHERE p.is_active = 1";

    //     if ($minPrice !== null) {
    //         $sql .= " AND p.base_price >= :min_price";
    //     }
    //     if ($maxPrice !== null) {
    //         $sql .= " AND p.base_price <= :max_price";
    //     }

    //     // Add sorting
    //     switch ($sortBy) {
    //         case 'price_low':
    //             $sql .= " ORDER BY p.base_price ASC";
    //             break;
    //         case 'price_high':
    //             $sql .= " ORDER BY p.base_price DESC";
    //             break;
    //         case 'name':
    //             $sql .= " ORDER BY p.name ASC";
    //             break;
    //         case 'oldest':
    //             $sql .= " ORDER BY p.created_at ASC";
    //             break;
    //         default:
    //             $sql .= " ORDER BY p.created_at DESC";
    //     }

    //     $sql .= " LIMIT :limit OFFSET :offset";

    //     $this->db->query($sql);
    //     $this->db->bind(':limit', $limit);
    //     $this->db->bind(':offset', $offset);
        
    //     if ($minPrice !== null) {
    //         $this->db->bind(':min_price', $minPrice);
    //     }
    //     if ($maxPrice !== null) {
    //         $this->db->bind(':max_price', $maxPrice);
    //     }

    //     return $this->db->resultSet();
    // }

    // Get total filtered products
    public function getTotalFilteredProducts($minPrice = null, $maxPrice = null) {
        $sql = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE is_active = 1";

        if ($minPrice !== null) {
            $sql .= " AND base_price >= :min_price";
        }
        if ($maxPrice !== null) {
            $sql .= " AND base_price <= :max_price";
        }

        $this->db->query($sql);
        
        if ($minPrice !== null) {
            $this->db->bind(':min_price', $minPrice);
        }
        if ($maxPrice !== null) {
            $this->db->bind(':max_price', $maxPrice);
        }

        $result = $this->db->single();
        return $result ? (int)$result->count : 0;
    }

    // Get products by collection with exclusion
    public function getProductsByCollection($collectionId, $limit = 4, $excludeProductId = null) {
        $sql = "SELECT p.*, c.collection_name
                FROM " . $this->table . " p 
                LEFT JOIN collection c ON p.collection_id = c.collection_id 
                WHERE p.collection_id = :collection_id AND p.is_active = 1";

        if ($excludeProductId) {
            $sql .= " AND p.product_id != :exclude_id";
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT :limit";

        $this->db->query($sql);
        $this->db->bind(':collection_id', $collectionId);
        $this->db->bind(':limit', $limit);
        
        if ($excludeProductId) {
            $this->db->bind(':exclude_id', $excludeProductId);
        }

        return $this->db->resultSet();
    }

    // =================== PRODUCT IMAGE METHODS ===================
    
    /**
     * Get primary (first) image for a product
     */
    public function getProductPrimaryImage($productId) {
        $images = $this->getProductImages($productId);
        return $images ? $images[0] : null;
    }
    
    /**
     * Get product by slug
     */
    public function getBySlug($slug) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE slug = :slug AND is_active = 1");
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }
    
    /**
     * Add image to product
     */
    public function addProductImage($productId, $imagePath) {
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
                         VALUES (:image_id, 'product', :ref_id, 0, NOW())");
        $this->db->bind(':image_id', $imageId);
        $this->db->bind(':ref_id', $productId);
        
        return $this->db->execute() ? $imageId : false;
    }
    
    /**
     * Delete a specific product image by usage_id
     */
    public function deleteProductImage($usageId) {
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
     * Delete all images for a product
     */
    public function deleteAllProductImages($productId) {
        $this->db->query("SELECT usage_id FROM image_usages 
                         WHERE ref_type = 'product' AND ref_id = :product_id");
        $this->db->bind(':product_id', $productId);
        $usages = $this->db->resultSet();
        
        foreach ($usages as $usage) {
            $this->deleteProductImage($usage->usage_id);
        }
        
        return true;
    }
    
    /**
     * Get products with their images (for display purposes)
     */
    public function getAllProductsWithImages($limit, $offset) {
        $products = $this->getAllProductsPaginated($limit, $offset);
        
        foreach ($products as $product) {
            $product->images = $this->getProductImages($product->product_id);
            $product->primary_image = $this->getProductPrimaryImage($product->product_id);
        }
        
        return $products;
    }
    
    /**
     * Get single product with images
     */
    public function getProductWithImages($productId) {
        $product = $this->getById($productId);
        if ($product) {
            $product->images = $this->getProductImages($productId);
            $product->primary_image = $this->getProductPrimaryImage($productId);
        }
        return $product;
    }
    
    /**
     * Get product by slug with images
     */
    public function getProductBySlugWithImages($slug) {
        $product = $this->getBySlug($slug);
        if ($product) {
            $product->images = $this->getProductImages($product->product_id);
            $product->primary_image = $this->getProductPrimaryImage($product->product_id);
        }
        return $product;
    }
}