<?php
require_once __DIR__ . '/../../configs/database.php';

class User extends BaseModel {
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Create a new user
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  SET email = :email,
                      password_hash = :password_hash,
                      name = :name,
                      phone = :phone,
                      verification_token = :verification_token,
                      token_expires_at = :token_expires_at,
                      created_at = NOW()";
        
        $this->db->query($query);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password_hash', $data['password_hash']);
        $this->db->bind(':name', $data['name'] ?? null);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':verification_token', $data['verification_token'] ?? null);
        $this->db->bind(':token_expires_at', $data['token_expires_at'] ?? null);
        
        return $this->db->execute();
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $this->db->query($query);
        $this->db->bind(':email', $email);
        return $this->db->single();
    }
    
    /**
     * Find user by verification token
     */
    public function findByVerificationToken($token) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE verification_token = :token 
                  AND token_expires_at > NOW()
                  LIMIT 1";
        
        $this->db->query($query);
        $this->db->bind(':token', $token);
        return $this->db->single();
    }
    
    /**
     * Verify user email
     */
    public function verifyEmail($userId) {
        $query = "UPDATE " . $this->table . " 
                  SET is_active = 1, 
                      verification_token = NULL, 
                      token_expires_at = NULL 
                  WHERE user_id = :user_id";
        
        $this->db->query($query);
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }
    
    /**
     * Update verification token
     */
    public function updateVerificationToken($email, $token, $expires) {
        $query = "UPDATE " . $this->table . " 
                  SET verification_token = :token, 
                      token_expires_at = :expires 
                  WHERE email = :email";
        
        $this->db->query($query);
        $this->db->bind(':token', $token);
        $this->db->bind(':expires', $expires);
        $this->db->bind(':email', $email);
        return $this->db->execute();
    }
    
    /**
     * Clean expired tokens
     */
    public function cleanExpiredTokens() {
        $query = "UPDATE " . $this->table . " 
                  SET verification_token = NULL, 
                      token_expires_at = NULL 
                  WHERE token_expires_at < NOW() 
                  AND token_expires_at IS NOT NULL";
        
        $this->db->query($query);
        return $this->db->execute();
    }
    
    /**
     * Update reset token for user
     */
    public function updateResetToken($email, $token, $expires) {
        $query = "UPDATE users SET reset_token = :token, reset_expires_at = :expires WHERE email = :email";
        
        $this->db->query($query);
        $this->db->bind(':token', $token);
        $this->db->bind(':expires', $expires);
        $this->db->bind(':email', $email);
        
        return $this->db->execute();
    }
    
    /**
     * Find user by reset token
     */
    public function findByResetToken($token) {
        $query = "SELECT * FROM users WHERE reset_token = :token AND reset_expires_at > NOW()";
        
        error_log("DEBUG findByResetToken - Token: " . $token);
        error_log("DEBUG findByResetToken - Query: " . $query);
        
        $this->db->query($query);
        $this->db->bind(':token', $token);
        
        $result = $this->db->single();
        error_log("DEBUG findByResetToken - Result: " . ($result ? 'FOUND' : 'NOT_FOUND'));
        
        return $result;
    }
    
    /**
     * Reset user password and clear reset token
     */
    public function resetPassword($userId, $hashedPassword) {
        $query = "UPDATE users SET password_hash = :password, verification_token = NULL, token_expires_at = NULL WHERE user_id = :user_id";
        
        $this->db->query($query);
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':user_id', $userId);
        
        return $this->db->execute();
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        $fields = [];
        $params = [':user_id' => $userId];
        
        foreach ($data as $key => $value) {
            if ($value !== null && $value !== '') {
                $fields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = :user_id";
        
        $this->db->query($query);
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        return $this->db->execute();
    }
    
    /**
     * Update user avatar
     */
    public function updateAvatar($userId, $avatarPath) {
        $query = "UPDATE users SET avatar = :avatar WHERE user_id = :user_id";
        
        $this->db->query($query);
        $this->db->bind(':avatar', $avatarPath);
        $this->db->bind(':user_id', $userId);
        
        return $this->db->execute();
    }
    
    /**
     * Update user password
     */
    public function updatePassword($userId, $hashedPassword) {
        $query = "UPDATE users SET password_hash = :password WHERE user_id = :user_id";
        
        $this->db->query($query);
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':user_id', $userId);
        
        return $this->db->execute();
    }
    
    /**
     * Find user by ID
     */
    public function findById($userId) {
        $query = "SELECT * FROM users WHERE user_id = :user_id";
        
        $this->db->query($query);
        $this->db->bind(':user_id', $userId);
        
        return $this->db->single();
    }
    
    /**
     * Check if token is valid
     */
    public function isTokenValid($expires) {
        return strtotime($expires) >= time();
    }
}