<?php

class CustomerController extends BaseController {
    private $productModel;
    private $categoryModel;
    private $collectionModel;
    private $siteAssetsModel;
    private $reviewModel;

    public function __construct() {
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->collectionModel = $this->model('Collection');
        $this->siteAssetsModel = $this->model('SiteAssets');
        $this->reviewModel = $this->model('Review');
    }

    /**
     * Trang chủ customer - hiển thị sliders, new arrivals, popular products, categories
     */
    public function index() {
        // Lấy dữ liệu cho trang chủ
        $data = [
            'sliders' => $this->siteAssetsModel->getSliders(),
            'banners' => $this->siteAssetsModel->getBanners(),
            'logo' => $this->siteAssetsModel->getLogo(),
            'newArrivals' => $this->productModel->getNewArrivals(8), // 8 sản phẩm mới nhất
            'popularProducts' => $this->productModel->getPopularProducts(8), // 8 sản phẩm phổ biến nhất
            'categories' => $this->categoryModel->getAllCategoriesWithBanners(),
            'collections' => $this->collectionModel->getAllCollectionsWithCovers(),
            'pageTitle' => 'JEWELRY - Luxury Collection'
        ];

        // Render view
        $this->view('customer/pages/home', $data);
    }

    /**
     * Trang danh sách sản phẩm với filters
     */
    public function products() {
        // Lấy parameters từ URL
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12; // 12 sản phẩm mỗi trang
        $offset = ($page - 1) * $limit;
        
        $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
        $search = $_GET['search'] ?? '';

        // Lấy sản phẩm với filters
        if (!empty($search)) {
            $products = $this->productModel->searchProductsPaginated($search, $limit, $offset);
            $totalProducts = $this->productModel->getTotalSearchResults($search);
        } elseif ($categoryId) {
            $products = $this->productModel->getProductsByCategory($categoryId, $limit, $offset, $minPrice, $maxPrice, $sortBy);
            $totalProducts = $this->productModel->getTotalProductsByCategory($categoryId, $minPrice, $maxPrice);
        } else {
            $products = $this->productModel->getFilteredProducts($limit, $offset, $minPrice, $maxPrice, $sortBy);
            $totalProducts = $this->productModel->getTotalFilteredProducts($minPrice, $maxPrice);
        }
        
        // Thêm ảnh cho mỗi sản phẩm
        foreach ($products as $product) {
            $product->images = $this->productModel->getProductImages($product->product_id);
            $product->primary_image = $this->productModel->getProductPrimaryImage($product->product_id);
        }

        $totalPages = ceil($totalProducts / $limit);

        // Lấy categories cho sidebar
        $categories = $this->categoryModel->getAllCategoriesWithBanners();

        // Lấy price range
        $priceRange = $this->productModel->getPriceRange();

        $data = [
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'priceRange' => $priceRange,
            'filters' => [
                'category' => $categoryId,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'sort' => $sortBy,
                'search' => $search
            ],
            'pageTitle' => !empty($search) ? "Tìm kiếm '{$search}'" : 'Danh sách sản phẩm - JEWELRY'
        ];

        $this->view('customer/pages/list-product', $data);
    }

    /**
     * Chi tiết sản phẩm
     */
    public function productDetail($productId = null) {
        if (empty($productId)) {
            $this->view('404', ['message' => 'Không tìm thấy sản phẩm']);
            return;
        }

        $product = $this->productModel->getProductWithImages($productId);
        
        if (!$product) {
            $this->view('404', ['message' => 'Sản phẩm không tồn tại']);
            return;
        }

        // Lấy sản phẩm liên quan cùng collection
        $relatedProducts = [];
        if ($product->collection_id) {
            $relatedProducts = $this->productModel->getProductsByCollection($product->collection_id, 4, $product->product_id);
            // Thêm ảnh cho sản phẩm liên quan
            foreach ($relatedProducts as $relatedProduct) {
                $relatedProduct->primary_image = $this->productModel->getProductPrimaryImage($relatedProduct->product_id);
            }
        }

        // Lấy variants của sản phẩm (nếu có)
        $variants = $this->productModel->getProductVariants($product->product_id);

        // Lấy reviews của sản phẩm (nếu có model Review)
        $reviews = [];
        $reviewStats = (object)['average_rating' => 4.5, 'total_reviews' => rand(50, 200)];
        
        if (class_exists('Review')) {
            $reviews = $this->reviewModel->getProductReviews($product->product_id, 10);
            $reviewStats = $this->reviewModel->getProductRatingStats($product->product_id);
        }

        // Lấy categories của sản phẩm cho breadcrumb
        $productCategories = $this->categoryModel->getCategoriesForProduct($product->product_id);

        $data = [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'variants' => $variants,
            'reviews' => $reviews,
            'reviewStats' => $reviewStats,
            'productCategories' => $productCategories,
            'pageTitle' => $product->name . ' - JEWELRY'
        ];

        $this->view('customer/pages/details-product', $data);
    }

    /**
     * API để search sản phẩm (AJAX)
     */
    public function searchApi() {
        header('Content-Type: application/json');
        
        $query = $_GET['q'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
        
        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Search query is required']);
            return;
        }

        // Tìm kiếm sản phẩm theo tên
        $products = $this->productModel->searchProducts($query);
        
        // Giới hạn số lượng kết quả
        $products = array_slice($products, 0, $limit);
        
        // Thêm ảnh cho mỗi sản phẩm
        foreach ($products as $product) {
            $product->primary_image = $this->productModel->getProductPrimaryImage($product->product_id);
        }
        
        echo json_encode([
            'success' => true,
            'data' => $products,
            'total' => count($products)
        ]);
    }

    /**
     * API để lấy sản phẩm theo category (AJAX)
     */
    public function getProductsByCategory() {
        header('Content-Type: application/json');
        
        $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 12;
        $offset = ($page - 1) * $limit;
        
        if (!$categoryId) {
            echo json_encode(['success' => false, 'message' => 'Category ID is required']);
            return;
        }

        $products = $this->productModel->getProductsByCategory($categoryId, $limit, $offset);
        $totalProducts = $this->productModel->getTotalProductsByCategory($categoryId);
        
        // Thêm ảnh cho mỗi sản phẩm
        foreach ($products as $product) {
            $product->primary_image = $this->productModel->getProductPrimaryImage($product->product_id);
        }

        echo json_encode([
            'success' => true,
            'products' => $products,
            'totalProducts' => $totalProducts,
            'totalPages' => ceil($totalProducts / $limit),
            'currentPage' => $page
        ]);
    }

    /**
     * API để lấy new arrivals (AJAX)
     */
    public function getNewArrivals() {
        header('Content-Type: application/json');
        
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
        $products = $this->productModel->getNewArrivals($limit);
        
        // Thêm ảnh cho mỗi sản phẩm
        foreach ($products as $product) {
            $product->primary_image = $this->productModel->getProductPrimaryImage($product->product_id);
        }
        
        echo json_encode([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * API để lấy popular products (AJAX)
     */
    public function getPopularProducts() {
        header('Content-Type: application/json');
        
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
        $products = $this->productModel->getPopularProducts($limit);
        
        // Thêm ảnh cho mỗi sản phẩm
        foreach ($products as $product) {
            $product->primary_image = $this->productModel->getProductPrimaryImage($product->product_id);
        }
        
        echo json_encode([
            'success' => true,
            'data' => $products
        ]);
    }
}
