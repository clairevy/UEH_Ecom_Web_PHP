<?php

/**
 * CustomerController (Refactored) - Sử dụng Service Layer
 */
class CustomerControllerRefactored extends BaseController {
    private $productService;
    
    public function __construct() {
        $this->productService = new ProductService();
    }
    
    /**
     * Trang danh sách sản phẩm - CLEAN VERSION
     */
    public function products() {
        // 1. Parse filters từ URL
        $filters = $this->parseFilters();
        
        // 2. Gọi Service để lấy data
        $products = $this->productService->getProductsWithFilters($filters);
        $totalProducts = $this->productService->countProductsWithFilters($filters);
        
        // 3. Tính pagination
        $totalPages = ceil($totalProducts / $filters['limit']);
        
        // 4. Chuẩn bị data cho View
        $data = [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'currentPage' => $filters['page'],
            'totalPages' => $totalPages,
            'filters' => $filters,
            'pageTitle' => $this->generatePageTitle($filters)
        ];
        
        // 5. Render View
        $this->view('customer/pages/list-product', $data);
    }
    
    /**
     * Chi tiết sản phẩm - CLEAN VERSION  
     */
    public function productDetail($productId) {
        if (empty($productId)) {
            $this->view('404', ['message' => 'Không tìm thấy sản phẩm']);
            return;
        }
        
        // 1. Lấy sản phẩm với đầy đủ thông tin
        $product = $this->productService->getProductWithFullDetails($productId);
        
        if (!$product) {
            $this->view('404', ['message' => 'Sản phẩm không tồn tại']);
            return;
        }
        
        // 2. Lấy sản phẩm liên quan
        $relatedProducts = $this->productService->getRelatedProducts($productId);
        
        // 3. Chuẩn bị data cho View
        $data = [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'pageTitle' => $product->name . ' - JEWELRY'
        ];
        
        // 4. Render View
        $this->view('customer/pages/details-product', $data);
    }
    
    // ============= HELPER METHODS =============
    
    /**
     * Parse filters từ $_GET parameters
     */
    private function parseFilters() {
        return [
            'category_id' => $_GET['category'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'search' => $_GET['search'] ?? '',
            'sort_by' => $_GET['sort'] ?? 'newest',
            'page' => $_GET['page'] ?? 1,
            'limit' => 12,
            'offset' => (($_GET['page'] ?? 1) - 1) * 12
        ];
    }
    
    /**
     * Tạo page title dựa trên filters
     */
    private function generatePageTitle($filters) {
        if (!empty($filters['search'])) {
            return "Tìm kiếm '{$filters['search']}' - JEWELRY";
        }
        
        return 'Danh sách sản phẩm - JEWELRY';
    }
}