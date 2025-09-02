<?php
class User extends BaseModel {
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    //Customer registration
    public function signup($userData){
        $errors = [];
        
        // Validate name
        if(empty($userData['name'])) {
            $errors['name'] = 'Vui lòng nhập họ tên';
        } elseif(strlen($userData['name']) < 2) {
            $errors['name'] = 'Họ tên phải có ít nhất 2 ký tự';
        }

        // Validate email
        if(empty($userData['email'])) {
            $errors['email'] = 'Vui lòng nhập địa chỉ email';
        } elseif(!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $userData['email'])) {
            $errors['email'] = 'Vui lòng nhập địa chỉ email hợp lệ';
        } elseif($this->findByEmail($userData['email'])) {
            $errors['email'] = 'Email này đã được đăng ký';
        }

        // Validate password
        if(empty($userData['password'])) {
            $errors['password'] = 'Vui lòng nhập mật khẩu';
        } elseif(strlen($userData['password']) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }

        // Validate confirm password
        if(empty($userData['confirm_password'])) {
            $errors['confirm_password'] = 'Vui lòng xác nhận mật khẩu';
        } elseif($userData['password'] !== $userData['confirm_password']) {
            $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
        }

        // If no errors, create user
        if(empty($errors)) {
            if($this->createNewUser($userData)) {
                return ['success' => true, 'message' => 'Đăng ký thành công'];
            } else {
                return ['success' => false, 'message' => 'Có lỗi xảy ra khi tạo tài khoản'];
            }
        }

        return ['success' => false, 'errors' => $errors];
    }

    //Customer login
    public function signin($email, $password) {
        // Validate input
        if(empty($email)) {
            return ['success' => false, 'message' => 'Vui lòng nhập email'];
        }
        
        if(empty($password)) {
            return ['success' => false, 'message' => 'Vui lòng nhập mật khẩu'];
        }

        // Find user by email
        $user = $this->findByEmail($email);
        
        if(!$user) {
            return ['success' => false, 'message' => 'Email không tồn tại'];
        }

        // Check if user is active
        if(!$user->is_active) {
            return ['success' => false, 'message' => 'Tài khoản đã bị khóa'];
        }

        // Verify password
        if(password_verify($password, $user->password_hash)) {
            // Remove password from user data for security
            unset($user->password_hash);
            
            return [
                'success' => true, 
                'message' => 'Đăng nhập thành công',
                'user' => $user
            ];
        } else {
            return ['success' => false, 'message' => 'Mật khẩu không chính xác'];
        }
    }

    // Create a new user
    public function createNewUser($data) {
        $this->db->query("INSERT INTO " . $this->table . " (email, password_hash, name, phone, is_active, role_id, created_at, updated_at) VALUES (:email, :password_hash, :name, :phone, 1, 2, NOW(), NOW())");
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password_hash', password_hash($data['password'], PASSWORD_BCRYPT));
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':phone', $data['phone'] ?? null);
        return $this->db->execute();
    }

    public function findByEmail($email) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }
    
    public function findById($id) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE user_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
     public function findByPhoneNumber($phone) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE phone = :phone");
        $this->db->bind(':phone', $phone);
        return $this->db->single();
    }
    
     public function deleteById($id) {
        $this->db->query("DELETE FROM " . $this->table . " WHERE user_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getAllUsers() {
        $this->db->query("SELECT user_id, email, name, phone, is_active, created_at FROM " . $this->table . " ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    // Update user profile
    public function updateProfile($userId, $data) {
        $this->db->query("UPDATE " . $this->table . " SET name = :name, phone = :phone, updated_at = NOW() WHERE user_id = :id");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    // Change password
    public function changePassword($userId, $newPassword) {
        $this->db->query("UPDATE " . $this->table . " SET password_hash = :password_hash, updated_at = NOW() WHERE user_id = :id");
        $this->db->bind(':password_hash', password_hash($newPassword, PASSWORD_BCRYPT));
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    // Toggle user active status
    public function toggleActive($userId) {
        $this->db->query("UPDATE " . $this->table . " SET is_active = NOT is_active, updated_at = NOW() WHERE user_id = :id");
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    // Check if email exists (for forgot password feature)
    public function emailExists($email) {
        return $this->findByEmail($email) !== false;
    }
}