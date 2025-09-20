<?php

class UsersController extends BaseController {
    // Chúng ta sẽ dùng User Model rất nhiều, nên hãy khởi tạo nó ngay từ đầu
    private $userModel;

    public function __construct() {
        // Dòng này sẽ tạo một đối tượng User Model và gán nó vào biến $userModel
        $this->userModel = $this->model('User');
    }

    /**
     * Phương thức xử lý trang và logic Đăng ký
     */
    public function signup() {
        // Trường hợp 1: Người dùng gửi dữ liệu lên (nhấn nút submit)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Lấy và làm sạch dữ liệu
        $data = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'password' => trim($_POST['password']),
            'confirm_password' => trim($_POST['confirm_password']),
            'errors' => []
        ];

        // --- Validate dữ liệu ---
        if (empty($data['name'])) $data['errors']['name'] = 'Vui lòng nhập tên.';
        if ($this->userModel->findByEmail($data['email'])) {
            $data['errors']['email'] = 'Email đã tồn tại.';
        }
        if (strlen($data['password']) < 6) {
            $data['errors']['password'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        }
        if ($data['password'] !== $data['confirm_password']) {
            $data['errors']['confirm_password'] = 'Mật khẩu không khớp.';
        }
        // --- Kết thúc Validate ---

        // Nếu không có lỗi nào
        if (empty($data['errors'])) {
            // Gọi model để đăng ký (không hash ở đây vì model sẽ hash)
            $result = $this->userModel->signup($data);
            
            if ($result['success']) {
                // Chuyển hướng tới trang đăng nhập với đường dẫn đúng
                header('location: /Ecom_website/users/login');
                exit(); // Quan trọng: dừng script sau khi redirect
            } else {
                // Nếu có lỗi từ model, hiển thị lỗi
                if (isset($result['errors'])) {
                    $data['errors'] = array_merge($data['errors'], $result['errors']);
                }
                $data['error_message'] = $result['message'] ?? 'Đã có lỗi xảy ra.';
                $this->renderView('testview/signup', $data);
            }
        } else {
            // Nếu có lỗi, tải lại view và hiển thị lỗi
            $this->renderView('testview/signup', $data);
        }
    } else {
        // Trường hợp 2: Người dùng chỉ truy cập trang, hiển thị form trống
        $this->renderView('testview/signup');
    }
}

    /**
     * Phương thức xử lý trang và logic Đăng nhập
     */
 public function login() {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'email' => trim($_POST['email']),
            'password' => trim($_POST['password']),
            'errors' => []
        ];

        // Validate... (bạn có thể tự thêm)

        // Gọi model để xử lý đăng nhập
        $loggedInUser = $this->userModel->login($data['email'], $data['password']);

        if ($loggedInUser) {
            // Đăng nhập thành công, tạo session
            $this->createUserSession($loggedInUser);
            header('location: /Ecom_website'); // Chuyển về trang chủ
        } else {
            // Đăng nhập thất bại, báo lỗi
            $data['errors']['general'] = 'Email hoặc mật khẩu không đúng.';
            $this->renderView('testview/login', $data);
        }
    } else {
        $this->renderView('testview/login');
    }
}

// Phương thức trợ giúp để tạo session
public function createUserSession($user) {
    $_SESSION['user_id'] = $user['user_id']; // Lấy từ mảng dữ liệu user
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
}
    /**
     * Phương thức xử lý logic Đăng xuất
     */
    public function logout() {
        // Sẽ viết code ở đây
    }
}