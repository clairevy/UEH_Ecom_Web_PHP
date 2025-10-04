<?php
/**
 * User Model
 * Quản lý người dùng
 */

require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel {
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    /**
     * Lấy người dùng với thông tin role
     */
    public function getUsersWithRoles($limit = 10, $offset = 0) {
        $sql = "SELECT u.*, r.role_name
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.role_id
                ORDER BY u.created_at DESC
                LIMIT {$limit} OFFSET {$offset}";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Thống kê người dùng
     */
    public function getUserStats() {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_users,
                    COUNT(CASE WHEN is_active = 0 THEN 1 END) as inactive_users
                FROM users";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
