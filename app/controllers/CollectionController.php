<?php
/**
 * CollectionController - Admin Collection Management
 */
class AdminCollectionController extends BaseController {
    private $collectionModel;

    public function __construct() {
        $this->collectionModel = new Collection();
    }

    public function index() {
        try {
            $collections = $this->collectionModel->getAllWithProductCount();

            $data = [
                'title' => 'Quản lý bộ sưu tập',
                'collections' => $collections
            ];

            $this->view('admin/pages/collections', $data);

        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'] ?? '',
                    'cover_image' => $_POST['cover_image'] ?? '',
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                $collectionId = $this->collectionModel->create($data);
                
                if ($collectionId) {
                    $_SESSION['success'] = 'Tạo bộ sưu tập thành công!';
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi tạo bộ sưu tập!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        
        $this->index();
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'] ?? '',
                    'cover_image' => $_POST['cover_image'] ?? '',
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                if ($this->collectionModel->update($id, $data)) {
                    $_SESSION['success'] = 'Cập nhật bộ sưu tập thành công!';
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật bộ sưu tập!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        
        $this->index();
    }

    public function delete($id) {
        try {
            if ($this->collectionModel->delete($id)) {
                $_SESSION['success'] = 'Xóa bộ sưu tập thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa bộ sưu tập!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        $this->index();
    }
}
