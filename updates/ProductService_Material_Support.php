<?php

/**
 * Updated ProductService methods for Material Column and Hierarchical Categories
 * 
 * Replace the existing methods in app/services/ProductService.php
 */

/**
 * Get active categories with hierarchical support
 */
public function getActiveCategories() {
    $sql = "SELECT c1.category_id, c1.name, c1.category_type, c1.parent_id, c1.sort_order,
                   c2.name as parent_name
            FROM categories c1 
            LEFT JOIN categories c2 ON c1.parent_id = c2.category_id 
            WHERE c1.is_active = 1 
            ORDER BY COALESCE(c1.parent_id, c1.category_id), c1.sort_order, c1.category_id";
    $this->db->query($sql);
    return $this->db->resultSet();
}

/**
 * Get parent categories (materials) only
 */
public function getParentCategories() {
    $sql = "SELECT category_id, name, category_type, sort_order
            FROM categories 
            WHERE parent_id IS NULL 
            AND is_active = 1 
            ORDER BY sort_order, category_id";
    $this->db->query($sql);
    return $this->db->resultSet();
}

/**
 * Get child categories by parent ID
 */
public function getChildCategories($parentId) {
    $sql = "SELECT category_id, name, category_type, sort_order
            FROM categories 
            WHERE parent_id = :parent_id 
            AND is_active = 1 
            ORDER BY sort_order, category_id";
    $this->db->query($sql);
    $this->db->bind(':parent_id', $parentId);
    return $this->db->resultSet();
}

/**
 * Get available materials from products
 */
public function getAvailableMaterials() {
    $sql = "SELECT DISTINCT material 
            FROM products 
            WHERE material IS NOT NULL 
            AND is_active = 1 
            ORDER BY 
                CASE material 
                    WHEN 'gold' THEN 1 
                    WHEN 'silver' THEN 2 
                    WHEN 'diamond' THEN 3 
                    WHEN 'pearl' THEN 4 
                    ELSE 5 
                END";
    $this->db->query($sql);
    $materials = $this->db->resultSet();
    
    return array_map(function($item) {
        return $item->material;
    }, $materials);
}

/**
 * UPDATED: Get products with filters including material column
 */
public function getProductsWithFilters($filters = [], $page = 1, $limit = 12) {
    // Build dynamic SQL based on filters
    $sql = "SELECT p.*, c.collection_name 
            FROM products p 
            LEFT JOIN collection c ON p.collection_id = c.collection_id 
            WHERE p.is_active = 1";
    
    $params = [];
    
    // Multiple categories filter
    if (!empty($filters['category_ids']) && is_array($filters['category_ids'])) {
        $placeholders = [];
        foreach ($filters['category_ids'] as $index => $categoryId) {
            $placeholder = ":category_id_$index";
            $placeholders[] = $placeholder;
            $params[$placeholder] = $categoryId;
        }
        $categoryPlaceholders = implode(',', $placeholders);
        
        $sql .= " AND EXISTS (
                    SELECT 1 FROM product_categories pc 
                    WHERE pc.product_id = p.product_id 
                    AND pc.category_id IN ($categoryPlaceholders)
                  )";
    }
    // Single category filter (backward compatibility)
    elseif (!empty($filters['category_id'])) {
        $sql .= " AND EXISTS (
                    SELECT 1 FROM product_categories pc 
                    WHERE pc.product_id = p.product_id 
                    AND pc.category_id = :category_id
                  )";
        $params[':category_id'] = $filters['category_id'];
    }
    
    // NEW: Materials filter using material column
    if (!empty($filters['materials']) && is_array($filters['materials'])) {
        $materialPlaceholders = [];
        foreach ($filters['materials'] as $index => $material) {
            $placeholder = ":material_$index";
            $materialPlaceholders[] = $placeholder;
            $params[$placeholder] = $material;
        }
        $materialList = implode(',', $materialPlaceholders);
        $sql .= " AND p.material IN ($materialList)";
    }
    
    // Price filters
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
    
    // Collection filter
    if (!empty($filters['collection_slug'])) {
        $sql .= " AND c.collection_slug = :collection_slug";
        $params[':collection_slug'] = $filters['collection_slug'];
    }
    
    // Sorting
    switch ($filters['sort_by'] ?? 'newest') {
        case 'price_low':
        case 'price_asc':
            $sql .= " ORDER BY p.base_price ASC";
            break;
        case 'price_high':
        case 'price_desc':
            $sql .= " ORDER BY p.base_price DESC";
            break;
        case 'name':
            $sql .= " ORDER BY p.name ASC";
            break;
        case 'popular':
            $sql .= " ORDER BY p.created_at DESC"; // Assume newer = popular for now
            break;
        default:
            $sql .= " ORDER BY p.created_at DESC";
    }
    
    // Pagination
    $offset = ($page - 1) * $limit;
    $sql .= " LIMIT $limit OFFSET $offset";

    // Execute query
    $this->productModel->db->query($sql);
    foreach ($params as $key => $value) {
        $this->productModel->db->bind($key, $value);
    }
    
    $products = $this->productModel->db->resultSet();
    
    // Attach images for each product
    foreach ($products as $product) {
        $product->images = $this->getProductImages($product->product_id);
        $product->primary_image = $this->getProductPrimaryImage($product->product_id);
    }
    
    // Get total count for pagination
    $total = $this->countProductsWithFilters($filters);
    
    return [
        'products' => $products,
        'total' => $total
    ];
}

/**
 * UPDATED: Count products with filters including material column
 */
public function countProductsWithFilters($filters = []) {
    $sql = "SELECT COUNT(*) as total 
            FROM products p 
            LEFT JOIN collection c ON p.collection_id = c.collection_id 
            WHERE p.is_active = 1";
    
    $params = [];
    
    // Multiple categories filter
    if (!empty($filters['category_ids']) && is_array($filters['category_ids'])) {
        $placeholders = [];
        foreach ($filters['category_ids'] as $index => $categoryId) {
            $placeholder = ":category_id_$index";
            $placeholders[] = $placeholder;
            $params[$placeholder] = $categoryId;
        }
        $categoryPlaceholders = implode(',', $placeholders);
        
        $sql .= " AND EXISTS (
                    SELECT 1 FROM product_categories pc 
                    WHERE pc.product_id = p.product_id 
                    AND pc.category_id IN ($categoryPlaceholders)
                  )";
    }
    // Single category filter (backward compatibility)
    elseif (!empty($filters['category_id'])) {
        $sql .= " AND EXISTS (
                    SELECT 1 FROM product_categories pc 
                    WHERE pc.product_id = p.product_id 
                    AND pc.category_id = :category_id
                  )";
        $params[':category_id'] = $filters['category_id'];
    }
    // Category slug filter (backward compatibility)
    elseif (!empty($filters['category_slug'])) {
        $sql .= " AND EXISTS (
                    SELECT 1 FROM product_categories pc 
                    INNER JOIN categories cat ON pc.category_id = cat.category_id 
                    WHERE pc.product_id = p.product_id 
                    AND cat.category_id = :category_slug
                    AND cat.is_active = 1
                  )";
        $params[':category_slug'] = $filters['category_slug'];
    }
    
    // NEW: Materials filter using material column
    if (!empty($filters['materials']) && is_array($filters['materials'])) {
        $materialPlaceholders = [];
        foreach ($filters['materials'] as $index => $material) {
            $placeholder = ":material_$index";
            $materialPlaceholders[] = $placeholder;
            $params[$placeholder] = $material;
        }
        $materialList = implode(',', $materialPlaceholders);
        $sql .= " AND p.material IN ($materialList)";
    }
    
    // Collection filter
    if (!empty($filters['collection_slug'])) {
        $sql .= " AND c.collection_slug = :collection_slug";
        $params[':collection_slug'] = $filters['collection_slug'];
    }
    
    // Price filters
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
    
    // Execute query
    $this->productModel->db->query($sql);
    foreach ($params as $key => $value) {
        $this->productModel->db->bind($key, $value);
    }
    
    $result = $this->productModel->db->single();
    return $result->total ?? 0;
}