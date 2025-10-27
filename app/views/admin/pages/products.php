<?php
/**
 * Products View - Pure MVC View
 * Hiển thị data từ database, không có hardcode
 */

// Biến được truyền từ Controller:
// $products - danh sách products từ database
// $categories - danh sách categories từ database
// $collections - danh sách collections từ database
// $materials - danh sách materials có sẵn
// $materialStats - thống kê theo material
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KICKS Admin - All Products</title>
    
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
                <?php if (!empty($flashMessage)): ?>
                    <?= $flashMessage ?>
                <?php endif; ?>
                
                <!-- Product Management Table -->
                <div class="table-card">
                    <div class="table-header">
                        <h5 class="table-title">Danh Sách Sản Phẩm</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <!-- Filters -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3031/3031293.png" alt="Filter" width="16" height="16" class="me-1 d-none d-md-inline">
                                    <span class="d-none d-md-inline">Lọc</span>
                                    <span class="d-md-none">
                                        <img src="https://cdn-icons-png.flaticon.com/512/3031/3031293.png" alt="Filter" width="16" height="16">
                                    </span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="filterProducts('all')">Tất cả</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterProducts('active')">Đang bán</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterProducts('inactive')">Ngừng bán</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterProducts('best-seller')">Bán chạy</a></li>
                                </ul>
                            </div>
                            
                            <!-- Search -->
                            <div class="position-relative flex-grow-1" style="max-width: 300px;">
                                <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm..." style="padding-left: 35px;" id="productSearch">
                                <img src="https://cdn-icons-png.flaticon.com/512/751/751463.png" alt="Search" width="16" height="16" class="position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);">
                            </div>
                            
                            <!-- Add Product -->
                            <button class="btn btn-success-custom btn-sm" onclick="window.location.href='index.php?url=add-product'">
                                <img src="https://cdn-icons-png.flaticon.com/512/748/748113.png" alt="Add" width="16" height="16" class="me-1">
                                <span class="d-none d-md-inline">Thêm Sản Phẩm</span>
                                <span class="d-md-none">Thêm</span>
                            </button>
                            
                          
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" id="productsTable">
                            <thead>
                                <tr>
                                    <th width="50" class="d-none d-md-table-cell">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>Sản Phẩm</th>
                                    <th class="d-none d-lg-table-cell">Chất Liệu</th>
                                    <th class="d-none d-lg-table-cell">Danh Mục</th>
                                    <th>Giá</th>
                                    <th class="d-none d-md-table-cell">SKU</th>
                                    <th class="d-none d-sm-table-cell">Trạng Thái</th>
                                    <th class="d-none d-lg-table-cell">Ngày Tạo</th>
                                    <th width="100">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td class="d-none d-md-table-cell">
                                                <input type="checkbox" class="form-check-input row-checkbox">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php 
                                                    // Load ảnh từ database (images + image_usages)
                                                    $imageSrc = 'https://via.placeholder.com/40x40?text=No+Image';
                                                    
                                                    if (!empty($product->primary_image) && isset($product->primary_image->file_path)) {
                                                        // Ưu tiên: primary_image từ image_usages
                                                        $imageSrc = htmlspecialchars($product->primary_image->file_path);
                                                    } elseif (!empty($product->main_image)) {
                                                        // Fallback: main_image column
                                                        $imageSrc = htmlspecialchars($product->main_image);
                                                    }
                                                    ?>
                                                    <img src="<?= $imageSrc ?>" 
                                                         alt="Product" 
                                                         width="40" 
                                                         height="40" 
                                                         class="rounded me-2"
                                                         onerror="this.src='https://via.placeholder.com/40x40?text=No+Image'">
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($product->name) ?></div>
                                                        <div class="small text-muted">SKU: #<?= htmlspecialchars($product->sku ?? 'N/A') ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <span class="badge bg-warning text-dark">
                                                    <?= htmlspecialchars(ucfirst($product->material ?? 'gold')) ?>
                                                </span>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <span class="badge bg-info text-dark">
                                                    <?= htmlspecialchars($product->category_name ?? 'Chưa phân loại') ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold text-success">
                                                <?= number_format($product->base_price, 0, ',', '.') ?> VND
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <code><?= htmlspecialchars($product->sku ?? 'N/A') ?></code>
                                            </td>
                                            <td class="d-none d-sm-table-cell">
                                                <?php if ($product->is_active): ?>
                                                    <span class="badge badge-delivered badge-custom">Đang bán</span>
                                                <?php else: ?>
                                                    <span class="badge badge-canceled badge-custom">Ngừng bán</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <div class="text-nowrap">
                                                    <?= date('d/m/Y', strtotime($product->created_at)) ?>
                                                    <br>
                                                    <small class="text-muted"><?= date('H:i', strtotime($product->created_at)) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    
                                                    <button type="button" class="btn btn-outline-success" 
                                                            onclick="editProduct(<?= $product->product_id ?>)" title="Chỉnh sửa">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/1159/1159633.png" alt="Edit" width="14" height="14">
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="deleteProduct(<?= $product->product_id ?>)" title="Xóa">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="14" height="14">
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="No Data" width="48" height="48" class="mb-3 opacity-50">
                                                <p>Chưa có sản phẩm nào</p>
                                                <button class="btn btn-primary" onclick="window.location.href='index.php?url=add-product'">
                                                    Thêm sản phẩm đầu tiên
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
        // Prepare categories data
        <?php
        $categoriesData = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $categoriesData[] = [
                    'name' => $category->name,
                    'count' => $category->product_count ?? 0
                ];
            }
        } else {
            $categoriesData = [
                ['name' => 'Vòng cổ', 'count' => 0],
                ['name' => 'Vòng tay', 'count' => 0],
                ['name' => 'Bông tai', 'count' => 0],
                ['name' => 'Nhẫn', 'count' => 0],
                ['name' => 'Lắc chân', 'count' => 0]
            ];
        }
        ?>
        
        window.pageConfig = {
            sidebar: {
                brandName: 'KICKS',
                activePage: 'products',
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
                title: 'Sản Phẩm',
                breadcrumb: 'Home > Sản Phẩm > Tất cả sản phẩm'
            }
        };

        // Product management functions
        function viewProduct(productId) {
            window.location.href = 'index.php?url=product-details&id=' + productId;
        }

        function editProduct(productId) {
            window.location.href = 'index.php?url=edit-product&id=' + productId;
        }

        function deleteProduct(productId) {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này? Hành động này không thể hoàn tác!')) {
                window.location.href = 'index.php?url=products&action=delete&id=' + productId;
            }
        }

        function filterProducts(filter) {
            console.log('Filtering products by:', filter);
            // Implement filtering logic here
        }

        // Search functionality
        document.getElementById('productSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#productsTable tbody tr');
            
            rows.forEach(row => {
                const productName = row.querySelector('.fw-bold')?.textContent.toLowerCase() || '';
                if (productName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Select all checkbox functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Initialize pagination
        document.addEventListener('DOMContentLoaded', function() {
            const paginationContainer = document.getElementById('pagination-container');
            if (paginationContainer && window.ComponentManager) {
                window.ComponentManager.renderPagination(paginationContainer, {
                    ariaLabel: 'Products pagination'
                });
            }
        });
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="app/views/admin/assets/js/main.js"></script>
    
    <!-- Products JS -->
    <script src="app/views/admin/assets/js/products.js"></script>
</body>
</html>