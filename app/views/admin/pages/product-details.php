<?php
/**
 * Product Details View - Pure MVC View
 * Hiển thị chi tiết sản phẩm từ database
 * Hỗ trợ cả VIEW mode và EDIT mode
 * 
 * Biến được truyền từ Controller:
 * - $product: Thông tin sản phẩm từ database
 * - $categories: Danh sách categories
 * - $collections: Danh sách collections
 * - $materials: Danh sách materials
 * - $editMode: true/false - Cho phép chỉnh sửa hay chỉ xem
 * - $title, $pageTitle, $breadcrumb
 */

$editMode = $editMode ?? false;
$product = $product ?? null;

if (!$product) {
    echo "<div class='alert alert-danger'>Không tìm thấy sản phẩm!</div>";
    return;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Chi Tiết Sản Phẩm') ?> - KICKS Admin</title>
    
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
                <!-- Back Button -->
                <div class="mb-3">
                    <a href="index.php?url=products" class="btn btn-outline-secondary btn-sm">
                        <img src="https://cdn-icons-png.flaticon.com/512/271/271220.png" alt="Back" width="14" height="14" class="me-1">
                        Quay lại Danh Sách
                    </a>
                </div>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Product Form -->
                <form id="productDetailsForm" method="POST" action="index.php?url=products&action=update&id=<?= $product->product_id ?>" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Product Form -->
                        <div class="col-lg-8 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <?= $editMode ? 'Chỉnh Sửa Sản Phẩm' : 'Thông Tin Sản Phẩm' ?>
                                    </h5>

                                    <!-- Product Name -->
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label">Tên Sản Phẩm</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="name" 
                                               name="name" 
                                               value="<?= htmlspecialchars($product->name) ?>"
                                               <?= !$editMode ? 'readonly' : '' ?>>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group mb-3">
                                        <label for="description" class="form-label">Mô Tả</label>
                                        <textarea class="form-control" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="4"
                                                  <?= !$editMode ? 'readonly' : '' ?>><?= htmlspecialchars($product->description ?? '') ?></textarea>
                                    </div>

                                    <!-- Material -->
                                    <div class="form-group mb-3">
                                        <label for="material" class="form-label">Chất Liệu</label>
                                        <select class="form-control" id="material" name="material" <?= !$editMode ? 'disabled' : '' ?>>
                                            <?php 
                                            $materialOptions = is_array($materials) ? $materials : ['gold', 'silver', 'diamond', 'pearl'];
                                            foreach ($materialOptions as $material): 
                                            ?>
                                                <option value="<?= htmlspecialchars($material) ?>"
                                                    <?= $product->material == $material ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars(ucfirst($material)) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- SKU and Price -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="sku" class="form-label">SKU</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="sku" 
                                                       name="sku" 
                                                       value="<?= htmlspecialchars($product->sku ?? '') ?>"
                                                       <?= !$editMode ? 'readonly' : '' ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="base_price" class="form-label">Giá Bán (VND)</label>
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="base_price" 
                                                       name="base_price" 
                                                       value="<?= $product->base_price ?>"
                                                       <?= !$editMode ? 'readonly' : '' ?>>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Collection -->
                                    <div class="form-group mb-3">
                                        <label for="collection_id" class="form-label">Bộ Sưu Tập</label>
                                        <select class="form-control" id="collection_id" name="collection_id" <?= !$editMode ? 'disabled' : '' ?>>
                                            <option value="">-- Không chọn --</option>
                                            <?php if (!empty($collections)): ?>
                                                <?php foreach ($collections as $collection): ?>
                                                    <option value="<?= $collection->collection_id ?>"
                                                        <?= $product->collection_id == $collection->collection_id ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($collection->collection_name) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <!-- Product Status -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">Trạng Thái Sản Phẩm</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <span id="statusBadge" class="badge <?= $product->is_active ? 'badge-delivered' : 'badge-canceled' ?> badge-custom">
                                                <?= $product->is_active ? 'Đang bán' : 'Ngừng bán' ?>
                                            </span>
                                            <?php if ($editMode): ?>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           id="is_active" 
                                                           name="is_active" 
                                                           value="1"
                                                           <?= $product->is_active ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="is_active">
                                                        Kích hoạt sản phẩm
                                                    </label>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2 mt-4">
                                        <?php if ($editMode): ?>
                                            <button type="submit" class="btn btn-primary-custom btn-custom px-4">
                                                <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" alt="Update" width="16" height="16" class="me-1">
                                                CẬP NHẬT
                                            </button>
                                            <button type="button" class="btn btn-danger-custom btn-custom px-4" onclick="deleteProduct(<?= $product->product_id ?>)">
                                                <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="16" height="16" class="me-1">
                                                XÓA
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-success-custom btn-custom px-4" onclick="enableEditMode(<?= $product->product_id ?>)">
                                                <img src="https://cdn-icons-png.flaticon.com/512/1159/1159633.png" alt="Edit" width="16" height="16" class="me-1">
                                                CHỈNH SỬA
                                            </button>
                                        <?php endif; ?>
                                        
                                        <button type="button" class="btn btn-outline-secondary btn-custom px-4" onclick="window.location.href='index.php?url=products'">
                                            <img src="https://cdn-icons-png.flaticon.com/512/189/189665.png" alt="Cancel" width="16" height="16" class="me-1">
                                            <?= $editMode ? 'HỦY' : 'ĐÓNG' ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Images -->
                        <div class="col-lg-4 mb-4">
                            <!-- Primary Image -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3">
                                        <span class="badge bg-primary me-2">Primary</span>
                                        Ảnh Chính
                                    </h6>
                                    
                                    <!-- Display Primary Image -->
                                    <div class="text-center mb-3">
                                        <?php 
                                        // Ưu tiên load từ image_usages (ĐÚNG SCHEMA)
                                        $primaryImageSrc = 'https://via.placeholder.com/300x300?text=No+Image';
                                        
                                        if (!empty($product->primary_image) && isset($product->primary_image->file_path)) {
                                            $primaryImageSrc = htmlspecialchars($product->primary_image->file_path);
                                        } elseif (!empty($product->main_image)) {
                                            // Fallback: main_image column
                                            $primaryImageSrc = htmlspecialchars($product->main_image);
                                        }
                                        ?>
                                        <img src="<?= $primaryImageSrc ?>" 
                                             alt="Product" 
                                             class="img-fluid rounded" 
                                             style="max-height: 300px;"
                                             onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                                    </div>

                                    <?php if ($editMode): ?>
                                        <!-- Upload New Images -->
                                        <div class="border-2 border-dashed rounded p-3 text-center" 
                                             style="border-color: #ddd; cursor: pointer;" 
                                             onclick="document.getElementById('product_images').click()">
                                            <img src="https://cdn-icons-png.flaticon.com/512/1160/1160358.png" alt="Upload" width="32" height="32" class="mb-2">
                                            <p class="text-muted mb-0 small">Upload ảnh mới</p>
                                            <p class="text-muted mb-0 small">(Ảnh đầu tiên = Primary)</p>
                                        </div>
                                        <input type="file" 
                                               id="product_images" 
                                               name="product_images[]" 
                                               accept="image/*" 
                                               multiple
                                               style="display: none;">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Gallery Images -->
                            <?php if (!empty($product->images) && count($product->images) > 0): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3">
                                        <span class="badge bg-secondary me-2">Gallery</span>
                                        Tất Cả Ảnh (<?= count($product->images) ?>)
                                    </h6>
                                    
                                    <div class="row g-2">
                                        <?php foreach ($product->images as $image): ?>
                                            <div class="col-6">
                                                <div class="position-relative">
                                                    <img src="<?= htmlspecialchars($image->file_path) ?>" 
                                                         alt="Gallery" 
                                                         class="img-fluid rounded"
                                                         onerror="this.src='https://via.placeholder.com/150x150?text=Error'">
                                                    <?php if ($image->is_primary): ?>
                                                        <span class="badge bg-primary position-absolute top-0 end-0 m-1" style="font-size: 0.7rem;">
                                                            Primary
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Product Info -->
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-2">Thông Tin:</h6>
                                    <div class="small">
                                        <div class="mb-1"><strong>ID:</strong> #<?= $product->product_id ?></div>
                                        <div class="mb-1"><strong>SKU:</strong> <?= htmlspecialchars($product->sku ?? 'N/A') ?></div>
                                        <div class="mb-1"><strong>Slug:</strong> <?= htmlspecialchars($product->slug ?? 'N/A') ?></div>
                                        <div class="mb-1"><strong>Images:</strong> <?= count($product->images ?? []) ?> ảnh</div>
                                        <div class="mb-1"><strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($product->created_at)) ?></div>
                                        <?php if (isset($product->updated_at)): ?>
                                            <div><strong>Cập nhật:</strong> <?= date('d/m/Y H:i', strtotime($product->updated_at)) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <!-- Component Manager -->
    <script src="app/views/admin/components/component-manager.js"></script>
    
    <!-- Page Configuration -->
    <script>
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
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: '<?= htmlspecialchars($pageTitle ?? 'Chi Tiết Sản Phẩm') ?>',
                breadcrumb: '<?= htmlspecialchars($breadcrumb ?? 'Home > Sản Phẩm > Chi Tiết') ?>'
            }
        };

        const editMode = <?= $editMode ? 'true' : 'false' ?>;

        // Status toggle (chỉ cho edit mode)
        <?php if ($editMode): ?>
        const statusToggle = document.getElementById('is_active');
        const statusBadge = document.getElementById('statusBadge');
        
        if (statusToggle && statusBadge) {
            statusToggle.addEventListener('change', function() {
                if (this.checked) {
                    statusBadge.className = 'badge badge-delivered badge-custom';
                    statusBadge.textContent = 'Đang bán';
                } else {
                    statusBadge.className = 'badge badge-canceled badge-custom';
                    statusBadge.textContent = 'Ngừng bán';
                }
            });
        }
        <?php endif; ?>

        // Enable edit mode
        function enableEditMode(productId) {
            window.location.href = 'index.php?url=edit-product&id=' + productId;
        }

        // Delete product
        function deleteProduct(productId) {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này? Hành động này không thể hoàn tác!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php?url=products&action=delete&id=' + productId;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Form submit
        <?php if ($editMode): ?>
        document.getElementById('productDetailsForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            
            if (name.length < 3) {
                e.preventDefault();
                alert('Tên sản phẩm phải có ít nhất 3 ký tự');
                return false;
            }
            
            // Show loading
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang cập nhật...';
            }
            
            return true;
        });
        <?php endif; ?>
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="app/views/admin/assets/js/main.js"></script>
    
    <!-- Product Details JS -->
    <script src="app/views/admin/assets/js/product-details.js"></script>
</body>
</html>
