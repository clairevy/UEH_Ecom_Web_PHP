<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../helpers/session_helper.php';

class ProfileController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        
        // Check if user is logged in
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /Ecom_website/signin');
            exit;
        }
    }

    /**
     * Show user profile page
     */
    public function index() {
        $userId = SessionHelper::getUserId();
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            header('Location: /Ecom_website/signin');
            exit;
        }

        $this->view('customer/pages/profile', [
            'title' => 'Thông tin cá nhân',
            'user' => $user
        ]);
    }

    /**
     * Update user profile
     */
    public function update() {
        try {
            $userId = SessionHelper::getUserId();
            
            // Validate input
            $errors = $this->validateProfileData($_POST);
            if (!empty($errors)) {
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ', $errors);
                return;
            }

            $updateData = [
                'name' => trim($_POST['name']),
                'phone' => trim($_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'date_of_birth' => $_POST['date_of_birth'] ?? null,
                'gender' => $_POST['gender'] ?? null
            ];

            $updated = $this->userModel->updateProfile($userId, $updateData);
            
            if ($updated) {
                // Update session data
                SessionHelper::updateUserData($updateData);
                $this->jsonResponse(true, 'Cập nhật thông tin thành công!');
            } else {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi cập nhật thông tin');
            }

        } catch (Exception $e) {
            error_log("Profile Update Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar() {
        try {
            $userId = SessionHelper::getUserId();
            
            if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                $this->jsonResponse(false, 'Không có file được upload hoặc có lỗi xảy ra');
                return;
            }

            $file = $_FILES['avatar'];
            
            // Validate file
            $validation = $this->validateImageFile($file);
            if (!$validation['valid']) {
                $this->jsonResponse(false, $validation['message']);
                return;
            }

            // Upload file
            $uploadResult = $this->uploadImageFile($file, 'avatars');
            if (!$uploadResult['success']) {
                $this->jsonResponse(false, $uploadResult['message']);
                return;
            }

            // Update user avatar in database
            $updated = $this->userModel->updateAvatar($userId, $uploadResult['file_path']);
            
            if ($updated) {
                $this->jsonResponse(true, 'Cập nhật avatar thành công!', [
                    'avatar_url' => $uploadResult['file_path']
                ]);
            } else {
                $this->jsonResponse(false, 'Có lỗi khi lưu avatar vào database');
            }

        } catch (Exception $e) {
            error_log("Avatar Upload Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Change password
     */
    public function changePassword() {
        try {
            $userId = SessionHelper::getUserId();
            
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validate passwords
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $this->jsonResponse(false, 'Vui lòng điền đầy đủ thông tin');
                return;
            }

            if ($newPassword !== $confirmPassword) {
                $this->jsonResponse(false, 'Mật khẩu xác nhận không khớp');
                return;
            }

            if (strlen($newPassword) < 6) {
                $this->jsonResponse(false, 'Mật khẩu mới phải có ít nhất 6 ký tự');
                return;
            }

            // Verify current password
            $user = $this->userModel->findById($userId);
            if (!password_verify($currentPassword, $user->password_hash)) {
                $this->jsonResponse(false, 'Mật khẩu hiện tại không đúng');
                return;
            }

            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updated = $this->userModel->updatePassword($userId, $hashedPassword);
            
            if ($updated) {
                $this->jsonResponse(true, 'Đổi mật khẩu thành công!');
            } else {
                $this->jsonResponse(false, 'Có lỗi khi cập nhật mật khẩu');
            }

        } catch (Exception $e) {
            error_log("Change Password Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Validate profile data
     */
    private function validateProfileData($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'Tên là bắt buộc';
        }
        
        if (!empty($data['phone']) && !preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        }
        
        if (!empty($data['date_of_birth']) && !strtotime($data['date_of_birth'])) {
            $errors['date_of_birth'] = 'Ngày sinh không hợp lệ';
        }
        
        return $errors;
    }

    /**
     * Validate image file
     */
    private function validateImageFile($file) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['valid' => false, 'message' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF)'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'message' => 'File ảnh không được vượt quá 5MB'];
        }
        
        return ['valid' => true];
    }

    /**
     * Upload image file
     */
    private function uploadImageFile($file, $folder) {
        try {
            $uploadDir = __DIR__ . '/../../public/uploads/' . $folder . '/';
            
            // Create directory if not exists
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $filePath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                return [
                    'success' => true,
                    'file_path' => '/public/uploads/' . $folder . '/' . $filename
                ];
            } else {
                return ['success' => false, 'message' => 'Không thể upload file'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi khi upload file: ' . $e->getMessage()];
        }
    }

  
}
?>