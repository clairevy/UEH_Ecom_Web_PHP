<?php
/**
 * AdminCategoryController - Admin Category Management
 * Quản lý danh mục cho admin
 */
class CategoriesController extends BaseController {
    private $categoryService;

    public function __construct() {
        $this->categoryService = new CategoryService();
    }

    /**
     * Danh sách danh mục
     */
    public function index() {
        // Handle POST request (create new category)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            try {
                $categoryId = $this->categoryService->createCategoryWithImage($_POST, $_FILES);
                
                if ($categoryId) {
                    $_SESSION['success'] = 'Tạo danh mục thành công!';
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi tạo danh mục!';
                }
                
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        
        // Display page (GET or after POST)
        try {
            $categories = $this->categoryService->getAllCategoriesWithStats();

            $data = [
                'title' => 'Danh Mục',
                'categories' => $categories,
                'message' => $_SESSION['success'] ?? null,
                'error' => $_SESSION['error'] ?? null
            ];

            // Clear session messages
            unset($_SESSION['success'], $_SESSION['error']);

            // Render admin page with layout
            $this->renderAdminPage('admin/pages/categories', $data);

        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }


    /**
     * Cập nhật danh mục
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if ($this->categoryService->updateCategory($id, $_POST, $_FILES)) {
                    $_SESSION['success'] = 'Cập nhật danh mục thành công!';
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật danh mục!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        
        $this->index();
    }

    /**
     * Xóa danh mục
     */
    public function delete($id) {
        try {
            if ($this->categoryService->deleteCategory($id)) {
                $_SESSION['success'] = 'Xóa danh mục thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa danh mục!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        $this->index();
    }
}
