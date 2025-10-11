<?php
/**
 * Session Helper - Quản lý session người dùng
 */
class SessionHelper {
    
    /**
     * Bắt đầu session nếu chưa có
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Kiểm tra user đã đăng nhập hay chưa
     */
    public static function isLoggedIn() {
        self::start();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Lấy thông tin user từ session
     */
    public static function getUser() {
        self::start();
        if (self::isLoggedIn()) {
            return (object) [
                'user_id' => $_SESSION['user_id'] ?? null,
                'email' => $_SESSION['user_email'] ?? null,
                'name' => $_SESSION['user_name'] ?? null,
                'role_id' => $_SESSION['role_id'] ?? null
            ];
        }
        return null;
    }
    
    /**
     * Lấy ID của user
     */
    public static function getUserId() {
        $user = self::getUser();
        return $user ? $user->user_id : null;
    }
    
    /**
     * Lấy tên của user
     */
    public static function getUserName() {
        $user = self::getUser();
        return $user ? $user->name : null;
    }
    
    /**
     * Lấy email của user
     */
    public static function getUserEmail() {
        $user = self::getUser();
        return $user ? $user->email : null;
    }
    
    /**
     * Tạo session cho user
     */
    public static function createUserSession($userData) {
        self::start();
        $_SESSION['user_id'] = $userData->user_id;
        $_SESSION['user_email'] = $userData->email;
        $_SESSION['user_name'] = $userData->name ?? '';
        $_SESSION['role_id'] = $userData->role_id ?? 1;
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
    }
    
    /**
     * Xóa session của user
     */
    public static function destroyUserSession() {
        self::start();
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        unset($_SESSION['role_id']);
        unset($_SESSION['logged_in']);
        unset($_SESSION['login_time']);
        
        // Xóa toàn bộ session
        session_destroy();
    }
    
    /**
     * Làm mới thời gian session
     */
    public static function refreshSession() {
        self::start();
        if (self::isLoggedIn()) {
            $_SESSION['last_activity'] = time();
        }
    }
    
    /**
     * Kiểm tra session có hết hạn hay không (30 phút không hoạt động)
     */
    public static function isSessionExpired() {
        self::start();
        if (!self::isLoggedIn()) {
            return true;
        }
        
        $lastActivity = $_SESSION['last_activity'] ?? $_SESSION['login_time'] ?? 0;
        $sessionTimeout = 30 * 60; // 30 phút
        
        return (time() - $lastActivity) > $sessionTimeout;
    }
    
    /**
     * Lấy avatar URL (placeholder cho tương lai)
     */
    public static function getAvatarUrl() {
        $user = self::getUser();
        if ($user) {
            // Tạm thời sử dụng avatar mặc định, sau này có thể thêm avatar từ database
            return "https://ui-avatars.com/api/?name=" . urlencode($user->name) . "&background=667eea&color=fff&size=40";
        }
        return null;
    }
}
?>