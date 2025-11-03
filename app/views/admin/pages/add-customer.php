<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JEWELLERY Admin - Add New Customer</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Component Container -->
        <div id="sidebar-container"></div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header Component Container -->
            <div id="header-container"></div>

            <!-- Content -->
            <main class="content">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="row">
                    <!-- Customer Form -->
                    <div class="col-lg-8 col-md-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" alt="Customer" width="32" height="32" class="me-3">
                                    <h4 class="fw-bold mb-0">Thêm Khách Hàng Mới</h4>
                                </div>

                                <form id="customerForm" method="POST" action="<?= BASE_URL ?>/admin/index.php?url=customers&action=create">
                                    <!-- Profile Image -->
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <h6 class="fw-bold border-bottom pb-2 mb-3">Ảnh Đại Diện</h6>
                                            <div class="text-center">
                                                <img id="profileImagePreview" src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Profile" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #dee2e6;">
                                                <div>
                                                    <div class="border border-2 border-dashed rounded p-3 d-inline-block" style="cursor: pointer;" onclick="document.getElementById('profileImageInput').click()">
                                                        <input type="file" id="profileImageInput" accept="image/*" style="display: none;">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/1160/1160358.png" alt="Upload" width="24" height="24" class="me-2">
                                                        <span class="small">Upload ảnh profile</span>
                                                    </div>
                                                    <p class="text-muted small mt-2 mb-0">JPG, PNG (Max 2MB) - Kích thước đề xuất: 400x400px</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Basic Information -->
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h6 class="fw-bold border-bottom pb-2 mb-3">Thông Tin Cá Nhân</h6>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customerName" class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="customerName" name="name" placeholder="Nhập họ và tên" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customerPhone" class="form-label">Số Điện Thoại <span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control" id="customerPhone" name="phone" placeholder="0901234567" required>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="customerEmail" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="customerEmail" name="email" placeholder="example@gmail.com" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Account Information -->
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h6 class="fw-bold border-bottom pb-2 mb-3">Thông Tin Tài Khoản</h6>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customerRole" class="form-label">Vai Trò <span class="text-danger">*</span></label>
                                                <select class="form-control" id="customerRole" required>
                                                    <option value="">Chọn vai trò</option>
                                                    <option value="customer">Customer</option>
                                                    <option value="vip_customer">VIP Customer</option>
                                                    <option value="admin">Admin</option>
                                                    <option value="manager">Manager</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customerPassword" class="form-label">Mật Khẩu <span class="text-danger">*</span></label>
                                                <div class="position-relative">
                                                    <input type="password" class="form-control" id="customerPassword" name="password" placeholder="Nhập mật khẩu" required>
                                                    <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-2" onclick="togglePassword('customerPassword')">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/709/709612.png" alt="Show" width="16" height="16">
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" checked>
                                                    <label class="form-check-label fw-bold" for="isActive">
                                                        Kích Hoạt Tài Khoản
                                                    </label>
                                                    <div class="form-text">Tài khoản sẽ có thể đăng nhập ngay lập tức</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Address Information -->
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h6 class="fw-bold border-bottom pb-2 mb-3">Địa Chỉ Mặc Định</h6>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="addressStreet" class="form-label">Địa Chỉ</label>
                                                <textarea class="form-control" id="addressStreet" rows="2" placeholder="Số nhà, tên đường..."></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="addressCountry" class="form-label">Quốc gia <span class="text-danger">*</span></label>
                                                <select class="form-control" id="addressCountry" required onchange="loadProvinces()">
                                                    <option value="">Chọn quốc gia</option>
                                                    <option value="VN" selected>Việt Nam</option>
                                                    <option value="US">United States</option>
                                                    <option value="UK">United Kingdom</option>
                                                    <option value="JP">Japan</option>
                                                    <option value="KR">South Korea</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="addressProvince" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                                <select class="form-control" id="addressProvince" required onchange="loadDistricts()">
                                                    <option value="">Chọn tỉnh/thành</option>
                                                </select>
                                                <small class="text-muted">Vui lòng chọn quốc gia trước</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="addressDistrict" class="form-label">Quận/Huyện</label>
                                                <select class="form-control" id="addressDistrict" onchange="loadWards()">
                                                    <option value="">Chọn quận/huyện</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="addressWard" class="form-label">Phường/Xã</label>
                                                <select class="form-control" id="addressWard">
                                                    <option value="">Chọn phường/xã</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2 mt-4">
                                        <button type="submit" class="btn btn-success-custom btn-custom px-4">
                                            <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" alt="Save" width="16" height="16" class="me-1">
                                            TẠO KHÁCH HÀNG
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-custom px-4" onclick="cancelAdd()">
                                            <img src="https://cdn-icons-png.flaticon.com/512/189/189665.png" alt="Cancel" width="16" height="16" class="me-1">
                                            HỦY BỎ
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Preview & Guidelines -->
                    <div class="col-lg-4">
                        <!-- Customer Preview -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Xem Trước Khách Hàng</h6>
                                
                                <div class="text-center mb-3">
                                    <img id="previewAvatar" src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Avatar" width="64" height="64" class="rounded-circle">
                                </div>
                                
                                <div class="preview-info">
                                    <div class="mb-2">
                                        <strong id="previewName">Tên Khách Hàng</strong>
                                        <div class="small text-muted" id="previewRole">Customer</div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="small">📧 <span id="previewEmail">email@example.com</span></div>
                                        <div class="small">📞 <span id="previewPhone">0901234567</span></div>
                                    </div>
                                    <div class="mb-2">
                                        <span class="badge" id="previewStatus" style="background-color: var(--success-color);">Hoạt động</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Guidelines -->
                        <div class="card">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Hướng Dẫn Tạo Khách Hàng</h6>
                                <ul class="small text-muted mb-0">
                                    <li class="mb-2">Đảm bảo email chưa được sử dụng trong hệ thống</li>
                                    <li class="mb-2">Số điện thoại phải đúng định dạng Việt Nam</li>
                                    <li class="mb-2">Mật khẩu tối thiểu 8 ký tự, có chữ hoa và số</li>
                                    <li class="mb-2">Chọn vai trò phù hợp với khách hàng</li>
                                    <li class="mb-2">Địa chỉ có thể bổ sung sau khi tạo</li>
                                    <li class="mb-2">Tài khoản sẽ gửi email thông báo nếu được kích hoạt</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Role Information -->
                        <div class="card mt-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Thông Tin Vai Trò</h6>
                                <div class="role-info">
                                    <div class="mb-2">
                                        <span class="badge bg-primary me-2">Customer</span>
                                        <span class="small">Khách hàng thông thường</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="badge bg-secondary me-2">VIP Customer</span>
                                        <span class="small">Khách hàng VIP với ưu đãi đặc biệt</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="badge bg-warning me-2">Manager</span>
                                        <span class="small">Quản lý cửa hàng</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="badge bg-danger me-2">Admin</span>
                                        <span class="small">Quản trị hệ thống</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Component Manager -->
    <script src="../components/component-manager.js"></script>
    
    <!-- Page Configuration -->
    <script>
        window.pageConfig = {
            sidebar: {
                brandName: 'JEWELLERY',
                activePage: 'customers',
                links: {
                    dashboard: '../index.html',
                    products: 'products.html',
                    orders: 'orders.html',
                    customers: 'customers.html',
                    'customer-roles': 'customer-roles.html'
                },
                categories: [
                    { name: 'Vòng cổ', count: 12 },
                    { name: 'Vòng tay', count: 10 },
                    { name: 'Bông tai', count: 13 },
                    { name: 'Nhẫn', count: 4 },
                    { name: 'Lắc chân', count: 6 }
                ],
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: 'Add New Customer',
                breadcrumb: 'Home > Customers > Add New Customer'
            }
        };
    </script>
    
    <!-- Add Customer Page Script -->
    <script src="../assets/js/add-customer.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
