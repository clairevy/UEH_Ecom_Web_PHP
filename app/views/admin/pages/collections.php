<?php
/**
 * Collections View - Pure MVC View
 * Hiển thị data từ database, không có hardcode
 */

// Biến được truyền từ Controller:
// $collections - danh sách collections từ database
// $stats - thống kê collections
// $message - thông báo thành công
// $error - thông báo lỗi
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JEWELLERY Admin - Collections Management</title>
    
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

                <!-- Collections Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3208/3208676.png" alt="Total Collections" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-primary"><?= $stats['total'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Tổng Bộ Sưu Tập</h6>
                                <p class="text-muted small mb-0">Tất cả bộ sưu tập</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" alt="Active Collections" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-success"><?= $stats['active'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Đang Hoạt Động</h6>
                                <p class="text-muted small mb-0"><?= $stats['total'] > 0 ? number_format(($stats['active'] / $stats['total']) * 100, 1) : 0 ?>% tổng số</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3081/3081559.png" alt="Products" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-warning"><?= $stats['total_products'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Tổng Sản Phẩm</h6>
                                <p class="text-muted small mb-0">Trong các bộ sưu tập</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3208/3208676.png" alt="Best Seller" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-info"><?= $stats['best_sellers'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Bán Chạy Nhất</h6>
                                <p class="text-muted small mb-0">Bộ sưu tập hàng đầu</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collections Management Table -->
                <div class="table-card">
                    <div class="table-header">
                        <h5 class="table-title">Quản Lý Bộ Sưu Tập</h5>
                        <div class="d-flex gap-2">
                            <!-- Search -->
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm bộ sưu tập..." style="width: 200px; padding-left: 35px;" id="collectionSearch">
                                <img src="https://cdn-icons-png.flaticon.com/512/751/751463.png" alt="Search" width="16" height="16" class="position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);">
                            </div>
                            
                            <!-- Add Collection -->
                            <button class="btn btn-success-custom btn-sm" onclick="addNewCollection()">
                                <img src="https://cdn-icons-png.flaticon.com/512/748/748113.png" alt="Add" width="16" height="16" class="me-1">
                                Thêm Bộ Sưu Tập
                            </button>
                            
                            <!-- More Actions -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2311/2311524.png" alt="More" width="16" height="16">
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Xuất Excel</a></li>
                                    <li><a class="dropdown-item" href="#">Nhập dữ liệu</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" id="collectionsTable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>ID</th>
                                    <th>Tên Bộ Sưu Tập</th>
                                    <th>Slug</th>
                                    <th>Mô Tả</th>
                                    <th>Số Sản Phẩm</th>
                                    <th>Trạng Thái</th>
                                    <th>Ngày Tạo</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($collections)): ?>
                                    <?php foreach ($collections as $collection): ?>
                                        <tr>
                                            <td><input type="checkbox" class="form-check-input row-checkbox"></td>
                                            <td class="fw-bold">#<?= $collection->collection_id ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://cdn-icons-png.flaticon.com/512/3208/3208676.png" alt="Collection" width="32" height="32" class="me-2">
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($collection->collection_name) ?></div>
                                                        <div class="small text-muted"><?= htmlspecialchars($collection->slug) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><code><?= htmlspecialchars($collection->slug) ?></code></td>
                                            <td><?= htmlspecialchars($collection->description ?? 'Không có mô tả') ?></td>
                                            <td>
                                                <div class="text-center">
                                                    <span class="fw-bold text-primary"><?= $collection->product_count ?? 0 ?></span>
                                                    <div class="small text-muted">sản phẩm</div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($collection->is_active): ?>
                                                    <span class="badge badge-delivered badge-custom">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge badge-canceled badge-custom">Không hoạt động</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($collection->created_at)) ?></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/2311/2311524.png" alt="Actions" width="16" height="16">
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="viewCollection(<?= $collection->collection_id ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/709/709612.png" alt="View" width="16" height="16" class="me-2">
                                                            Xem chi tiết
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="editCollection(<?= $collection->collection_id ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1159/1159633.png" alt="Edit" width="16" height="16" class="me-2">
                                                            Chỉnh sửa
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="viewProducts(<?= $collection->collection_id ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/3081/3081559.png" alt="Products" width="16" height="16" class="me-2">
                                                            Xem sản phẩm
                                                        </a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-warning" href="#" onclick="toggleCollection(<?= $collection->collection_id ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828843.png" alt="Toggle" width="16" height="16" class="me-2">
                                                            <?= $collection->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' ?>
                                                        </a></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteCollection(<?= $collection->collection_id ?>)">
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
                                            <p class="text-muted mb-0">Chưa có bộ sưu tập nào</p>
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
                activePage: 'collections',
                links: {
                    dashboard: '/admin/index.php?url=dashboard',
                    products: '/admin/index.php?url=products',
                    categories: '/admin/index.php?url=categories',
                    orders: '/admin/index.php?url=orders',
                    customers: '/admin/index.php?url=customers',
                    collections: '/admin/index.php?url=collections',
                    reviews: '/admin/index.php?url=reviews',
                    media: '/admin/index.php?url=media'
                }
            }
        };
    </script>
    
    <!-- Collections Page Script -->
    <script src="../assets/js/collections.js"></script>
    
    <!-- Collection Management Functions -->
    <script>
        function addNewCollection() {
            window.location.href = '/admin/index.php?url=collections&action=create';
        }
        
        function viewCollection(collectionId) {
            window.location.href = '/admin/index.php?url=collections&action=show&id=' + collectionId;
        }
        
        function editCollection(collectionId) {
            window.location.href = '/admin/index.php?url=collections&action=edit&id=' + collectionId;
        }
        
        function viewProducts(collectionId) {
            window.location.href = '/admin/index.php?url=products&collection=' + collectionId;
        }
        
        function toggleCollection(collectionId) {
            if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái của bộ sưu tập này không?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/index.php?url=collections&action=toggle&id=' + collectionId;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function deleteCollection(collectionId) {
            if (confirm('Bạn có chắc chắn muốn xóa bộ sưu tập này không?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/index.php?url=collections&action=delete&id=' + collectionId;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

