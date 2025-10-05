<?php
/**
 * AdminCategoryController - Admin Category Management
 * Quản lý danh mục cho admin
 */
class AdminCategoryController extends BaseController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new Category();
    }

    /**
     * Danh sách danh mục
     */
    public function index() {
        try {
            $categories = $this->categoryModel->getAllWithProductCount();

            $data = [
                'title' => 'Quản lý danh mục',
                'categories' => $categories
            ];

            $this->view('admin/pages/categories', $data);

        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Tạo danh mục mới
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'] ?? '',
                    'image_url' => $_POST['image_url'] ?? '',
                    'icon_url' => $_POST['icon_url'] ?? '',
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                $categoryId = $this->categoryModel->create($data);
                
                if ($categoryId) {
                    $_SESSION['success'] = 'Tạo danh mục thành công!';
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi tạo danh mục!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        
        $this->index();
    }

    /**
     * Cập nhật danh mục
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'] ?? '',
                    'image_url' => $_POST['image_url'] ?? '',
                    'icon_url' => $_POST['icon_url'] ?? '',
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                if ($this->categoryModel->update($id, $data)) {
                    $_SESSION['success'] = 'Cập nhật danh mục thành công!';
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật danh mục!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        
        $this->index();
    }

    /**
     * Xóa danh mục
     */
    public function delete($id) {
        try {
            if ($this->categoryModel->delete($id)) {
                $_SESSION['success'] = 'Xóa danh mục thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa danh mục!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        $this->index();
    }
}
