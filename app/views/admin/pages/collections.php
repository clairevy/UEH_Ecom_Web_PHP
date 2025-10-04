<?php
/**
 * Collections Page - Quản lý bộ sưu tập với database
 * Chuyển đổi từ collections.html với OOP MVC
 */

// Thiết lập
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Include models
require_once __DIR__ . '/../models/CollectionModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

try {
    // Khởi tạo models
    $collectionModel = new CollectionModel();
    $productModel = new ProductModel();
    
    // Lấy dữ liệu từ database
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $keyword = $_GET['search'] ?? '';
    
    // Lấy collections
    if (!empty($keyword)) {
        $collections = $collectionModel->searchCollections($keyword, $limit, $offset);
    } else {
        $collections = $collectionModel->getCollectionsWithDetails($limit, $offset);
    }
    
    $totalCollections = $collectionModel->count();
    $totalPages = ceil($totalCollections / $limit);
    
    // Lấy thống kê
    $activeCollections = $collectionModel->count(['is_active' => 1]);
    $inactiveCollections = $totalCollections - $activeCollections;
    
    // Thiết lập dữ liệu cho view
    $pageTitle = 'JEWELLERY Admin - Bộ sưu tập';
    $currentPage = 'collections';
    $baseUrl = '../';
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $collections = [];
    $totalCollections = 0;
    $totalPages = 0;
    $activeCollections = 0;
    $inactiveCollections = 0;
    $pageTitle = 'JEWELLERY Admin - Bộ sưu tập';
    $currentPage = 'collections';
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
                    <!-- Collections Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <img src="https://cdn-icons-png.flaticon.com/512/3208/3208676.png" alt="Total Collections" width="32" height="32" class="me-2">
                                        <h3 class="mb-0 text-primary"><?= number_format($totalCollections) ?></h3>
                                    </div>
                                    <h6 class="fw-bold">Tổng Bộ Sưu Tập</h6>
                                    <p class="text-muted small mb-0">Tất cả collections</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" alt="Active Collections" width="32" height="32" class="me-2">
                                        <h3 class="mb-0 text-success"><?= number_format($activeCollections) ?></h3>
                                    </div>
                                    <h6 class="fw-bold">Đang Hoạt Động</h6>
                                    <p class="text-muted small mb-0"><?= $totalCollections > 0 ? round(($activeCollections / $totalCollections) * 100, 1) : 0 ?>% tổng số</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" alt="Inactive Collections" width="32" height="32" class="me-2">
                                        <h3 class="mb-0 text-warning"><?= number_format($inactiveCollections) ?></h3>
                                    </div>
                                    <h6 class="fw-bold">Tạm Dừng</h6>
                                    <p class="text-muted small mb-0">Collections không hoạt động</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="This Month" width="32" height="32" class="me-2">
                                        <h3 class="mb-0 text-info"><?= number_format($activeCollections) ?></h3>
                                    </div>
                                    <h6 class="fw-bold">Tháng Này</h6>
                                    <p class="text-muted small mb-0">Collections mới</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex gap-3">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" class="form-control" placeholder="Tìm kiếm bộ sưu tập..." 
                                       value="<?= htmlspecialchars($keyword) ?>" id="searchInput">
                                <button class="btn btn-outline-secondary" type="button" onclick="searchCollections()">
                                    <img src="https://cdn-icons-png.flaticon.com/512/54/54481.png" alt="Search" width="16" height="16">
                                </button>
                            </div>
                            <select class="form-select" style="width: 200px;" id="statusFilter" onchange="filterByStatus()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="1" <?= isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '' ?>>Hoạt động</option>
                                <option value="0" <?= isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' ?>>Tạm dừng</option>
                            </select>
                        </div>
                        <button class="btn btn-primary" onclick="window.location.href='add-collection.html'">
                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" alt="Add" width="16" height="16" class="me-2">
                            Thêm bộ sưu tập
                        </button>
                    </div>

                    <!-- Collections Table -->
                    <div class="table-card">
                        <div class="table-header">
                            <h5 class="table-title">Danh sách bộ sưu tập</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted">Tổng: <?= count($collections) ?> bộ sưu tập</span>
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
                                        <th>Bộ sưu tập</th>
                                        <th>Mô tả</th>
                                        <th>Sản phẩm</th>
                                        <th>Người tạo</th>
                                        <th>Ngày tạo</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($collections)): ?>
                                        <?php foreach ($collections as $collection): ?>
                                            <tr>
                                                <td><input type="checkbox" class="form-check-input collection-checkbox"></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?= $collection['cover_image'] ?: 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=50&h=50&fit=crop' ?>" 
                                                             alt="Collection" class="collection-img me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                                        <div>
                                                            <div class="fw-bold"><?= htmlspecialchars($collection['name']) ?></div>
                                                            <div class="text-muted small"><?= htmlspecialchars($collection['slug']) ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($collection['description']) ?>">
                                                        <?= htmlspecialchars($collection['description']) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary"><?= $collection['product_count'] ?> sản phẩm</span>
                                                </td>
                                                <td><?= htmlspecialchars($collection['created_by_name'] ?? 'Admin') ?></td>
                                                <td><?= date('d/m/Y', strtotime($collection['created_at'])) ?></td>
                                                <td>
                                                    <span class="badge <?= $collection['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                                        <?= $collection['is_active'] ? 'Hoạt động' : 'Tạm dừng' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                onclick="editCollection(<?= $collection['collection_id'] ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828270.png" alt="Edit" width="14" height="14">
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-info" 
                                                                onclick="viewCollection(<?= $collection['collection_id'] ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/709/709612.png" alt="View" width="14" height="14">
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteCollection(<?= $collection['collection_id'] ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1214/1214428.png" alt="Delete" width="14" height="14">
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <p class="text-muted">Không tìm thấy bộ sưu tập nào</p>
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
                    orders: 'orders.php',
                    categories: 'categories.php',
                    customers: 'customers.php',
                    collections: 'collections.php'
                }
            },
            header: {
                title: 'Bộ sưu tập',
                breadcrumb: 'Home > Bộ sưu tập',
                showDateRange: false
            }
        };

        // Functions
        function searchCollections() {
            const keyword = document.getElementById('searchInput').value;
            window.location.href = `?search=${encodeURIComponent(keyword)}`;
        }

        function filterByStatus() {
            const status = document.getElementById('statusFilter').value;
            const keyword = document.getElementById('searchInput').value;
            let url = '?';
            if (keyword) url += `search=${encodeURIComponent(keyword)}&`;
            if (status) url += `status=${status}`;
            window.location.href = url;
        }

        function editCollection(collectionId) {
            window.location.href = `add-collection.html?id=${collectionId}`;
        }

        function viewCollection(collectionId) {
            window.location.href = `collection-details.html?id=${collectionId}`;
        }

        function deleteCollection(collectionId) {
            if (confirm('Bạn có chắc chắn muốn xóa bộ sưu tập này?')) {
                fetch('../ajax/delete-collection.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `collection_id=${collectionId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Xóa bộ sưu tập thành công!');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa bộ sưu tập');
                });
            }
        }

        // Select all checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.collection-checkbox');
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
