<?php
/**
 * Categories Page - Quản lý danh mục với database
 * Chuyển đổi từ categories.html với OOP MVC
 */

// Thiết lập
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Include models
require_once __DIR__ . '/../models/CategoryModel.php';

try {
    // Khởi tạo model
    $categoryModel = new CategoryModel();
    
    // Lấy dữ liệu từ database
    $categories = $categoryModel->getCategoriesWithProductCount();
    
    // Thiết lập dữ liệu cho view
    $pageTitle = 'JEWELLERY Admin - Danh mục';
    $currentPage = 'categories';
    $baseUrl = '../';
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $categories = [];
    $pageTitle = 'JEWELLERY Admin - Danh mục';
    $currentPage = 'categories';
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
                    <!-- Categories Management -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4>Quản lý danh mục</h4>
                        <button class="btn btn-primary" onclick="addCategory()">
                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828817.png" alt="Add" width="16" height="16" class="me-2">
                            Thêm danh mục
                        </button>
                    </div>

                    <!-- Categories Table -->
                    <div class="table-card">
                        <div class="table-header">
                            <h5 class="table-title">Danh sách danh mục</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted">Tổng: <?= count($categories) ?> danh mục</span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên danh mục</th>
                                        <th>Slug</th>
                                        <th>Số sản phẩm</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td><?= $category['category_id'] ?></td>
                                                <td>
                                                    <div class="fw-bold"><?= htmlspecialchars($category['name']) ?></div>
                                                    <?php if ($category['parent_id']): ?>
                                                        <div class="text-muted small">Danh mục con</div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <code><?= htmlspecialchars($category['slug']) ?></code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info"><?= $category['product_count'] ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $category['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                                        <?= $category['is_active'] ? 'Hoạt động' : 'Tạm dừng' ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($category['created_at'])) ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                onclick="editCategory(<?= $category['category_id'] ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828270.png" alt="Edit" width="14" height="14">
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteCategory(<?= $category['category_id'] ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1214/1214428.png" alt="Delete" width="14" height="14">
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <p class="text-muted">Không có danh mục nào</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
                    orders: 'orders.php'
                },
                categories: <?= json_encode($categories ?? []) ?>,
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: 'Danh mục',
                breadcrumb: 'Home > Danh mục',
                showDateRange: false
            }
        };

        // Functions
        function addCategory() {
            const name = prompt('Nhập tên danh mục:');
            if (name) {
                const slug = name.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
                // Gọi API tạo danh mục
                fetch('../ajax/create-category.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `name=${encodeURIComponent(name)}&slug=${encodeURIComponent(slug)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Tạo danh mục thành công!');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi tạo danh mục');
                });
            }
        }

        function editCategory(categoryId) {
            const newName = prompt('Nhập tên danh mục mới:');
            if (newName) {
                const slug = newName.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
                // Gọi API cập nhật danh mục
                fetch('../ajax/update-category.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `category_id=${categoryId}&name=${encodeURIComponent(newName)}&slug=${encodeURIComponent(slug)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cập nhật danh mục thành công!');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi cập nhật danh mục');
                });
            }
        }

        function deleteCategory(categoryId) {
            if (confirm('Bạn có chắc chắn muốn xóa danh mục này?')) {
                fetch('../ajax/delete-category.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `category_id=${categoryId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Xóa danh mục thành công!');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa danh mục');
                });
            }
        }
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $baseUrl ?>assets/js/main.js"></script>
</body>
</html>
