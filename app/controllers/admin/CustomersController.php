<?php
/**
 * CustomersController - Admin Customer Management
 * Quản lý khách hàng cho admin
 */
class CustomersController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    /**
     * Hiển thị danh sách khách hàng
     */
    public function index() {
        try {
            // Lấy danh sách users với thống kê đơn hàng
            $users = $this->userModel->getAllWithOrderCount(false);
            
            // Tính toán thống kê
            $totalUsers = $this->userModel->getTotalCount();
            $activeUsers = $this->userModel->getActiveCount();
            $usersWithOrders = $this->userModel->getUsersWithOrdersCount();
            $newUsersThisMonth = $this->userModel->getNewUsersThisMonthCount();
            
            $stats = [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'with_orders' => $usersWithOrders,
                'new_this_month' => $newUsersThisMonth
            ];

            $data = [
                'title' => 'Khách Hàng',
                'users' => $users,
                'stats' => $stats,
                'message' => $_SESSION['success'] ?? null,
                'error' => $_SESSION['error'] ?? null
            ];

            // Clear flash messages
            unset($_SESSION['success'], $_SESSION['error']);

            // Render admin page with layout
            $this->renderAdminPage('admin/pages/customers', $data);
        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Tạo khách hàng mới
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => trim($_POST['name']),
                    'email' => trim($_POST['email']),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'password' => trim($_POST['password']),
                    'confirm_password' => trim($_POST['confirm_password']),
                    'role_id' => 1 // Customer role
                ];

                $result = $this->userModel->signup($data);
                
                if ($result['success']) {
                    $_SESSION['success'] = 'Tạo khách hàng thành công!';
                } else {
                    $_SESSION['error'] = $result['message'] ?? 'Có lỗi xảy ra khi tạo khách hàng!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        
        $this->index();
    }

    /**
     * Cập nhật khách hàng
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => trim($_POST['name']),
                    'phone' => trim($_POST['phone'] ?? '')
                ];

                if ($this->userModel->updateProfile($id, $data)) {
                    $_SESSION['success'] = 'Cập nhật khách hàng thành công!';
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật khách hàng!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        
        $this->index();
    }

    /**
     * Xóa khách hàng
     */
    public function delete($id) {
        try {
            if ($this->userModel->delete($id)) {
                $_SESSION['success'] = 'Xóa khách hàng thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa khách hàng!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        $this->index();
    }

    /**
     * Toggle trạng thái active của khách hàng
     */
    public function toggle($id) {
        try {
            if ($this->userModel->toggleActive($id)) {
                $_SESSION['success'] = 'Cập nhật trạng thái khách hàng thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật trạng thái!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        $this->index();
    }

    /**
     * Xem chi tiết khách hàng
     */
    public function show($id) {
        try {
            $user = $this->userModel->getById($id);
            
            if (!$user) {
                $_SESSION['error'] = 'Không tìm thấy khách hàng!';
                $this->index();
                return;
            }

            $data = [
                'title' => 'Chi tiết khách hàng',
                'user' => $user
            ];
            
            // Load customer-details content with data
            ob_start();
            include __DIR__ . '/../../views/admin/pages/customer-details.php';
            $content = ob_get_clean();
            $data['content'] = $content;
            extract($data);
            include __DIR__ . '/../../views/admin/layouts/main.php';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            $this->index();
        }
    }
}