<?php

/**
 * ProductService - Xử lý Business Logic phức tạp
 * Chịu trách nhiệm cho các logic filter, search, pagination
 */
class ProductService  {
    private $productModel;
    private $siteModel;
    private $categoryModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->siteModel = new SiteAssets();
        $this->categoryModel = new Category();
    }
    
    /**
     * Lấy sản phẩm với filters phức tạp
     */
    public function getProductsWithFilters($filters = []) {
        // Xây dựng SQL động dựa trên filters
        $sql = "SELECT p.*, c.collection_name 
                FROM products p 
                LEFT JOIN collection c ON p.collection_id = c.collection_id 
                WHERE p.is_active = 1";
        
        $params = [];
        
        // Category filter
        if (!empty($filters['category_id'])) {
            $sql .= " AND EXISTS (
                        SELECT 1 FROM product_categories pc 
                        WHERE pc.product_id = p.product_id 
                        AND pc.category_id = :category_id
                      )";
            $params[':category_id'] = $filters['category_id'];
        }
        
        // Price filter
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.base_price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.base_price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        
        // Search filter
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Sorting
        switch ($filters['sort_by'] ?? 'newest') {
            case 'price_low':
                $sql .= " ORDER BY p.base_price ASC";
                break;
            case 'price_high':
                $sql .= " ORDER BY p.base_price DESC";
                break;
            case 'name':
                $sql .= " ORDER BY p.name ASC";
                break;
            default:
                $sql .= " ORDER BY p.created_at DESC";
        }
        
        // Pagination
        $limit = $filters['limit'] ?? 12;
        $offset = $filters['offset'] ?? 0;
        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        
        // Execute query
        $this->productModel->db->query($sql);
        foreach ($params as $key => $value) {
            $this->productModel->db->bind($key, $value);
        }
        
        $products = $this->productModel->db->resultSet();
        
        // Attach images cho mỗi sản phẩm
        foreach ($products as $product) {
            $product->images = $this->siteModel->getProductImages($product->product_id);
            $product->primary_image = $this->siteModel->getProductPrimaryImage($product->product_id);
        }
        
        return $products;
    }
    
    /**
     * Đếm tổng số sản phẩm với filters
     */
    public function countProductsWithFilters($filters = []) {
        // Tương tự getProductsWithFilters nhưng chỉ COUNT
        // Implementation...
    }
    
    /**
     * Lấy sản phẩm với đầy đủ thông tin (images, variants, etc.)
     */
    public function getProductWithFullDetails($productId) {
        $product = $this->productModel->findById($productId);
        
        if (!$product) {
            return null;
        }
        
        // Attach additional data
        $product->images = $this->siteModel->getProductImages($productId);
        $product->primary_image = $this->siteModel->getProductPrimaryImage($productId);
        $product->categories = $this->categoryModel->getCategoriesForProduct($productId);
        
        return $product;
    }
    
    /**
     * Lấy sản phẩm liên quan
     */
    public function getRelatedProducts($productId, $limit = 4) {
        $product = $this->productModel->findById($productId);
        
        if (!$product || !$product->collection_id) {
            return [];
        }
        
        // Lấy sản phẩm cùng collection, trừ sản phẩm hiện tại
        $sql = "SELECT * FROM products 
                WHERE collection_id = :collection_id 
                AND product_id != :product_id 
                AND is_active = 1 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $this->productModel->db->query($sql);
        $this->productModel->db->bind(':collection_id', $product->collection_id);
        $this->productModel->db->bind(':product_id', $productId);
        $this->productModel->db->bind(':limit', $limit);
        
        return $this->productModel->db->resultSet();
    }
}