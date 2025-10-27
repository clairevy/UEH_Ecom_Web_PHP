<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../helpers/email_helper.php';
require_once __DIR__ . '/../../helpers/session_helper.php';

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Show sign up form
     */
    public function showSignUp() {
        $this->view('auth/signup');
    }

    /**
     * Show sign in form  
     */
    public function showSignIn() {
        $this->view('auth/signin');
    }

    /**
     * Handle sign up registration
     */
    public function signUp() {
        try {
            // Debug log
            error_log("SignUp method called");
            error_log("POST data: " . print_r($_POST, true));
            
            // Validate input
            $errors = $this->validateSignUpData($_POST);
            if (!empty($errors)) {
                error_log("Validation errors: " . print_r($errors, true));
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ', $errors);
                return;
            }

            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            // Check if email already exists
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser) {
                $this->jsonResponse(false, 'Email này đã được sử dụng');
                return;
            }

            // Generate verification token
            $verificationToken = $this->generateVerificationToken();
            $tokenExpires = date('Y-m-d H:i:s', strtotime('+' . VERIFICATION_EXPIRE_MINUTES . ' minutes'));

            // Create user
            $userData = [
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'name' => $name,
                'phone' => $phone,
                'verification_token' => $verificationToken,
                'token_expires_at' => $tokenExpires
            ];

            $created = $this->userModel->create($userData);
            
            if (!$created) {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi tạo tài khoản');
                return;
            }

            // Send verification email
            error_log("Attempting to send verification email to: " . $email);
            $emailSent = EmailHelper::sendVerificationEmail($email, $verificationToken);
            error_log("Email sent result: " . ($emailSent ? 'true' : 'false'));
            
            if (!$emailSent) {
                // User created but email failed - still success but with warning
                error_log("Email failed but user was created successfully");
                $this->jsonResponse(true, 'Tài khoản được tạo thành công nhưng không thể gửi email xác thực. Vui lòng liên hệ admin.');
                return;
            }

            error_log("User created and email sent successfully");
            $this->jsonResponse(true, 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.');

        } catch (Exception $e) {
            error_log("SignUp Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Handle sign in authentication
     */
    public function signIn() {
        try {
            // Validate input
            $errors = $this->validateSignInData($_POST);
            if (!empty($errors)) {
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ', $errors);
                return;
            }

            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // Find user by email
            $user = $this->userModel->findByEmail($email);
            
            if (!$user) {
                $this->jsonResponse(false, 'Email hoặc mật khẩu không đúng');
                return;
            }

            // Verify password
            if (!password_verify($password, $user->password_hash)) {
                $this->jsonResponse(false, 'Email hoặc mật khẩu không đúng');
                return;
            }

            // Check if email is verified
            if (!$user->is_active) {
                $this->jsonResponse(false, 'Tài khoản chưa được xác thực email. Vui lòng kiểm tra email của bạn.');
                return;
            }

            // Create session
            $this->createUserSession($user);

            $this->jsonResponse(true, 'Đăng nhập thành công', [
                'redirect' => '/Ecom_website/'
            ]);

        } catch (Exception $e) {
            error_log("SignIn Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Handle email verification
     */
    public function verifyEmail() {
        try {
            $token = $_GET['token'] ?? '';
            
            if (empty($token)) {
                $this->view('auth/verification-result', [
                    'success' => false,
                    'message' => 'Token xác thực không hợp lệ'
                ]);
                return;
            }

            // Find user by verification token
            $user = $this->userModel->findByVerificationToken($token);
            
            if (!$user) {
                $this->view('auth/verification-result', [
                    'success' => false,
                    'message' => 'Token xác thực không hợp lệ hoặc đã hết hạn'
                ]);
                return;
            }

            // Verify email
            $verified = $this->userModel->verifyEmail($user->user_id);
            
            if (!$verified) {
                $this->view('auth/verification-result', [
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xác thực email'
                ]);
                return;
            }

            $this->view('auth/verification-result', [
                'success' => true,
                'message' => 'Email được xác thực thành công! Bạn có thể đăng nhập ngay bây giờ.'
            ]);

        } catch (Exception $e) {
            error_log("Email Verification Error: " . $e->getMessage());
            $this->view('auth/verification-result', [
                'success' => false,
                'message' => 'Có lỗi hệ thống xảy ra'
            ]);
        }
    }

    /**
     * Handle logout
     */
    public function logout() {
        SessionHelper::destroyUserSession();
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $this->jsonResponse(true, 'Đăng xuất thành công');
        } else {
            header('Location: /Ecom_website/signin');
            exit;
        }
    }

    /**
     * Resend verification email
     */
    public function resendVerification() {
        try {
            if (empty($_POST['email'])) {
                $this->jsonResponse(false, 'Email là bắt buộc');
                return;
            }

            $email = trim($_POST['email']);
            
            // Find user by email
            $user = $this->userModel->findByEmail($email);
            
            if (!$user) {
                $this->jsonResponse(false, 'Không tìm thấy tài khoản với email này');
                return;
            }

            // Check if already verified
            if ($user->is_active) {
                $this->jsonResponse(false, 'Tài khoản đã được xác thực');
                return;
            }

            // Generate new verification token
            $verificationToken = $this->generateVerificationToken();
            $tokenExpires = date('Y-m-d H:i:s', strtotime('+' . VERIFICATION_EXPIRE_MINUTES . ' minutes'));

            // Update token in database
            $updated = $this->userModel->updateVerificationToken($email, $verificationToken, $tokenExpires);
            
            if (!$updated) {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi cập nhật token');
                return;
            }

            // Send verification email
            $emailSent = EmailHelper::sendVerificationEmail($email, $verificationToken);
            
            if (!$emailSent) {
                $this->jsonResponse(false, 'Không thể gửi email xác thực');
                return;
            }

            $this->jsonResponse(true, 'Email xác thực đã được gửi lại. Vui lòng kiểm tra hộp thư của bạn.');

        } catch (Exception $e) {
            error_log("Resend Verification Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword() {
        $this->view('auth/forgot-password');
    }

    /**
     * Handle forgot password request
     */
    public function forgotPassword() {
        try {
            if (empty($_POST['email'])) {
                $this->jsonResponse(false, 'Email là bắt buộc');
                return;
            }

            $email = trim($_POST['email']);
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->jsonResponse(false, 'Email không hợp lệ');
                return;
            }

            // Find user by email
            $user = $this->userModel->findByEmail($email);
            
            if (!$user) {
                // Don't reveal if email exists for security
                $this->jsonResponse(true, 'Nếu email này tồn tại, bạn sẽ nhận được link đặt lại mật khẩu.');
                return;
            }

            // Check if user is active
            if (!$user->is_active) {
                $this->jsonResponse(false, 'Tài khoản chưa được xác thực. Vui lòng xác thực email trước.');
                return;
            }

            // Generate reset token
            $resetToken = $this->generateVerificationToken();
            $tokenExpires = date('Y-m-d H:i:s', strtotime('+' . RESET_EXPIRE_MINUTES . ' minutes'));

            // Debug log
            error_log("Generated reset token: " . $resetToken . " for email: " . $email);
            error_log("Token expires at: " . $tokenExpires);

            // Save reset token
            $updated = $this->userModel->updateResetToken($email, $resetToken, $tokenExpires);
            
            // Debug log
            error_log("Update result: " . ($updated ? 'SUCCESS' : 'FAILED'));
            
            if (!$updated) {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi tạo yêu cầu đặt lại mật khẩu');
                return;
            }

            // Send reset email
            $emailSent = EmailHelper::sendResetPasswordEmail($email, $resetToken);
            
            if (!$emailSent) {
                $this->jsonResponse(false, 'Không thể gửi email đặt lại mật khẩu');
                return;
            }

            $this->jsonResponse(true, 'Link đặt lại mật khẩu đã được gửi đến email của bạn.');

        } catch (Exception $e) {
            error_log("Forgot Password Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Show reset password form
     */
    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
        
        error_log("DEBUG showResetPassword - Token received: " . $token);
        
        if (empty($token)) {
            error_log("DEBUG showResetPassword - Token is empty");
            $this->view('auth/reset-result', [
                'success' => false,
                'message' => 'Token đặt lại mật khẩu không hợp lệ'
            ]);
            return;
        }

        // Verify token exists and not expired
        $user = $this->userModel->findByResetToken($token);
        
        error_log("DEBUG showResetPassword - User found: " . ($user ? 'YES' : 'NO'));
        if ($user) {
            error_log("DEBUG showResetPassword - User email: " . $user->email);
            error_log("DEBUG showResetPassword - Token expires: " . $user->reset_expires_at);
            error_log("DEBUG showResetPassword - Current time: " . date('Y-m-d H:i:s'));
        }
        
        if (!$user) {
            error_log("DEBUG showResetPassword - Token not found or expired");
            $this->view('auth/reset-result', [
                'success' => false,
                'message' => 'Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn'
            ]);
            return;
        }

        // Show reset password form
        error_log("DEBUG showResetPassword - Showing reset form");
        $this->view('auth/reset-password', ['token' => $token]);
    }

    /**
     * Handle reset password submission
     */
    public function resetPassword() {
        try {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validate input
            if (empty($token)) {
                $this->jsonResponse(false, 'Token không hợp lệ');
                return;
            }

            if (empty($password)) {
                $this->jsonResponse(false, 'Mật khẩu mới là bắt buộc');
                return;
            }

            if (strlen($password) < 6) {
                $this->jsonResponse(false, 'Mật khẩu phải có ít nhất 6 ký tự');
                return;
            }

            if ($password !== $confirmPassword) {
                $this->jsonResponse(false, 'Mật khẩu xác nhận không khớp');
                return;
            }

            // Find user by reset token
            $user = $this->userModel->findByResetToken($token);
            
            if (!$user) {
                $this->jsonResponse(false, 'Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn');
                return;
            }

            // Update password and clear reset token
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updated = $this->userModel->resetPassword($user->user_id, $hashedPassword);
            
            if (!$updated) {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi đặt lại mật khẩu');
                return;
            }

            $this->jsonResponse(true, 'Mật khẩu đã được đặt lại thành công. Bạn có thể đăng nhập bằng mật khẩu mới.');

        } catch (Exception $e) {
            error_log("Reset Password Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Validate sign up data
     */
    private function validateSignUpData($data) {
        $errors = [];
        
        // Email validation
        if (empty($data['email'])) {
            $errors['email'] = 'Email là bắt buộc';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }
        
        // Password validation
        if (empty($data['password'])) {
            $errors['password'] = 'Mật khẩu là bắt buộc';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }
        
        // Confirm password validation
        if (empty($data['confirm_password'])) {
            $errors['confirm_password'] = 'Xác nhận mật khẩu là bắt buộc';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
        }

        // Phone validation (optional)
        if (!empty($data['phone']) && !preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        }
        
        return $errors;
    }

    /**
     * Validate sign in data
     */
    private function validateSignInData($data) {
        $errors = [];
        
        // Email validation
        if (empty($data['email'])) {
            $errors['email'] = 'Email là bắt buộc';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }
        
        // Password validation
        if (empty($data['password'])) {
            $errors['password'] = 'Mật khẩu là bắt buộc';
        }
        
        return $errors;
    }

    /**
     * Generate secure verification token
     */
    private function generateVerificationToken() {
        return bin2hex(random_bytes(32));
    }

    /**
     * Create user session
     */
    private function createUserSession($user) {
        SessionHelper::createUserSession($user);
    }
}
?>
