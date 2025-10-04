<?php
/**
 * Customers Page - Quản lý khách hàng với database
 * Chuyển đổi từ customers.html với OOP MVC
 */

// Thiết lập
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Include models
require_once __DIR__ . '/../models/UserModel.php';

try {
    // Khởi tạo model
    $userModel = new UserModel();
    
    // Lấy dữ liệu từ database
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $keyword = $_GET['search'] ?? '';
    
    // Lấy users với roles
    $users = $userModel->getUsersWithRoles($limit, $offset);
    $totalUsers = $userModel->count();
    $totalPages = ceil($totalUsers / $limit);
    
    // Thiết lập dữ liệu cho view
    $pageTitle = 'JEWELLERY Admin - Khách hàng';
    $currentPage = 'customers';
    $baseUrl = '../';
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $users = [];
    $totalUsers = 0;
    $totalPages = 0;
    $pageTitle = 'JEWELLERY Admin - Khách hàng';
    $currentPage = 'customers';
    $baseUrl = '../';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/variables.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/main.css">
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
                 <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" alt="Total Customers" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-primary">1,247</h3>
                                </div>
                                <h6 class="fw-bold">Tổng Khách Hàng</h6>
                                <p class="text-muted small mb-0">+12% so với tháng trước</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" alt="Active Users" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-success">1,189</h3>
                                </div>
                                <h6 class="fw-bold">Đang Hoạt Động</h6>
                                <p class="text-muted small mb-0">95.3% tổng khách hàng</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3063/3063822.png" alt="Orders" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-warning">892</h3>
                                </div>
                                <h6 class="fw-bold">Đã Đặt Hàng</h6>
                                <p class="text-muted small mb-0">71.5% khách hàng</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/748/748113.png" alt="New This Month" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-info">47</h3>
                                </div>
                                <h6 class="fw-bold">Mới Tháng Này</h6>
                                <p class="text-muted small mb-0">+8 so với tuần trước</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Lỗi:</strong> <?= htmlspecialchars($error) ?>
                    </div>
                     
                <?php else: ?>
                    <!-- Customers Management -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex gap-3">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" class="form-control" placeholder="Tìm kiếm khách hàng..." 
                                       value="<?= htmlspecialchars($keyword) ?>" id="searchInput">
                                <button class="btn btn-outline-secondary" type="button" onclick="searchCustomers()">
                                    <img src="https://cdn-icons-png.flaticon.com/512/54/54481.png" alt="Search" width="16" height="16">
                                </button>
                            </div>
                        </div>
                        <button class="btn btn-primary" onclick="addCustomer()">
                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" alt="Add" width="16" height="16" class="me-2">
                            Thêm khách hàng
                        </button>
                    </div>

                    <!-- Customers Table -->
                    <div class="table-card">
                        <div class="table-header">
    <h5 class="table-title">Danh Sách Khách Hàng</h5>
    <div class="d-flex gap-2">
        <!-- Filters -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <img src="https://cdn-icons-png.flaticon.com/512/3031/3031293.png" alt="Filter" width="16" height="16" class="me-1">
                Lọc
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?filter=all">Tất cả</a></li>
                <li><a class="dropdown-item" href="?filter=active">Đang hoạt động</a></li>
                <li><a class="dropdown-item" href="?filter=inactive">Không hoạt động</a></li>
            </ul>
        </div>

        <!-- Search -->
        <form method="get" class="position-relative">
            <input type="text" name="search" value="<?= htmlspecialchars($keyword) ?>" class="form-control form-control-sm" placeholder="Tìm kiếm khách hàng..." style="width: 200px; padding-left: 35px;">
            <img src="https://cdn-icons-png.flaticon.com/512/751/751463.png" alt="Search" width="16" height="16" class="position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);">
        </form>

        <!-- Add Customer -->
        <button class="btn btn-success-custom btn-sm" onclick="addCustomer()">
            <img src="https://cdn-icons-png.flaticon.com/512/748/748113.png" alt="Add" width="16" height="16" class="me-1">
            Thêm Khách Hàng
        </button>
    </div>
</div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>Khách hàng</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($users)): ?>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><input type="checkbox" class="form-check-input customer-checkbox"></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User" width="32" height="32" class="rounded-circle me-3">
                                                        <div>
                                                            <div class="fw-bold"><?= htmlspecialchars($user['name'] ?? 'N/A') ?></div>
                                                            <div class="text-muted small">ID: <?= $user['user_id'] ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                                <td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?= htmlspecialchars($user['role_name'] ?? 'customer') ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $user['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                                        <?= $user['is_active'] ? 'Hoạt động' : 'Tạm dừng' ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                onclick="viewCustomer(<?= $user['user_id'] ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828270.png" alt="View" width="14" height="14">
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-secondary" 
                                                                onclick="editCustomer(<?= $user['user_id'] ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828270.png" alt="Edit" width="14" height="14">
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteCustomer(<?= $user['user_id'] ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1214/1214428.png" alt="Delete" width="14" height="14">
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <p class="text-muted">Không có khách hàng nào</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="d-flex justify-content-center mt-4">
                                <nav>
                                    <ul class="pagination">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($keyword) ?>">Trước</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($keyword) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($keyword) ?>">Sau</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Component Manager -->
    <script src="<?= $baseUrl ?>components/component-manager.js"></script>
    
    <!-- Page Configuration -->
    <script>
        window.pageConfig = {
            sidebar: {
                brandName: 'JEWELLERY',
                activePage: '<?= $currentPage ?>',
                links: {
                    dashboard: '../index.php',
                    products: 'products.php',
                    orders: 'orders.php'
                },
                categories: [],
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: 'Khách hàng',
                breadcrumb: 'Home > Khách hàng',
                showDateRange: false
            }
        };

        // Functions
        function searchCustomers() {
            const keyword = document.getElementById('searchInput').value;
            window.location.href = `?search=${encodeURIComponent(keyword)}`;
        }

        function addCustomer() {
            alert('Tính năng thêm khách hàng đang được phát triển');
        }

        function viewCustomer(userId) {
            window.location.href = `customer-details.html?id=${userId}`;
        }

        function editCustomer(userId) {
            alert('Tính năng chỉnh sửa khách hàng đang được phát triển');
        }

        function deleteCustomer(userId) {
            if (confirm('Bạn có chắc chắn muốn xóa khách hàng này?')) {
                alert('Tính năng xóa khách hàng đang được phát triển');
            }
        }

        // Select all checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.customer-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $baseUrl ?>assets/js/main.js"></script>
</body>
</html>
