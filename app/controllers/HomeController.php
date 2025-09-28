<!-- <?php

class HomeController extends BaseController {
    private $productModel;
    private $categoryModel;
    private $collectionModel;
    private $siteAssetsModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->collectionModel = new Collection();
        $this->siteAssetsModel = new SiteAssets();
    }

    /**
     * Trang chủ - hiển thị sliders, featured products, categories
     */
    public function index() {
        // Lấy dữ liệu cho trang chủ
        $data = [
            'sliders' => $this->siteAssetsModel->getSliders(),
            'banners' => $this->siteAssetsModel->getBanners(),
            'logo' => $this->siteAssetsModel->getLogo(),
            'featuredProducts' => $this->productModel->getAllProductsWithImages(8, 0), // Lấy 8 sản phẩm nổi bật
            'categories' => $this->categoryModel->getAllCategoriesWithBanners(),
            'collections' => $this->collectionModel->getAllCollectionsWithCovers(),
            'pageTitle' => 'JEWELRY - Luxury Collection'
        ];

        // Render view
        $this->view('home', $data);
    }

    /**
     * Trang giới thiệu
     */
    public function about() {
        $data = [
            'pageTitle' => 'About Us - JEWELRY'
        ];
        
        $this->view('about', $data);
    }

    /**
     * Trang liên hệ
     */
    public function contact() {
        $data = [
            'pageTitle' => 'Contact Us - JEWELRY'
        ];
        
        $this->view('contact', $data);
    }

    /**
     * API để search sản phẩm (AJAX)
     */
    public function search() {
        header('Content-Type: application/json');
        
        $query = $_GET['q'] ?? '';
        
        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Search query is required']);
            return;
        }

        // Tìm kiếm sản phẩm theo tên
        $products = $this->productModel->searchProducts($query);
        
        echo json_encode([
            'success' => true,
            'data' => $products,
            'total' => count($products)
        ]);
    }
} -->