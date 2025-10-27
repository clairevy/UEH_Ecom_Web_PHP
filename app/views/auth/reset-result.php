<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? 'Thành công' : 'Lỗi'; ?> - Jewelry Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #B8860B;
            --dark-gold: #8B6508;
            --light-gold: #FFD700;
            --cream: #FFFDD0;
            --dark-brown: #8B4513;
        }
        
        body {
            background-color: var(--cream);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        
        .result-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: url('/public/assets/images/auth-bg.jpg') center/cover no-repeat;
        }
        
        .result-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            box-shadow: 0 20px 40px rgba(184, 134, 11, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            margin: 20px;
            text-align: center;
            border: 1px solid var(--gold);
        }
        
        .result-header {
            background: <?php echo $success ? 'var(--gold)' : 'var(--dark-brown)'; ?>;
            color: var(--cream);
            padding: 3rem 2rem;
        }
        
        .result-icon {
            font-size: 4.5rem;
            margin-bottom: 1.5rem;
            color: var(--cream);
        }
        
        .result-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 600;
            font-family: 'Playfair Display', serif;
            color: var(--cream);
        }
        
        .result-body {
            padding: 3rem 2rem;
            background: white;
        }
        
        .result-message {
            font-size: 1.1rem;
            line-height: 1.6;
            color: var(--dark-brown);
            margin-bottom: 2rem;
            font-family: 'Inter', sans-serif;
        }
        
        .btn-primary {
            background: var(--gold);
            border: none;
            border-radius: 0.5rem;
            padding: 1rem 2rem;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
            display: inline-block;
            margin: 0 10px;
            font-family: 'Inter', sans-serif;
        
        .btn-primary:hover {
            background: var(--dark-gold);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(184, 134, 11, 0.2);
            color: white;
        }
        
        .btn-secondary {
            background: var(--dark-brown);
            border: none;
            border-radius: 0.5rem;
            padding: 1rem 2rem;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
            display: inline-block;
            margin: 0 10px;
            font-family: 'Inter', sans-serif;
        }
        
        .btn-secondary:hover {
            background: #723710;
            transform: translateY(-2px);
            color: white;
        }
        
        .action-buttons {
            margin-top: 2rem;
        }

        .home-link {
            color: var(--dark-brown);
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .home-link:hover {
            color: var(--gold);
        }
        
        @media (max-width: 576px) {
            .btn-primary, .btn-secondary {
                width: 100%;
                margin: 0.5rem 0;
            }
            
            .result-card {
                margin: 1rem;
            }
            
            .result-header {
                padding: 2rem 1.5rem;
            }
            
            .result-body {
                padding: 2rem 1.5rem;
            }
            
            .result-header h1 {
                font-size: 2rem;
            }
            
            .result-icon {
                font-size: 3.5rem;
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
                    <a href="<?php echo url(''); ?>" class="home-link">
                        <i class="fas fa-home"></i>
                        <span>Về trang chủ</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>