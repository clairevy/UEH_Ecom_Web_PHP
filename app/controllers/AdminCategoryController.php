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
        // Handle POST request (create new category) - No redirect, just reload view
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            try {
                // Handle file upload
                $imageUrl = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'public/uploads/categories/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $fileName = uniqid('category_') . '_' . $_FILES['image']['name'];
                    $filePath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                        $imageUrl = $filePath;
                    }
                }

                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'] ?? ''),
                    'image_url' => $imageUrl,
                    'icon_url' => trim($_POST['icon_url'] ?? ''),
                    'parent_id' => null,  // Always null for now
                    'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1
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
        
        // Display page (GET or after POST)
        try {
            $categories = $this->categoryModel->getAllWithProductCount(false); // false = show all (including inactive)

            $data = [
                'title' => 'Quản lý danh mục',
                'categories' => $categories,
                'message' => $_SESSION['success'] ?? null,
                'error' => $_SESSION['error'] ?? null
            ];

            // Clear session messages
            unset($_SESSION['success'], $_SESSION['error']);

            $this->view('admin/pages/categories', $data);

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
