<?php
/**
 * Categories View - Pure MVC View
 * Hiển thị data từ database, không có hardcode
 */

// Biến được truyền từ Controller:
// $categories - danh sách categories từ database
// $message - thông báo thành công
// $error - thông báo lỗi
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JEWELLERY Admin - Categories Management</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Component -->
        <div id="sidebar-container"></div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header Component -->
            <div id="header-container"></div>

            <!-- Page Content -->
            <main class="content-area">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Quản Lý Danh Mục</h2>
                        <p class="text-muted mb-0">Quản lý các danh mục sản phẩm trang sức</p>
                    </div>
                </div>

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

                <!-- Add Category Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <img src="https://cdn-icons-png.flaticon.com/512/748/748113.png" alt="Add Category" width="24" height="24" class="me-2">
                            Thêm Danh Mục Mới
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="addCategoryForm" method="POST" action="/admin/index.php?url=categories" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="categoryName" class="form-label">Tên Danh Mục <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="categoryName" name="name" placeholder="VD: Nhẫn, Dây chuyền..." required>
                                </div>
                                <div class="col-md-3">
                                    <label for="categoryDescription" class="form-label">Mô Tả</label>
                                    <input type="text" class="form-control" id="categoryDescription" name="description" placeholder="Mô tả ngắn về danh mục">
                                </div>
                                <div class="col-md-2">
                                    <label for="categoryImage" class="form-label">Ảnh Danh Mục <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="categoryImage" name="image" accept="image/*" required>
                                    <div class="small text-muted mt-1">Max 2MB</div>
                                </div>
                                <div class="col-md-2">
                                    <label for="categoryIcon" class="form-label">Icon URL</label>
                                    <input type="text" class="form-control" id="categoryIcon" name="icon_url" placeholder="https://...">
                                </div>
                                <div class="col-md-2">
                                    <label for="categoryStatus" class="form-label">Trạng Thái</label>
                                    <select class="form-select" id="categoryStatus" name="is_active" required>
                                        <option value="1">Hoạt động</option>
                                        <option value="0">Không hoạt động</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success-custom btn-custom w-100">
                                        <img src="https://cdn-icons-png.flaticon.com/512/748/748113.png" alt="Add" width="16" height="16">
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Image Preview -->
                            <div class="row mt-3" id="categoryImagePreview" style="display: none;">
                                <div class="col-md-12">
                                    <div class="border rounded p-2 d-inline-block">
                                        <img id="previewImg" src="" alt="Preview" style="max-width: 150px; max-height: 150px;">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Categories Management Table -->
                <div class="table-card">
                    <div class="table-header">
                        <h5 class="table-title">Danh Sách Danh Mục</h5>
                        <div class="d-flex gap-2">
                            <!-- Search -->
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm danh mục..." style="width: 200px; padding-left: 35px;" id="categorySearch">
                                <img src="https://cdn-icons-png.flaticon.com/512/751/751463.png" alt="Search" width="16" height="16" class="position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);">
                            </div>
                            
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
                                            <td><?= date('d/m/Y', strtotime($category->created_at)) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-primary" onclick="editCategory('<?= $category->category_id ?>')" title="Chỉnh sửa">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/1159/1159633.png" alt="Edit" width="14" height="14">
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" onclick="deleteCategory('<?= $category->category_id ?>')" title="Xóa">
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
    <script src="../components/component-manager.js"></script>
    
    <!-- Page Configuration -->
    <script>
        window.pageConfig = {
            sidebar: {
                brandName: 'JEWELLERY',
                activePage: 'categories',
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
    
    <!-- Categories Page Script -->
    <script src="../assets/js/categories.js"></script>
    
    <!-- Category Management Functions -->
    <script>
        function editCategory(categoryId) {
            // Redirect to edit page using Front Controller
            window.location.href = '/admin/index.php?url=categories&action=edit&id=' + categoryId;
        }
        
        function deleteCategory(categoryId) {
            if (confirm('Bạn có chắc chắn muốn xóa danh mục này không?')) {
                // Create form for POST delete (RESTful)
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/index.php?url=categories&action=delete&id=' + categoryId;
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <!-- Bootstrap 5 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>