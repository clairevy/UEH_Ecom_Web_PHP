<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - Jewelry Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        }
        
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            margin: 20px;
        }
        
        .auth-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .auth-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .auth-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .auth-body {
            padding: 40px 30px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border-left: 4px solid #28a745;
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }
        
        .auth-links {
            text-align: center;
            margin-top: 20px;
        }
        
        .auth-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .auth-links a:hover {
            color: #764ba2;
        }
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .password-requirements {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .password-requirements li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1><i class="fas fa-lock me-2"></i>Đặt lại mật khẩu</h1>
                <p>Nhập mật khẩu mới cho tài khoản của bạn</p>
            </div>
            
            <div class="auth-body">
                <div id="alertContainer"></div>
                
                <div class="password-requirements">
                    <h6><i class="fas fa-info-circle me-2"></i>Yêu cầu mật khẩu:</h6>
                    <ul>
                        <li>Ít nhất 6 ký tự</li>
                        <li>Nên chứa chữ hoa, chữ thường và số</li>
                        <li>Không sử dụng mật khẩu dễ đoán</li>
                    </ul>
                </div>
                
                <form id="resetPasswordForm">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Mật khẩu mới" required minlength="6">
                        <label for="password"><i class="fas fa-lock me-2"></i>Mật khẩu mới</label>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Xác nhận mật khẩu" required minlength="6">
                        <label for="confirm_password"><i class="fas fa-lock me-2"></i>Xác nhận mật khẩu</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="spinner"></span>
                        <i class="fas fa-save me-2"></i>Đặt lại mật khẩu
                    </button>
                </form>
                
                <div class="auth-links">
                    <a href="<?php echo url('signin'); ?>"><i class="fas fa-arrow-left me-2"></i>Quay lại đăng nhập</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('resetPasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const alertContainer = document.getElementById('alertContainer');
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Validate passwords match
            if (password !== confirmPassword) {
                alertContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Mật khẩu xác nhận không khớp!
                    </div>
                `;
                return;
            }
            
            // Show loading state
            form.classList.add('loading');
            spinner.classList.remove('d-none');
            
            try {
                const formData = new FormData(form);
                
                const response = await fetch('<?php echo url('auth/reset-password'); ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                // Show alert
                alertContainer.innerHTML = `
                    <div class="alert alert-${result.success ? 'success' : 'danger'}">
                        <i class="fas fa-${result.success ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                        ${result.message}
                    </div>
                `;
                
                // Redirect to login if successful
                if (result.success) {
                    setTimeout(() => {
                        window.location.href = '<?php echo url('signin'); ?>';
                    }, 2000);
                }
                
            } catch (error) {
                alertContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Có lỗi xảy ra khi kết nối đến server!
                    </div>
                `;
            } finally {
                // Hide loading state
                form.classList.remove('loading');
                spinner.classList.add('d-none');
            }
        });
        
        // Real-time password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Mật khẩu xác nhận không khớp');
                this.style.borderColor = '#dc3545';
            } else {
                this.setCustomValidity('');
                this.style.borderColor = '';
            }
        });
    </script>
</body>
</html>