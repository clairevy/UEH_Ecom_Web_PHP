<?php
/**
 * AdminProductController - Admin Product Management
 * Quản lý sản phẩm cho admin
 */
class AdminProductController extends BaseController {
    private $productModel;
    private $categoryModel;
    private $collectionModel;

    public function __construct() {
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->collectionModel = $this->model('Collection');
    }

    /**
     * Danh sách sản phẩm
     */
    public function index() {
        try {
            $products = $this->productModel->getAllWithDetails();
            $categories = $this->categoryModel->getAll();
            $collections = $this->collectionModel->getAll();

            $data = [
                'title' => 'Quản lý sản phẩm',
                'products' => $products,
                'categories' => $categories,
                'collections' => $collections
            ];

            $this->view('admin/pages/products', $data);

        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Tạo sản phẩm mới
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'base_price' => $_POST['base_price'],
                    'sku' => $_POST['sku'],
                    'collection_id' => $_POST['collection_id'] ?? null,
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                $productId = $this->productModel->create($data);
                
                if ($productId) {
                    $_SESSION['success'] = 'Tạo sản phẩm thành công!';
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi tạo sản phẩm!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        
        $this->index();
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'base_price' => $_POST['base_price'],
                    'sku' => $_POST['sku'],
                    'collection_id' => $_POST['collection_id'] ?? null,
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                if ($this->productModel->update($id, $data)) {
                    $_SESSION['success'] = 'Cập nhật sản phẩm thành công!';
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật sản phẩm!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        
        $this->index();
    }

    /**
     * Xóa sản phẩm
     */
    public function delete($id) {
        try {
            if ($this->productModel->delete($id)) {
                $_SESSION['success'] = 'Xóa sản phẩm thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa sản phẩm!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        $this->index();
    }
}
