<?php
/**
 * Product Controller
 * Xử lý quản lý sản phẩm
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';

class ProductController extends BaseController {
    private $productModel;
    private $categoryModel;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Hiển thị danh sách sản phẩm
     */
    public function index() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;
            $keyword = $_GET['search'] ?? '';
            $category_id = $_GET['category'] ?? null;

            $products = $this->productModel->searchProducts($keyword, $category_id, $limit, $offset);
            $categories = $this->categoryModel->getAll();
            $totalProducts = $this->productModel->count();
            $totalPages = ceil($totalProducts / $limit);

            $this->data = [
                'pageTitle' => 'JEWELLERY Admin - Sản phẩm',
                'currentPage' => 'products',
                'baseUrl' => '../',
                'products' => $products,
                'categories' => $categories,
                'currentPageNum' => $page,
                'totalPages' => $totalPages,
                'keyword' => $keyword,
                'selectedCategory' => $category_id
            ];

            $this->render('products/index');
        } catch (Exception $e) {
            $this->data = [
                'pageTitle' => 'JEWELLERY Admin - Sản phẩm',
                'currentPage' => 'products',
                'baseUrl' => '../',
                'error' => $e->getMessage()
            ];
            $this->render('products/index');
        }
    }

    /**
     * Xóa sản phẩm (AJAX)
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'] ?? null;
            
            if ($product_id) {
                try {
                    $result = $this->productModel->delete($product_id);
                    $this->jsonResponse(['success' => true, 'message' => 'Xóa sản phẩm thành công']);
                } catch (Exception $e) {
                    $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
                }
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'ID sản phẩm không hợp lệ'], 400);
            }
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Method không được phép'], 405);
        }
    }
}
?>
