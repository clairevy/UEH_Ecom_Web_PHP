<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực email - JEWELRY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .verification-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .verification-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        
        .verification-icon {
            font-size: 4rem;
            margin-bottom: 2rem;
        }
        
        .verification-icon.success {
            color: #28a745;
        }
        
        .verification-icon.error {
            color: #dc3545;
        }
        
        .verification-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .verification-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1rem;
            margin: 0 10px 10px 0;
            transition: transform 0.2s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1rem;
            margin: 0 10px 10px 0;
            transition: all 0.2s ease;
        }
        
        .btn-outline-primary:hover {
            background: #667eea;
            border-color: #667eea;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .verification-card {
                padding: 40px 20px;
            }
            
            .verification-title {
                font-size: 1.5rem;
            }
            
            .verification-icon {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card">
            <?php if (isset($success) && $success): ?>
                <div class="verification-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="verification-title text-success">Xác thực thành công!</h1>
                <p class="verification-message"><?php echo htmlspecialchars($message); ?></p>
                <div class="mt-4">
                    <a href="/Ecom_website/signin" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập ngay
                    </a>
                    <a href="/Ecom_website/" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Về trang chủ
                    </a>
                </div>
            <?php else: ?>
                <div class="verification-icon error">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h1 class="verification-title text-danger">Xác thực thất bại</h1>
                <p class="verification-message"><?php echo htmlspecialchars($message ?? 'Có lỗi xảy ra trong quá trình xác thực email.'); ?></p>
                <div class="mt-4">
                    <a href="/Ecom_website/signup" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Đăng ký lại
                    </a>
                    <a href="/Ecom_website/" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Về trang chủ
                    </a>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        Gặp vấn đề? <a href="mailto:support@jewelry.com" class="text-decoration-none">Liên hệ hỗ trợ</a>
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>