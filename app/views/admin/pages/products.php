<?php
/**
 * Products Page - Quản lý sản phẩm với database
 * Chuyển đổi từ products.html với OOP MVC
 */

// Thiết lập
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Include models và controllers
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';

try {
    // Khởi tạo models
    $productModel = new ProductModel();
    $categoryModel = new CategoryModel();
    
    // Lấy dữ liệu từ database
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $keyword = $_GET['search'] ?? '';
    $category_id = $_GET['category'] ?? null;
    
    // Lấy sản phẩm với categories
    $products = $productModel->getProductsWithCategories($limit, $offset);
    $categories = $categoryModel->getAll();
    $totalProducts = $productModel->count();
    $totalPages = ceil($totalProducts / $limit);
    
    // Thiết lập dữ liệu cho view
    $pageTitle = 'JEWELLERY Admin - Sản phẩm';
    $currentPage = 'products';
    $baseUrl = '../';
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $products = [];
    $categories = [];
    $totalProducts = 0;
    $totalPages = 0;
    $pageTitle = 'JEWELLERY Admin - Sản phẩm';
    $currentPage = 'products';
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
                    <!-- Search and Filter -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex gap-3">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm..." 
                                       value="<?= htmlspecialchars($keyword) ?>" id="searchInput">
                                <button class="btn btn-outline-secondary" type="button" onclick="searchProducts()">
                                    <img src="https://cdn-icons-png.flaticon.com/512/54/54481.png" alt="Search" width="16" height="16">
                                </button>
                            </div>
                            <select class="form-select" style="width: 200px;" id="categoryFilter" onchange="filterByCategory()">
                                <option value="">Tất cả danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['category_id'] ?>" 
                                            <?= $category_id == $category['category_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button class="btn btn-primary" onclick="window.location.href='add-product.html'">
                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" alt="Add" width="16" height="16" class="me-2">
                            Thêm sản phẩm
                        </button>
                    </div>

                    <!-- Products Table -->
                    <div class="table-card">
                        <div class="table-header">
                            <h5 class="table-title">Danh sách sản phẩm</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted">Tổng: <?= count($products) ?> sản phẩm</span>
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
                                        <th>Sản phẩm</th>
                                        <th>Mô tả</th>
                                        <th>SKU</th>
                                        <th>Giá</th>
                                        <th>Tồn kho</th>
                                        <th>Danh mục</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($products)): ?>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td><input type="checkbox" class="form-check-input product-checkbox"></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=50&h=50&fit=crop" 
                                                             alt="Product" class="product-img me-3">
                                                        <div>
                                                            <div class="fw-bold"><?= htmlspecialchars($product['name']) ?></div>
                                                           
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($product['description'] ?? '') ?></td> <!-- Cột mô tả riêng -->
                                                <td><?= htmlspecialchars($product['sku']) ?></td>
                                                <td><?= number_format($product['base_price']) ?>₫</td>
                                                <td>
                                                    <span class="badge <?= $product['total_stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                        <?= $product['total_stock'] ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($product['category_names'] ?? 'Chưa phân loại') ?></td>
                                                <td>
                                                    <span class="badge <?= $product['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                                        <?= $product['is_active'] ? 'Hoạt động' : 'Tạm dừng' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                onclick="editProduct(<?= $product['product_id'] ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828270.png" alt="Edit" width="14" height="14">
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteProduct(<?= $product['product_id'] ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1214/1214428.png" alt="Delete" width="14" height="14">
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <p class="text-muted">Không tìm thấy sản phẩm nào</p>
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
                                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($keyword) ?>&category=<?= $category_id ?>">Trước</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($keyword) ?>&category=<?= $category_id ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($keyword) ?>&category=<?= $category_id ?>">Sau</a>
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
                categories: <?= json_encode($categories ?? []) ?>,
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: 'Sản phẩm',
                breadcrumb: 'Home > Sản phẩm',
                showDateRange: false
            }
        };

        // Functions
        function searchProducts() {
            const keyword = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            window.location.href = `?search=${encodeURIComponent(keyword)}&category=${category}`;
        }

        function filterByCategory() {
            const keyword = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            window.location.href = `?search=${encodeURIComponent(keyword)}&category=${category}`;
        }

        function editProduct(productId) {
            window.location.href = `product-details.html?id=${productId}`;
        }

        function deleteProduct(productId) {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                fetch('../ajax/delete-product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Xóa sản phẩm thành công!');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa sản phẩm');
                });
            }
        }

        // Select all checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.product-checkbox');
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
