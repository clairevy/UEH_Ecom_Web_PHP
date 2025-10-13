<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Thông tin cá nhân'; ?> - Jewelry Store</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .profile-sidebar {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #667eea;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
        }
        
        .profile-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .nav-pills .nav-link {
            border-radius: 10px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: transform 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .upload-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            cursor: pointer;
        }
        
        .avatar-container:hover .upload-overlay {
            opacity: 1;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body class="bg-light">
    
    <!-- Include Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>
    
    <div class="container mt-5 pt-4">
        <div class="row">
            <!-- Profile Sidebar -->
            <div class="col-lg-4">
                <div class="profile-sidebar text-center">
                    <!-- Avatar -->
                    <div class="avatar-container position-relative d-inline-block mb-4">
                        <img id="profileAvatar" 
                             src="<?php echo !empty($user->avatar) ? $user->avatar : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=667eea&color=fff&size=120'; ?>" 
                             alt="Avatar" 
                             class="profile-avatar">
                        <div class="upload-overlay" onclick="document.getElementById('avatarInput').click()">
                            <i class="fas fa-camera fa-2x text-white"></i>
                        </div>
                        <input type="file" id="avatarInput" accept="image/*" style="display: none;">
                    </div>
                    
                    <!-- User Info -->
                    <h4 class="fw-bold mb-2"><?php echo htmlspecialchars($user->name); ?></h4>
                    <p class="text-muted mb-3"><?php echo htmlspecialchars($user->email); ?></p>
                    <span class="badge bg-success mb-4">
                        <i class="fas fa-check-circle me-1"></i>
                        Đã xác thực
                    </span>
                    
                    <!-- Navigation Pills -->
                    <ul class="nav nav-pills flex-column" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active w-100 text-start" id="info-tab" data-bs-toggle="pill" data-bs-target="#info" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>Thông tin cá nhân
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100 text-start" id="password-tab" data-bs-toggle="pill" data-bs-target="#password" type="button" role="tab">
                                <i class="fas fa-key me-2"></i>Đổi mật khẩu
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100 text-start" id="orders-tab" data-bs-toggle="pill" data-bs-target="#orders" type="button" role="tab">
                                <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Profile Content -->
            <div class="col-lg-8">
                <div class="profile-content">
                    <div class="tab-content" id="profileTabContent">
                        <!-- Personal Info Tab -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                            <h5 class="fw-bold mb-4">
                                <i class="fas fa-user text-primary me-2"></i>
                                Thông tin cá nhân
                            </h5>
                            
                            <div id="alertContainer"></div>
                            
                            <form id="profileForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Họ và tên *</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user->name); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user->email); ?>" readonly>
                                        <small class="text-muted">Email không thể thay đổi</small>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Số điện thoại</label>
                                        <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user->phone ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Ngày sinh</label>
                                        <input type="date" class="form-control" name="date_of_birth" value="<?php echo $user->date_of_birth ?? ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Giới tính</label>
                                        <select class="form-control" name="gender">
                                            <option value="">Chọn giới tính</option>
                                            <option value="male" <?php echo ($user->gender ?? '') === 'male' ? 'selected' : ''; ?>>Nam</option>
                                            <option value="female" <?php echo ($user->gender ?? '') === 'female' ? 'selected' : ''; ?>>Nữ</option>
                                            <option value="other" <?php echo ($user->gender ?? '') === 'other' ? 'selected' : ''; ?>>Khác</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Địa chỉ</label>
                                    <textarea class="form-control" name="address" rows="3" placeholder="Nhập địa chỉ của bạn"><?php echo htmlspecialchars($user->address ?? ''); ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Cập nhật thông tin
                                </button>
                            </form>
                        </div>
                        
                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel">
                            <h5 class="fw-bold mb-4">
                                <i class="fas fa-key text-primary me-2"></i>
                                Đổi mật khẩu
                            </h5>
                            
                            <div id="passwordAlertContainer"></div>
                            
                            <form id="passwordForm">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mật khẩu hiện tại *</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mật khẩu mới *</label>
                                    <input type="password" class="form-control" name="new_password" minlength="6" required>
                                    <small class="text-muted">Mật khẩu phải có ít nhất 6 ký tự</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Xác nhận mật khẩu mới *</label>
                                    <input type="password" class="form-control" name="confirm_password" minlength="6" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key me-2"></i>
                                    Đổi mật khẩu
                                </button>
                            </form>
                        </div>
                        
                        <!-- Orders Tab -->
                        <div class="tab-pane fade" id="orders" role="tabpanel">
                            <h5 class="fw-bold mb-4">
                                <i class="fas fa-shopping-bag text-primary me-2"></i>
                                Đơn hàng của tôi
                            </h5>
                            
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">Bạn chưa có đơn hàng nào</h6>
                                <p class="text-muted">Hãy bắt đầu mua sắm để xem đơn hàng tại đây</p>
                                <a href="/Ecom_website/products" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    Mua sắm ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Profile form submission
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const alertContainer = document.getElementById('alertContainer');
            
            try {
                const response = await fetch('/Ecom_website/profile/update', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                showAlert(alertContainer, result.success, result.message);
                
                if (result.success) {
                    // Update user name in header if changed
                    const nameInput = document.querySelector('input[name="name"]');
                    if (nameInput.value !== '<?php echo addslashes($user->name); ?>') {
                        setTimeout(() => location.reload(), 1500);
                    }
                }
                
            } catch (error) {
                showAlert(alertContainer, false, 'Có lỗi xảy ra khi kết nối đến server!');
            }
        });
        
        // Password form submission
        document.getElementById('passwordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const alertContainer = document.getElementById('passwordAlertContainer');
            
            // Validate password confirmation
            const newPassword = formData.get('new_password');
            const confirmPassword = formData.get('confirm_password');
            
            if (newPassword !== confirmPassword) {
                showAlert(alertContainer, false, 'Mật khẩu xác nhận không khớp!');
                return;
            }
            
            try {
                const response = await fetch('/Ecom_website/profile/change-password', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                showAlert(alertContainer, result.success, result.message);
                
                if (result.success) {
                    this.reset();
                }
                
            } catch (error) {
                showAlert(alertContainer, false, 'Có lỗi xảy ra khi kết nối đến server!');
            }
        });
        
        // Avatar upload
        document.getElementById('avatarInput').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('avatar', file);
            
            try {
                const response = await fetch('/Ecom_website/profile/upload-avatar', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('profileAvatar').src = result.data.avatar_url + '?t=' + Date.now();
                    showAlert(document.getElementById('alertContainer'), true, result.message);
                } else {
                    showAlert(document.getElementById('alertContainer'), false, result.message);
                }
                
            } catch (error) {
                showAlert(document.getElementById('alertContainer'), false, 'Có lỗi xảy ra khi upload avatar!');
            }
        });
        
        // Show alert function
        function showAlert(container, success, message) {
            container.innerHTML = `
                <div class="alert alert-${success ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${success ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    </script>
</body>
</html>