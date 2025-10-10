<?php
/**
 * CollectionController - Admin Collection Management
 */
class CollectionsController extends BaseController {
    private $collectionModel;

    public function __construct() {
        $this->collectionModel = $this->model('Collection');
    }

    public function index() {
        try {
            $collections = $this->collectionModel->getAllWithProductCount(false); // false = show all
            
            // Calculate statistics
            $total = count($collections);
            $active = 0;
            $totalProducts = 0;
            
            foreach ($collections as $collection) {
                if ($collection->is_active) {
                    $active++;
                }
                $totalProducts += ($collection->product_count ?? 0);
            }
            
            $stats = [
                'total' => $total,
                'active' => $active,
                'total_products' => $totalProducts,
                'best_sellers' => 3 // TODO: Calculate from sales data
            ];

            $data = [
                'title' => 'Bộ Sưu Tập',
                'collections' => $collections,
                'stats' => $stats,
                'message' => $_SESSION['success'] ?? null,
                'error' => $_SESSION['error'] ?? null
            ];

            // Clear session messages
            unset($_SESSION['success'], $_SESSION['error']);

            // Render admin page with layout
            $this->renderAdminPage('admin/pages/collections', $data);

        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Generate slug from name
                $slug = $this->generateSlug(trim($_POST['name']));
                
                $data = [
                    'collection_name' => trim($_POST['name']),
                    'slug' => $slug,
                    'description' => trim($_POST['description'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1
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
    
    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Generate slug from name
                $slug = $this->generateSlug(trim($_POST['name']));
                
                $data = [
                    'collection_name' => trim($_POST['name']),
                    'slug' => $slug,
                    'description' => trim($_POST['description'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1
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
