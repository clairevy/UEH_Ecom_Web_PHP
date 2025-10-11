<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - JEWELRY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        
        .auth-left {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        
        .auth-right {
            padding: 60px 40px;
        }
        
        .auth-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        .auth-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e1e5e9;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .password-field {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            margin-top: 1rem;
            transition: transform 0.2s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .auth-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .auth-link:hover {
            color: #764ba2;
        }
        
        .forgot-password {
            text-align: right;
            margin-top: 5px;
        }
        
        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 1rem;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .auth-left {
                padding: 40px 20px;
            }
            
            .auth-right {
                padding: 40px 20px;
            }
            
            .auth-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="row g-0">
                <div class="col-lg-5">
                    <div class="auth-left">
                        <div>
                            <i class="fas fa-gem mb-3" style="font-size: 3rem;"></i>
                            <h1 class="auth-title">JEWELRY</h1>
                            <p class="auth-subtitle">Khám phá bộ sưu tập trang sức cao cấp của chúng tôi</p>
                            <div class="mt-4">
                                <p class="mb-2">Chưa có tài khoản?</p>
                                <a href="/Ecom_website/signup" class="btn btn-outline-light">
                                    <i class="fas fa-user-plus me-2"></i>Đăng ký ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="auth-right">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-dark">Đăng nhập</h2>
                            <p class="text-muted">Chào mừng bạn trở lại!</p>
                        </div>
                        
                        <!-- Alert Messages -->
                        <div id="alert-container"></div>
                        
                        <form id="signinForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email Address
                                </label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email của bạn" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="password-field">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                                    <i class="fas fa-eye password-toggle" onclick="togglePassword()"></i>
                                </div>
                                <div class="invalid-feedback"></div>
                                <div class="forgot-password">
                                    <a href="<?php echo url('forgot-password'); ?>">Quên mật khẩu?</a>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                                <span class="spinner-border spinner-border-sm ms-2 d-none" id="loadingSpinner"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Show alert message
        function showAlert(message, type = 'danger') {
            const alertContainer = document.getElementById('alert-container');
            const alertHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            alertContainer.innerHTML = alertHTML;
        }

        // Clear form validation
        function clearValidation() {
            document.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
        }

        // Show field error
        function showFieldError(fieldName, message) {
            const field = document.getElementById(fieldName);
            const feedback = field.nextElementSibling;
            
            field.classList.add('is-invalid');
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = message;
            }
        }

        // Handle form submission
        document.getElementById('signinForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearValidation();
            
            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            // Show loading
            submitBtn.disabled = true;
            loadingSpinner.classList.remove('d-none');
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('/Ecom_website/auth/signin', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('Đăng nhập thành công! Đang chuyển hướng...', 'success');
                    setTimeout(() => {
                        if (result.data && result.data.redirect) {
                            window.location.href = result.data.redirect;
                        } else {
                            window.location.href = '/Ecom_website/';
                        }
                    }, 1500);
                } else {
                    if (result.data && typeof result.data === 'object') {
                        // Handle field-specific errors
                        Object.keys(result.data).forEach(fieldName => {
                            showFieldError(fieldName, result.data[fieldName]);
                        });
                    } else {
                        showAlert(result.message);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Có lỗi xảy ra khi kết nối đến server!');
            } finally {
                // Hide loading
                submitBtn.disabled = false;
                loadingSpinner.classList.add('d-none');
            }
        });

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    if (alert.classList.contains('show')) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            }, 5000);
        });
    </script>
</body>
</html>