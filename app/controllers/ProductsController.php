<?php

class ProductsController extends BaseController {
    private $productModel;
    private $categoryModel;

    public function __construct() {
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
    }

    /**
     * Hiển thị tất cả sản phẩm (trang chính)
     */
    public function index() {
        // Lấy parameters từ URL
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12; // 12 sản phẩm mỗi trang
        $offset = ($page - 1) * $limit;

        // Lấy sản phẩm với ảnh
        $products = $this->productModel->getAllProductsWithImages($limit, $offset);
        $totalProducts = $this->productModel->getTotalProductCount();
        $totalPages = ceil($totalProducts / $limit);

        // Lấy categories với banners cho sidebar
        $categories = $this->categoryModel->getAllCategoriesWithBanners();

        $data = [
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ];

        $this->renderView('testview/products/index', $data);
    }

    /**
     * Hiển thị chi tiết sản phẩm
     */
    public function show($slug) {
        if (empty($slug)) {
            $this->redirectTo('/products');
            return;
        }

        $product = $this->productModel->getProductBySlugWithImages($slug);
        
        if (!$product) {
            $this->renderView('errors/404', ['message' => 'Sản phẩm không tồn tại']);
            return;
        }

        // Lấy sản phẩm liên quan cùng collection
        $relatedProducts = [];
        if ($product->collection_id) {
            $relatedProducts = $this->productModel->getProductsByCollection($product->collection_id, 4, $product->product_id);
        }

        // Lấy variants của sản phẩm (nếu có)
        $variants = $this->productModel->getProductVariants($product->product_id);

        // Lấy hình ảnh sản phẩm
        $images = $this->productModel->getProductImages($product->product_id);

        $data = [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'variants' => $variants,
            'images' => $images
        ];

        $this->renderView('testview/products/show', $data);
    }

    /**
     * Hiển thị sản phẩm theo category
     */
    public function category($categorySlug = null) {
        if (empty($categorySlug)) {
            $this->redirectTo('/products');
            return;
        }

        // Lấy thông tin category với banner
        $category = $this->categoryModel->getCategoryBySlugWithBanner($categorySlug);
        if (!$category) {
            $this->renderView('errors/404', ['message' => 'Danh mục không tồn tại']);
            return;
        }

        // Lấy parameters cho filtering và pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

        // Lấy sản phẩm theo category với filters
        $products = $this->productModel->getProductsByCategory(
            $category->category_id, 
            $limit, 
            $offset,
            $minPrice,
            $maxPrice,
            $sortBy
        );

        // Thêm ảnh cho mỗi sản phẩm
        foreach ($products as $product) {
            $product->images = $this->productModel->getProductImages($product->product_id);
            $product->primary_image = $this->productModel->getProductPrimaryImage($product->product_id);
        }

        $totalProducts = $this->productModel->getTotalProductsByCategory($category->category_id, $minPrice, $maxPrice);
        $totalPages = ceil($totalProducts / $limit);

        // Lấy price range cho filter
        $priceRange = $this->productModel->getPriceRangeByCategory($category->category_id);

        // Lấy tất cả categories với banners cho sidebar
        $categories = $this->categoryModel->getAllCategoriesWithBanners();

        $data = [
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'priceRange' => $priceRange,
            'filters' => [
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'sort' => $sortBy
            ]
        ];

        $this->renderView('testview/products/category', $data);
    }

    /**
     * Tìm kiếm sản phẩm
     */
    public function search() {
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        if (empty($keyword)) {
            $this->redirectTo('/products');
            return;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Tìm kiếm sản phẩm
        $products = $this->productModel->searchProductsPaginated($keyword, $limit, $offset);
        
        // Thêm ảnh cho mỗi sản phẩm
        foreach ($products as $product) {
            $product->images = $this->productModel->getProductImages($product->product_id);
            $product->primary_image = $this->productModel->getProductPrimaryImage($product->product_id);
        }
        
        $totalProducts = $this->productModel->getTotalSearchResults($keyword);
        $totalPages = ceil($totalProducts / $limit);

        // Lấy categories với banners cho sidebar
        $categories = $this->categoryModel->getAllCategoriesWithBanners();

        $data = [
            'keyword' => $keyword,
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ];

        $this->renderView('testview/products/search', $data);
    }

    /**
     * Filter sản phẩm (AJAX endpoint)
     */
    public function filter() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $minPrice = isset($_POST['min_price']) ? (float)$_POST['min_price'] : null;
        $maxPrice = isset($_POST['max_price']) ? (float)$_POST['max_price'] : null;
        $sortBy = isset($_POST['sort']) ? $_POST['sort'] : 'newest';
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        if ($categoryId) {
            $products = $this->productModel->getProductsByCategory($categoryId, $limit, $offset, $minPrice, $maxPrice, $sortBy);
            $totalProducts = $this->productModel->getTotalProductsByCategory($categoryId, $minPrice, $maxPrice);
        } else {
            $products = $this->productModel->getFilteredProducts($limit, $offset, $minPrice, $maxPrice, $sortBy);
            $totalProducts = $this->productModel->getTotalFilteredProducts($minPrice, $maxPrice);
        }

        $totalPages = ceil($totalProducts / $limit);

        header('Content-Type: application/json');
        echo json_encode([
            'products' => $products,
            'totalProducts' => $totalProducts,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }

    /**
     * Helper method để redirect
     */
    private function redirectTo($path) {
        header("Location: /Ecom_website{$path}");
        exit();
    }

    // =================== IMAGE MANAGEMENT METHODS ===================
    
    /**
     * Upload ảnh cho sản phẩm
     * URL: /products/uploadImage
     * Method: POST
     */
    public function uploadImage() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $productId = $_POST['product_id'] ?? null;
        
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            return;
        }

        // Check if product exists
        $product = $this->productModel->getById($productId);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            return;
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No image uploaded or upload error']);
            return;
        }

        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF allowed']);
            return;
        }

        // Create upload directory
        $uploadDir = 'public/uploads/products/' . $productId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('product_' . $productId . '_') . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Save to database
            $imageData = [
                'file_path' => $filePath,
                'file_name' => $fileName,
                'alt_text' => $file['name']
            ];
            
            $imageId = $this->productModel->addProductImage($productId, $imageData);
            
            if ($imageId) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Image uploaded successfully',
                    'image_id' => $imageId,
                    'image_path' => $filePath
                ]);
            } else {
                // Delete uploaded file if database save failed
                unlink($filePath);
                echo json_encode(['success' => false, 'message' => 'Failed to save image to database']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
        }
    }

    /**
     * Xóa ảnh sản phẩm
     * URL: /products/deleteImage
     * Method: POST
     */
    public function deleteImage() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $usageId = $_POST['usage_id'] ?? null;
        
        if (!$usageId) {
            echo json_encode(['success' => false, 'message' => 'Usage ID is required']);
            return;
        }

        // Get image path before deleting from database
        $this->productModel->db->query("SELECT i.file_path FROM images i 
                                       JOIN image_usages iu ON i.image_id = iu.image_id 
                                       WHERE iu.usage_id = :usage_id");
        $this->productModel->db->bind(':usage_id', $usageId);
        $imageRecord = $this->productModel->db->single();

        if ($this->productModel->deleteProductImage($usageId)) {
            // Delete physical file if it exists
            if ($imageRecord && file_exists($imageRecord->file_path)) {
                unlink($imageRecord->file_path);
            }
            
            echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete image']);
        }
    }

    /**
     * Lấy tất cả ảnh của sản phẩm
     * URL: /products/getImages/{productId}
     * Method: GET
     */
    public function getImages($productId = null) {
        header('Content-Type: application/json');
        
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            return;
        }

        $images = $this->productModel->getProductImages($productId);
        
        echo json_encode([
            'success' => true,
            'images' => $images
        ]);
    }
}