<?php
/**
 * Customers View - Pure MVC View
 * Hiển thị data từ database, không có hardcode
 */

// Biến được truyền từ Controller:
// $users - danh sách users từ database
// $stats - thống kê users
// $message - thông báo thành công
// $error - thông báo lỗi
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JEWELLERY Admin - Customer Management</title>
    
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
                <!-- Flash Messages -->
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Thành công:</strong> <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Lỗi:</strong> <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Customer Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" alt="Total Customers" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-primary"><?= $stats['total'] ?? 0 ?></h3>
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
                                    <h3 class="mb-0 text-success"><?= $stats['active'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Đang Hoạt Động</h6>
                                <p class="text-muted small mb-0"><?= $stats['total'] > 0 ? number_format(($stats['active'] / $stats['total']) * 100, 1) : 0 ?>% tổng khách hàng</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3063/3063822.png" alt="Orders" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-warning"><?= $stats['with_orders'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Đã Đặt Hàng</h6>
                                <p class="text-muted small mb-0"><?= $stats['total'] > 0 ? number_format(($stats['with_orders'] / $stats['total']) * 100, 1) : 0 ?>% khách hàng</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/748/748113.png" alt="New This Month" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-info"><?= $stats['new_this_month'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Mới Tháng Này</h6>
                                <p class="text-muted small mb-0">+8 so với tuần trước</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Management Table -->
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
                                    <li><a class="dropdown-item" href="#" onclick="filterCustomers('all')">Tất cả</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterCustomers('active')">Đang hoạt động</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterCustomers('inactive')">Không hoạt động</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterCustomers('has-orders')">Có đơn hàng</a></li>
                                </ul>
                            </div>

                            <!-- Search -->
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm khách hàng..." style="width: 200px; padding-left: 35px;" id="customerSearch">
                                <img src="https://cdn-icons-png.flaticon.com/512/751/751463.png" alt="Search" width="16" height="16" class="position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);">
                            </div>

                            <!-- Add Customer -->
                            <button class="btn btn-success-custom btn-sm" onclick="addNewCustomer()">
                                <img src="https://cdn-icons-png.flaticon.com/512/748/748113.png" alt="Add" width="16" height="16" class="me-1">
                                Thêm Khách Hàng
                            </button>

                            <!-- More Actions -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2311/2311524.png" alt="More" width="16" height="16">
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Xuất Excel</a></li>
                                    <li><a class="dropdown-item" href="#">Xuất PDF</a></li>
                                    <li><a class="dropdown-item" href="#">In danh sách</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" id="customersTable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>Khách Hàng</th>
                                    <th>Email</th>
                                    <th>Số Điện Thoại</th>
                                    <th>Vai Trò</th>
                                    <th>Trạng Thái</th>
                                    <th>Đơn Hàng</th>
                                    <th>Ngày Tham Gia</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><input type="checkbox" class="form-check-input row-checkbox"></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User" width="32" height="32" class="rounded-circle me-2">
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($user->name ?? 'N/A') ?></div>
                                                        <div class="small text-muted">ID: #<?= $user->user_id ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($user->email) ?></td>
                                            <td><?= htmlspecialchars($user->phone ?? 'N/A') ?></td>
                                            <td>
                                                <?php if ($user->role_name): ?>
                                                    <span class="badge bg-primary"><?= htmlspecialchars($user->role_name) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Customer</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($user->is_active): ?>
                                                    <span class="badge badge-delivered badge-custom">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge badge-canceled badge-custom">Không hoạt động</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="fw-bold"><?= $user->order_count ?? 0 ?></div>
                                                    <div class="small text-muted">đơn hàng</div>
                                                </div>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($user->created_at)) ?></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/2311/2311524.png" alt="Actions" width="16" height="16">
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="viewCustomer(<?= $user->user_id ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/709/709612.png" alt="View" width="16" height="16" class="me-2">
                                                            Xem chi tiết
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="editCustomer(<?= $user->user_id ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1159/1159633.png" alt="Edit" width="16" height="16" class="me-2">
                                                            Chỉnh sửa
                                                        </a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-warning" href="#" onclick="toggleCustomerStatus(<?= $user->user_id ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828843.png" alt="Toggle" width="16" height="16" class="me-2">
                                                            <?= $user->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' ?>
                                                        </a></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteCustomer(<?= $user->user_id ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="16" height="16" class="me-2">
                                                            Xóa
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="No Data" width="64" height="64" class="mb-3 opacity-50">
                                            <p class="text-muted mb-0">Chưa có khách hàng nào</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div id="pagination-container"></div>
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
                    dashboard: '<?= BASE_URL ?>' + '/admin/index.php?url=dashboard',
                    products: '<?= BASE_URL ?>' + '/admin/index.php?url=products',
                    categories: '<?= BASE_URL ?>' + '/admin/index.php?url=categories',
                    orders: '<?= BASE_URL ?>' + '/admin/index.php?url=orders',
                    customers: '<?= BASE_URL ?>' + '/admin/index.php?url=customers',
                    collections: '<?= BASE_URL ?>' + '/admin/index.php?url=collections',
                    reviews: '<?= BASE_URL ?>' + '/admin/index.php?url=reviews',
                    media: '<?= BASE_URL ?>' + '/admin/index.php?url=media'
                }
            },
            header: {
                title: 'Customer Management',
                breadcrumb: 'Home > Customers > All Customers'
            }
        };
    </script>
    
    <!-- Customers Page Script -->
    <script src="../assets/js/customers.js"></script>
    
    <!-- Customer Management Functions -->
    <script>
        function addNewCustomer() {
            // Redirect to the Add Customer page (MVC: controller/view handled by admin router)
            window.location.href = '<?= BASE_URL ?>' + '/admin/index.php?url=add-customer';
        }
        
        //
        function viewCustomer(userId) {
            window.location.href = '<?= BASE_URL ?>' + '/admin/index.php?url=customers&action=show&id=' + userId;
        }
        
        function editCustomer(userId) {
            window.location.href = '<?= BASE_URL ?>' + '/admin/index.php?url=customers&action=edit&id=' + userId;
        }
        
        function toggleCustomerStatus(userId) {
            if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái của khách hàng này không?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= BASE_URL ?>' + '/admin/index.php?url=customers&action=toggle&id=' + userId;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function deleteCustomer(userId) {
            if (confirm('Bạn có chắc chắn muốn xóa khách hàng này không?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= BASE_URL ?>' + '/admin/index.php?url=customers&action=delete&id=' + userId;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function filterCustomers(filter) {
            // TODO: Implement filtering logic
            console.log('Filter customers by:', filter);
        }
    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
