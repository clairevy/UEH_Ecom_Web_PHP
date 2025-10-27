<?php
/**
 * Categories View - Pure MVC View
 * Hiển thị danh sách categories từ database, không có hardcode
 * 
 * Biến được truyền từ Controller:
 * - $categories: Danh sách categories từ database
 * - $title: Tiêu đề trang
 */
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Danh Mục') ?> - KICKS Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="app/views/admin/assets/css/variables.css">
    <link rel="stylesheet" href="app/views/admin/assets/css/main.css">
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
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Thành công:</strong> <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Lỗi:</strong> <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Categories Management Table -->
                <div class="table-card">
                    <div class="table-header">
                        <h5 class="table-title">Danh Sách Danh Mục</h5>
                        <div class="d-flex gap-2">
                            <!-- Search -->
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm..." style="width: 200px; padding-left: 35px;" id="categorySearch">
                                <img src="https://cdn-icons-png.flaticon.com/512/751/751463.png" alt="Search" width="16" height="16" class="position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);">
                            </div>
                            
                            <!-- Add Category Button -->
                            <button class="btn btn-success-custom btn-sm" onclick="window.location.href='index.php?url=add-category'">
                                <img src="https://cdn-icons-png.flaticon.com/512/748/748113.png" alt="Add" width="16" height="16" class="me-1">
                                <span class="d-none d-md-inline">Thêm Danh Mục</span>
                                <span class="d-md-none">Thêm</span>
                            </button>
                            
                            <!-- More Actions -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2311/2311524.png" alt="More" width="16" height="16">
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Xuất Excel</a></li>
                                    <li><a class="dropdown-item" href="#">Xuất PDF</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" id="categoriesTable">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th width="60">Icon</th>
                                    <th>Tên Danh Mục</th>
                                    <th>Mô Tả</th>
                                    <th>Số Sản Phẩm</th>
                                    <th>Trạng Thái</th>
                                    <th>Ngày Tạo</th>
                                    <th width="100">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><input type="checkbox" class="form-check-input row-checkbox"></td>
                                            <td>
                                                <?php if (!empty($category->icon_url)): ?>
                                                    <img src="<?= htmlspecialchars($category->icon_url) ?>" alt="Icon" width="32" height="32">
                                                <?php else: ?>
                                                    <img src="https://cdn-icons-png.flaticon.com/512/2249/2249884.png" alt="Icon" width="32" height="32">
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?= htmlspecialchars($category->name) ?></div>
                                                <div class="small text-muted">ID: #<?= str_pad($category->category_id, 6, '0', STR_PAD_LEFT) ?></div>
                                            </td>
                                            <td class="text-muted"><?= htmlspecialchars($category->description ?? 'Không có mô tả') ?></td>
                                            <td>
                                                <div class="text-center">
                                                    <span class="badge bg-info"><?= $category->product_count ?? 0 ?></span>
                                                    <div class="small text-muted">sản phẩm</div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($category->is_active): ?>
                                                    <span class="badge badge-delivered badge-custom">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge badge-canceled badge-custom">Không hoạt động</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="text-nowrap">
                                                    <?= date('d/m/Y', strtotime($category->created_at)) ?>
                                                    <br>
                                                    <small class="text-muted"><?= date('H:i', strtotime($category->created_at)) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-primary" 
                                                            onclick="editCategory('<?= $category->category_id ?>')" 
                                                            title="Chỉnh sửa">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/1159/1159633.png" alt="Edit" width="14" height="14">
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="deleteCategory('<?= $category->category_id ?>')" 
                                                            title="Xóa">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="14" height="14">
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="No Data" width="48" height="48" class="mb-3 opacity-50">
                                                <p>Chưa có danh mục nào</p>
                                                <button class="btn btn-primary" onclick="window.location.href='index.php?url=add-category'">
                                                    Thêm danh mục đầu tiên
                                                </button>
                                            </div>
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
    <script src="app/views/admin/components/component-manager.js"></script>
    
    <!-- Page Configuration -->
    <script>
        // Prepare categories data từ PHP
        <?php
        $categoriesData = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $categoriesData[] = [
                    'name' => $category->name,
                    'count' => $category->product_count ?? 0
                ];
            }
        }
        ?>
        
        window.pageConfig = {
            sidebar: {
                brandName: 'KICKS',
                activePage: 'categories',
                links: {
                    dashboard: 'index.php?url=dashboard',
                    products: 'index.php?url=products',
                    categories: 'index.php?url=categories',
                    collections: 'index.php?url=collections',
                    orders: 'index.php?url=orders',
                    customers: 'index.php?url=customers',
                    reviews: 'index.php?url=reviews'
                },
                categories: <?= json_encode($categoriesData) ?>,
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: 'Danh Mục',
                breadcrumb: 'Home > Danh Mục > Tất cả danh mục'
            }
        };

        /**
         * Category Management Functions
         */
        function editCategory(categoryId) {
            window.location.href = 'index.php?url=edit-category&id=' + categoryId;
        }
        
        function deleteCategory(categoryId) {
            if (confirm('Bạn có chắc chắn muốn xóa danh mục này? Hành động này không thể hoàn tác!')) {
                // Create form for POST delete
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php?url=categories&action=delete&id=' + categoryId;
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        /**
         * Search functionality
         */
        document.getElementById('categorySearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#categoriesTable tbody tr');
            
            rows.forEach(row => {
                const categoryName = row.querySelector('.fw-bold')?.textContent.toLowerCase() || '';
                if (categoryName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        /**
         * Select all checkbox functionality
         */
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="app/views/admin/assets/js/main.js"></script>
    
    <!-- Categories JS -->
    <script src="app/views/admin/assets/js/categories.js"></script>
</body>
</html>
