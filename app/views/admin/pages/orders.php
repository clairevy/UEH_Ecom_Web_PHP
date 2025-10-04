<?php
/**
 * Orders Page - Quản lý đơn hàng với database
 * Chuyển đổi từ orders.html với OOP MVC
 */

// Thiết lập
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Include models
require_once __DIR__ . '/../models/OrderModel.php';

try {
    // Khởi tạo model
    $orderModel = new OrderModel();
    
    // Lấy dữ liệu từ database
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $status = $_GET['status'] ?? '';
    
    // Lấy đơn hàng với thông tin chi tiết
    $orders = $orderModel->getOrdersWithDetails($limit, $offset);
    $totalOrders = $orderModel->count();
    $totalPages = ceil($totalOrders / $limit);
    
    // Thiết lập dữ liệu cho view
    $pageTitle = 'JEWELLERY Admin - Đơn hàng';
    $currentPage = 'orders';
    $baseUrl = '../';
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $orders = [];
    $totalOrders = 0;
    $totalPages = 0;
    $pageTitle = 'JEWELLERY Admin - Đơn hàng';
    $currentPage = 'orders';
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
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Lỗi:</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php else: ?>
                    <!-- Filter Options -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex gap-3">
                            <select class="form-select" style="width: 200px;" id="statusFilter" onchange="filterByStatus()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                <option value="paid" <?= $status == 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
                                <option value="shipped" <?= $status == 'shipped' ? 'selected' : '' ?>>Đã giao hàng</option>
                                <option value="delivered" <?= $status == 'delivered' ? 'selected' : '' ?>>Đã giao</option>
                                <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                            </select>
                            <input type="date" class="form-control" style="width: 150px;" id="dateFrom" placeholder="Từ ngày">
                            <input type="date" class="form-control" style="width: 150px;" id="dateTo" placeholder="Đến ngày">
                        </div>
                        <button class="btn btn-primary" onclick="exportOrders()">
                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" alt="Export" width="16" height="16" class="me-2">
                            Xuất báo cáo
                        </button>
                    </div>

                    <!-- Orders Table -->
                    <div class="table-card">
                        <div class="table-header">
                            <h5 class="table-title">Danh sách đơn hàng</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted">Tổng: <?= count($orders) ?> đơn hàng</span>
                                <div class="dropdown">
                                    <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                                        <img src="https://cdn-icons-png.flaticon.com/512/2311/2311524.png" alt="More" width="20" height="20">
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Xuất Excel</a></li>
                                        <li><a class="dropdown-item" href="#">Xuất PDF</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>Mã đơn hàng</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Số lượng SP</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($orders)): ?>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td><input type="checkbox" class="form-check-input order-checkbox"></td>
                                                <td>
                                                    <a href="order-details.html?id=<?= $order['order_id'] ?>" class="text-decoration-none">
                                                        #<?= $order['order_id'] ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User" width="24" height="24" class="rounded-circle me-2">
                                                        <div>
                                                            <div class="fw-bold"><?= htmlspecialchars($order['customer_name'] ?? 'Khách vãng lai') ?></div>
                                                            <div class="text-muted small"><?= htmlspecialchars($order['email']) ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?= $order['item_count'] ?> SP</span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?= number_format($order['total_amount']) ?>₫</div>
                                                    <?php if ($order['discount_amount'] > 0): ?>
                                                        <div class="text-success small">-<?= number_format($order['discount_amount']) ?>₫</div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm status-select" 
                                                            data-order-id="<?= $order['order_id'] ?>"
                                                            onchange="updateOrderStatus(<?= $order['order_id'] ?>, this.value)">
                                                        <option value="pending" <?= $order['order_status'] == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                                        <option value="paid" <?= $order['order_status'] == 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
                                                        <option value="shipped" <?= $order['order_status'] == 'shipped' ? 'selected' : '' ?>>Đã giao hàng</option>
                                                        <option value="delivered" <?= $order['order_status'] == 'delivered' ? 'selected' : '' ?>>Đã giao</option>
                                                        <option value="cancelled" <?= $order['order_status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                onclick="viewOrderDetails(<?= $order['order_id'] ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828270.png" alt="View" width="14" height="14">
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-secondary" 
                                                                onclick="printOrder(<?= $order['order_id'] ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" alt="Print" width="14" height="14">
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <p class="text-muted">Không có đơn hàng nào</p>
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
                                                <a class="page-link" href="?page=<?= $page - 1 ?>&status=<?= $status ?>">Trước</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>&status=<?= $status ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page + 1 ?>&status=<?= $status ?>">Sau</a>
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
                title: 'Đơn hàng',
                breadcrumb: 'Home > Đơn hàng',
                showDateRange: false
            }
        };

        // Functions
        function filterByStatus() {
            const status = document.getElementById('statusFilter').value;
            window.location.href = `?status=${status}`;
        }

        function updateOrderStatus(orderId, status) {
            if (confirm('Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng?')) {
                fetch('../ajax/update-order-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `order_id=${orderId}&status=${status}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cập nhật trạng thái thành công!');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                        location.reload(); // Reload to reset select
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi cập nhật trạng thái');
                    location.reload(); // Reload to reset select
                });
            } else {
                location.reload(); // Reload to reset select
            }
        }

        function viewOrderDetails(orderId) {
            window.location.href = `order-details.html?id=${orderId}`;
        }

        function printOrder(orderId) {
            window.open(`order-details.html?id=${orderId}&print=1`, '_blank');
        }

        function exportOrders() {
            alert('Tính năng xuất báo cáo đang được phát triển');
        }

        // Select all checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.order-checkbox');
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
