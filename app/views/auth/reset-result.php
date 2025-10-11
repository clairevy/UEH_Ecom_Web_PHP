<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? 'Thành công' : 'Lỗi'; ?> - Jewelry Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .result-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .result-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            margin: 20px;
            text-align: center;
        }
        
        .result-header {
            background: <?php echo $success ? 'linear-gradient(135deg, #28a745 0%, #20c997 100%)' : 'linear-gradient(135deg, #dc3545 0%, #e74c3c 100%)'; ?>;
            color: white;
            padding: 50px 30px;
        }
        
        .result-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .result-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .result-body {
            padding: 40px 30px;
        }
        
        .result-message {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #495057;
            margin-bottom: 30px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
            display: inline-block;
            margin: 0 10px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
            display: inline-block;
            margin: 0 10px;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            color: white;
        }
        
        .action-buttons {
            margin-top: 20px;
        }
        
        @media (max-width: 576px) {
            .btn-primary, .btn-secondary {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <div class="result-container">
        <div class="result-card">
            <div class="result-header">
                <div class="result-icon">
                    <i class="fas fa-<?php echo $success ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                </div>
                <h1><?php echo $success ? 'Thành công!' : 'Có lỗi xảy ra'; ?></h1>
            </div>
            
            <div class="result-body">
                <div class="result-message">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                
                <div class="action-buttons">
                    <?php if ($success): ?>
                        <a href="<?php echo url('signin'); ?>" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập ngay
                        </a>
                    <?php else: ?>
                        <a href="<?php echo url('forgot-password'); ?>" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>Thử lại
                        </a>
                        <a href="<?php echo url('signin'); ?>" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4">
                    <a href="<?php echo url(''); ?>" style="color: #6c757d; text-decoration: none;">
                        <i class="fas fa-home me-2"></i>Về trang chủ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>