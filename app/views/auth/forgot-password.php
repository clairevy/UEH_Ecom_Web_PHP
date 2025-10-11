<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Jewelry Store</title>
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
            max-width: 400px;
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
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1><i class="fas fa-key me-2"></i>Quên mật khẩu</h1>
                <p>Nhập email để nhận link đặt lại mật khẩu</p>
            </div>
            
            <div class="auth-body">
                <div id="alertContainer"></div>
                
                <form id="forgotPasswordForm">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
                        <label for="email"><i class="fas fa-envelope me-2"></i>Email Address</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="spinner"></span>
                        <i class="fas fa-paper-plane me-2"></i>Gửi link đặt lại
                    </button>
                </form>
                
                <div class="auth-links">
                    <a href="<?php echo url('signin'); ?>"><i class="fas fa-arrow-left me-2"></i>Quay lại đăng nhập</a>
                    <br><br>
                    <span class="text-muted">Chưa có tài khoản? </span>
                    <a href="<?php echo url('signup'); ?>">Đăng ký ngay</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const alertContainer = document.getElementById('alertContainer');
            
            // Show loading state
            form.classList.add('loading');
            spinner.classList.remove('d-none');
            
            try {
                const formData = new FormData(form);
                
                const response = await fetch('<?php echo url('auth/forgot-password'); ?>', {
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
                
                // Reset form if successful
                if (result.success) {
                    form.reset();
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
    </script>
</body>
</html>