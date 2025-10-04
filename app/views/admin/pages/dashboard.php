<?php
/**
 * Dashboard Page - Trang chủ admin
 * Hiển thị thống kê tổng quan
 */

// Thiết lập
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Include models
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/UserModel.php';

try {
    // Khởi tạo models
    $productModel = new ProductModel();
    $orderModel = new OrderModel();
    $categoryModel = new CategoryModel();
    $userModel = new UserModel();
    
    // Lấy dữ liệu thống kê
    $totalProducts = $productModel->count();
    $totalOrders = $orderModel->count();
    $totalCategories = $categoryModel->count();
    $totalUsers = $userModel->count();
    
    // Lấy sản phẩm bán chạy (giả sử)
    $bestSellers = [
        ['name' => 'Nhẫn vàng 18K', 'sales' => 45, 'revenue' => 22500000],
        ['name' => 'Dây chuyền bạc', 'sales' => 32, 'revenue' => 16000000],
        ['name' => 'Bông tai kim cương', 'sales' => 28, 'revenue' => 42000000]
    ];
    
    // Thiết lập dữ liệu cho view
    $pageTitle = 'JEWELLERY Admin - Dashboard';
    $currentPage = 'dashboard';
    $baseUrl = '../';
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $totalProducts = 0;
    $totalOrders = 0;
    $totalCategories = 0;
    $totalUsers = 0;
    $bestSellers = [];
    $pageTitle = 'JEWELLERY Admin - Dashboard';
    $currentPage = 'dashboard';
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
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-icon">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" alt="Products" width="24" height="24">
                                </div>
                                <div class="stats-content">
                                    <h3><?= number_format($totalProducts) ?></h3>
                                    <p>Sản phẩm</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-icon">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" alt="Orders" width="24" height="24">
                                </div>
                                <div class="stats-content">
                                    <h3><?= number_format($totalOrders) ?></h3>
                                    <p>Đơn hàng</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-icon">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" alt="Categories" width="24" height="24">
                                </div>
                                <div class="stats-content">
                                    <h3><?= number_format($totalCategories) ?></h3>
                                    <p>Danh mục</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-icon">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" alt="Users" width="24" height="24">
                                </div>
                                <div class="stats-content">
                                    <h3><?= number_format($totalUsers) ?></h3>
                                    <p>Khách hàng</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Best Sellers -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-card">
                                <div class="table-header">
                                    <h5 class="table-title">Sản phẩm bán chạy</h5>
                                </div>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th>Đã bán</th>
                                                <th>Doanh thu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($bestSellers as $item): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                                    <td><?= $item['sales'] ?></td>
                                                    <td><?= number_format($item['revenue']) ?>₫</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="table-card">
                                <div class="table-header">
                                    <h5 class="table-title">Thống kê nhanh</h5>
                                </div>
                                <div class="p-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tổng sản phẩm:</span>
                                        <strong><?= number_format($totalProducts) ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tổng đơn hàng:</span>
                                        <strong><?= number_format($totalOrders) ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Danh mục:</span>
                                        <strong><?= number_format($totalCategories) ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Khách hàng:</span>
                                        <strong><?= number_format($totalUsers) ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                    orders: 'orders.php',
                    categories: 'categories.php',
                    customers: 'customers.php'
                }
            },
            header: {
                title: 'Dashboard',
                breadcrumb: 'Home',
                showDateRange: false
            }
        };
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $baseUrl ?>assets/js/main.js"></script>
</body>
</html>
