<?php
/**
 * Add Category View - Pure MVC View
 * Nhận data từ Controller, không có hardcode
 * 
 * Biến được truyền từ Controller:
 * - $title: Tiêu đề trang
 * - $pageTitle: Tiêu đề cho header
 * - $breadcrumb: Breadcrumb text
 * - $parentCategories: Danh sách parent categories (optional)
 * - $oldInput: Dữ liệu cũ nếu có lỗi validation
 */

// Lấy old input nếu có lỗi validation
$oldInput = $oldInput ?? [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Thêm Danh Mục') ?> - KICKS Admin</title>
    
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
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Category Form -->
                <form id="addCategoryForm" method="POST" action="index.php?url=categories&action=create" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Category Form Fields -->
                        <div class="col-lg-8 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">Thông Tin Danh Mục</h5>

                                    <!-- Category Name -->
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label">Tên Danh Mục <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="name" 
                                               name="name" 
                                               placeholder="VD: Nhẫn, Vòng cổ, Bông tai..." 
                                               value="<?= htmlspecialchars($oldInput['name'] ?? '') ?>"
                                               required>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group mb-3">
                                        <label for="description" class="form-label">Mô Tả</label>
                                        <textarea class="form-control" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="4" 
                                                  placeholder="Mô tả chi tiết về danh mục..."><?= htmlspecialchars($oldInput['description'] ?? '') ?></textarea>
                                    </div>

                                    <!-- Icon URL -->
                                    <div class="form-group mb-3">
                                        <label for="icon_url" class="form-label">Icon URL</label>
                                        <input type="url" 
                                               class="form-control" 
                                               id="icon_url" 
                                               name="icon_url" 
                                               placeholder="https://example.com/icon.png"
                                               value="<?= htmlspecialchars($oldInput['icon_url'] ?? '') ?>">
                                        <small class="text-muted">Link đến icon hiển thị cho danh mục (optional)</small>
                                    </div>

                                    <!-- Parent Category (Optional - for nested categories) -->
                                    <?php if (!empty($parentCategories)): ?>
                                    <div class="form-group mb-3">
                                        <label for="parent_id" class="form-label">Danh Mục Cha</label>
                                        <select class="form-control" id="parent_id" name="parent_id">
                                            <option value="">-- Không có (Danh mục gốc) --</option>
                                            <?php foreach ($parentCategories as $parent): ?>
                                                <option value="<?= $parent->category_id ?>"
                                                    <?= (isset($oldInput['parent_id']) && $oldInput['parent_id'] == $parent->category_id) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($parent->name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Chọn danh mục cha nếu đây là danh mục con</small>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Is Active -->
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               <?= (isset($oldInput['is_active']) && $oldInput['is_active']) || !isset($oldInput['is_active']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">
                                            Kích hoạt danh mục (Hiển thị trên website)
                                        </label>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2 mt-4">
                                        <button type="submit" class="btn btn-primary-custom btn-custom px-4">
                                            <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" alt="Save" width="16" height="16" class="me-1">
                                            LƯU DANH MỤC
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-custom px-4" onclick="window.location.href='index.php?url=categories'">
                                            <img src="https://cdn-icons-png.flaticon.com/512/189/189665.png" alt="Cancel" width="16" height="16" class="me-1">
                                            HỦY BỎ
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Category Image Upload -->
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Hình Ảnh Danh Mục</h5>
                                    
                                    <!-- Image Upload Area -->
                                    <div class="mb-4">
                                        <div class="border-2 border-dashed rounded-custom p-4 text-center upload-area" 
                                             style="border-color: var(--border-color); cursor: pointer; transition: all 0.3s ease;" 
                                             onclick="document.getElementById('category_image').click()" 
                                             ondragover="handleDragOver(event)" 
                                             ondragleave="handleDragLeave(event)"
                                             ondrop="handleDrop(event)">
                                            <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="Upload" width="48" height="48" class="mb-2 opacity-50">
                                            <p class="text-muted mb-2">Kéo thả ảnh vào đây, hoặc click để chọn</p>
                                            <p class="text-muted small">Hỗ trợ: JPG, PNG, GIF, WEBP (Max 2MB)</p>
                                        </div>
                                        <input type="file" 
                                               id="category_image" 
                                               name="image" 
                                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" 
                                               style="display: none;" 
                                               onchange="handleFileSelect(this)">
                                    </div>

                                    <!-- Preview Image Container -->
                                    <div id="imagePreviewContainer" class="uploaded-image" style="display: none;">
                                        <h6 class="fw-bold mb-3">Ảnh Đã Chọn</h6>
                                        <div id="imagePreview" class="border rounded p-2 text-center">
                                            <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 200px;">
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-2 w-100" onclick="clearImage()">
                                            <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Remove" width="12" height="12">
                                            Xóa ảnh
                                        </button>
                                    </div>

                                    <!-- Tips -->
                                    <div class="mt-3 p-3 bg-light rounded-custom">
                                        <h6 class="fw-bold mb-2">Lưu Ý:</h6>
                                        <ul class="small text-muted mb-0">
                                            <li>Tải lên ảnh đại diện cho danh mục</li>
                                            <li>Kích thước đề xuất: 800x600px</li>
                                            <li>Kích thước tối đa: 2MB</li>
                                            <li>Sử dụng ảnh chất lượng cao</li>
                                        </ul>
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
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: '<?= htmlspecialchars($pageTitle ?? 'Thêm Danh Mục') ?>',
                breadcrumb: '<?= htmlspecialchars($breadcrumb ?? 'Home > Danh Mục > Thêm Mới') ?>'
            }
        };
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="app/views/admin/assets/js/main.js"></script>
    
    <!-- Add Category JS -->
    <script src="app/views/admin/assets/js/add-category.js"></script>
    
    <!-- Inline Script for Image Upload -->
    <script>
        /**
         * Handle file selection
         */
        function handleFileSelect(input) {
            const file = input.files[0];
            const previewContainer = document.getElementById('imagePreviewContainer');
            const previewImg = document.getElementById('previewImg');
            
            if (!file) {
                previewContainer.style.display = 'none';
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Chỉ chấp nhận file ảnh: JPG, PNG, GIF, WEBP');
                input.value = '';
                return;
            }

            // Validate file size (2MB)
            if (file.size > 2097152) {
                alert('Kích thước file không được vượt quá 2MB');
                input.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }

        /**
         * Clear selected image
         */
        function clearImage() {
            const fileInput = document.getElementById('category_image');
            const previewContainer = document.getElementById('imagePreviewContainer');
            
            fileInput.value = '';
            previewContainer.style.display = 'none';
        }

        /**
         * Handle drag over
         */
        function handleDragOver(event) {
            event.preventDefault();
            event.currentTarget.style.borderColor = 'var(--accent-color, #28a745)';
            event.currentTarget.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
        }

        /**
         * Handle drag leave
         */
        function handleDragLeave(event) {
            event.currentTarget.style.borderColor = 'var(--border-color, #ddd)';
            event.currentTarget.style.backgroundColor = 'transparent';
        }

        /**
         * Handle drop
         */
        function handleDrop(event) {
            event.preventDefault();
            event.currentTarget.style.borderColor = 'var(--border-color, #ddd)';
            event.currentTarget.style.backgroundColor = 'transparent';
            
            const files = event.dataTransfer.files;
            const fileInput = document.getElementById('category_image');
            
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(fileInput);
            }
        }

        /**
         * Form submission validation
         */
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('addCategoryForm');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    const name = document.getElementById('name').value.trim();
                    
                    if (name.length < 2) {
                        e.preventDefault();
                        alert('Tên danh mục phải có ít nhất 2 ký tự');
                        document.getElementById('name').focus();
                        return false;
                    }
                    
                    // Show loading
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';
                    }
                    
                    return true;
                });
            }
        });
    </script>
</body>
</html>
