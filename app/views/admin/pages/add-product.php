<?php
/**
 * Add Product View - Pure MVC View
 * Nhận data từ Controller, không có hardcode
 * 
 * Biến được truyền từ Controller:
 * - $categories: Danh sách categories từ database
 * - $collections: Danh sách collections từ database
 * - $materials: Danh sách materials có sẵn
 * - $title: Tiêu đề trang
 * - $pageTitle: Tiêu đề cho header
 * - $breadcrumb: Breadcrumb text
 */

// Lấy old input nếu có lỗi validation
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Thêm Sản Phẩm') ?> - KICKS Admin</title>
    
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

                <!-- Product Form -->
                <form id="addProductForm" method="POST" action="index.php?url=products&action=create" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Product Form Fields -->
                        <div class="col-lg-8 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">Thông Tin Sản Phẩm</h5>

                                    <!-- Product Name -->
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label">Tên Sản Phẩm <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="name" 
                                               name="name" 
                                               placeholder="Nhập tên sản phẩm" 
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
                                                  placeholder="Nhập mô tả sản phẩm"><?= htmlspecialchars($oldInput['description'] ?? '') ?></textarea>
                                    </div>

                                    <div class="row">
                                        <!-- Category -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="category_ids" class="form-label">Danh Mục <span class="text-danger">*</span></label>
                                                <select class="form-control" id="category_ids" name="category_ids[]" multiple size="5">
                                                    <?php if (!empty($categories)): ?>
                                                        <?php foreach ($categories as $category): ?>
                                                            <option value="<?= $category->category_id ?>">
                                                                <?= htmlspecialchars($category->name) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option value="">Chưa có danh mục nào</option>
                                                    <?php endif; ?>
                                                </select>
                                                <small class="text-muted">Giữ Ctrl (hoặc Cmd) để chọn nhiều danh mục</small>
                                            </div>
                                        </div>

                                        <!-- Collection -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="collection_id" class="form-label">Bộ Sưu Tập</label>
                                                <select class="form-control" id="collection_id" name="collection_id">
                                                    <option value="">-- Chọn bộ sưu tập --</option>
                                                    <?php if (!empty($collections)): ?>
                                                        <?php foreach ($collections as $collection): ?>
                                                            <option value="<?= $collection->collection_id ?>"
                                                                <?= (isset($oldInput['collection_id']) && $oldInput['collection_id'] == $collection->collection_id) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($collection->collection_name) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Material -->
                                    <div class="form-group mb-3">
                                        <label for="material" class="form-label">Chất Liệu</label>
                                        <select class="form-control" id="material" name="material">
                                            <?php 
                                            $materialOptions = is_array($materials) ? $materials : ['gold', 'silver', 'diamond', 'pearl'];
                                            foreach ($materialOptions as $material): 
                                            ?>
                                                <option value="<?= htmlspecialchars($material) ?>"
                                                    <?= (isset($oldInput['material']) && $oldInput['material'] == $material) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars(ucfirst($material)) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                                                        <!-- Product Variants Section -->
                                    <div class="card mt-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">Biến Thể Sản Phẩm</h5>
                                            <small class="text-muted">Thêm các size và màu sắc cho sản phẩm</small>
                                        </div>
                                        <div class="card-body">
                                            <div id="variantsContainer">
                                                <!-- Default variant -->
                                                <div class="variant-item border rounded p-3 mb-3" data-index="0">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0">Biến thể #1</h6>
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-variant" onclick="removeVariant(0)" style="display: none;">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Size <span class="text-danger">*</span></label>
                                                                <select class="form-control variant-size" name="variants[0][size]" required>
                                                                    <option value="">Chọn size</option>
                                                                    <option value="XS">XS</option>
                                                                    <option value="S">S</option>
                                                                    <option value="M">M</option>
                                                                    <option value="L">L</option>
                                                                    <option value="XL">XL</option>
                                                                    <option value="XXL">XXL</option>
                                                                    <option value="35">35</option>
                                                                    <option value="36">36</option>
                                                                    <option value="37">37</option>
                                                                    <option value="38">38</option>
                                                                    <option value="39">39</option>
                                                                    <option value="40">40</option>
                                                                    <option value="41">41</option>
                                                                    <option value="42">42</option>
                                                                    <option value="43">43</option>
                                                                    <option value="44">44</option>
                                                                    <option value="45">45</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Màu sắc <span class="text-danger">*</span></label>
                                                                <select class="form-control variant-color" name="variants[0][color]" required>
                                                                    <option value="">Chọn màu</option>
                                                                    <option value="Đỏ">Đỏ</option>
                                                                    <option value="Xanh">Xanh</option>
                                                                    <option value="Vàng">Vàng</option>
                                                                    <option value="Đen">Đen</option>
                                                                    <option value="Trắng">Trắng</option>
                                                                    <option value="Hồng">Hồng</option>
                                                                    <option value="Tím">Tím</option>
                                                                    <option value="Cam">Cam</option>
                                                                    <option value="Xám">Xám</option>
                                                                    <option value="Nâu">Nâu</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Giá <span class="text-danger">*</span></label>
                                                                <input type="number" 
                                                                       class="form-control variant-price" 
                                                                       name="variants[0][price]" 
                                                                       placeholder="0" 
                                                                       min="0" 
                                                                       step="1000"
                                                                       required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                                                <input type="number" 
                                                                       class="form-control variant-stock" 
                                                                       name="variants[0][stock]" 
                                                                       placeholder="0" 
                                                                       min="0"
                                                                       required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addVariant()">
                                                <i class="fas fa-plus"></i> Thêm biến thể
                                            </button>
                                        </div>
                                    </div>

                                    <!-- SKU and Price -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="sku" 
                                                       name="sku" 
                                                       placeholder="VD: PROD-001" 
                                                       value="<?= htmlspecialchars($oldInput['sku'] ?? '') ?>"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="base_price" class="form-label">Giá Bán (VND) <span class="text-danger">*</span></label>
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="base_price" 
                                                       name="base_price" 
                                                       placeholder="0" 
                                                       min="0" 
                                                       step="1000"
                                                       value="<?= htmlspecialchars($oldInput['base_price'] ?? '') ?>"
                                                       required>
                                            </div>
                                        </div>
                                    </div>

                            

                                    <!-- Is Active -->
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               <?= (isset($oldInput['is_active']) && $oldInput['is_active']) || !isset($oldInput['is_active']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">
                                            Kích hoạt sản phẩm (Hiển thị trên website)
                                        </label>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2 mt-4">
                                        <button type="submit" class="btn btn-primary-custom btn-custom px-4">
                                            <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" alt="Save" width="16" height="16" class="me-1">
                                            LƯU SẢN PHẨM
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-custom px-4" onclick="window.location.href='index.php?url=products'">
                                            <img src="https://cdn-icons-png.flaticon.com/512/189/189665.png" alt="Cancel" width="16" height="16" class="me-1">
                                            HỦY BỎ
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Gallery -->
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Hình Ảnh Sản Phẩm</h5>
                                    
                                    <!-- Product Gallery Upload -->
                                    <div class="mb-4">
                                        <div class="border-2 border-dashed rounded-custom p-4 text-center upload-area" 
                                             style="border-color: var(--border-color); cursor: pointer; transition: all 0.3s ease;" 
                                             onclick="document.getElementById('product_images').click()" 
                                             ondragover="handleDragOver(event)" 
                                             ondragleave="handleDragLeave(event)"
                                             ondrop="handleDrop(event)">
                                            <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="Upload" width="48" height="48" class="mb-2 opacity-50">
                                            <p class="text-muted mb-2">Kéo thả ảnh vào đây, hoặc click để chọn</p>
                                            <p class="text-muted small">Hỗ trợ: JPG, PNG, GIF, WEBP (Max 5MB/file)</p>
                                        </div>
                                        <input type="file" 
                                               id="product_images" 
                                               name="product_images[]" 
                                               multiple 
                                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" 
                                               style="display: none;" 
                                               onchange="handleFileSelect(this)">
                                    </div>

                                    <!-- Preview Images Container -->
                                    <div id="imagePreviewContainer" class="uploaded-images" style="display: none;">
                                        <h6 class="fw-bold mb-3">Ảnh Đã Chọn</h6>
                                        <div id="imageList"></div>
                                    </div>

                                    <!-- Tips -->
                                    <div class="mt-3 p-3 bg-light rounded-custom">
                                        <h6 class="fw-bold mb-2">Lưu Ý:</h6>
                                        <ul class="small text-muted mb-0">
                                            <li>Tải lên ít nhất một ảnh sản phẩm</li>
                                            <li>Kích thước đề xuất: 800x800px</li>
                                            <li>Sử dụng ảnh chất lượng cao</li>
                                            <li>Ảnh đầu tiên sẽ là ảnh chính</li>
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
                title: '<?= htmlspecialchars($pageTitle ?? 'Thêm Sản Phẩm') ?>',
                breadcrumb: '<?= htmlspecialchars($breadcrumb ?? 'Home > Sản Phẩm > Thêm Mới') ?>'
            }
        };
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="app/views/admin/assets/js/main.js"></script>
    <script>
        let variantIndex = 1;

        // Add new variant
        function addVariant() {
            const container = document.getElementById('variantsContainer');
            const newVariant = document.createElement('div');
            newVariant.className = 'variant-item border rounded p-3 mb-3';
            newVariant.setAttribute('data-index', variantIndex);
            
            newVariant.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Biến thể #${variantIndex + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant" onclick="removeVariant(${variantIndex})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label">Size <span class="text-danger">*</span></label>
                            <select class="form-control variant-size" name="variants[${variantIndex}][size]" required>
                                <option value="">Chọn size</option>
                                <option value="XS">XS</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                                <option value="35">35</option>
                                <option value="36">36</option>
                                <option value="37">37</option>
                                <option value="38">38</option>
                                <option value="39">39</option>
                                <option value="40">40</option>
                                <option value="41">41</option>
                                <option value="42">42</option>
                                <option value="43">43</option>
                                <option value="44">44</option>
                                <option value="45">45</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label">Màu sắc <span class="text-danger">*</span></label>
                            <select class="form-control variant-color" name="variants[${variantIndex}][color]" required>
                                <option value="">Chọn màu</option>
                                <option value="Đỏ">Đỏ</option>
                                <option value="Xanh">Xanh</option>
                                <option value="Vàng">Vàng</option>
                                <option value="Đen">Đen</option>
                                <option value="Trắng">Trắng</option>
                                <option value="Hồng">Hồng</option>
                                <option value="Tím">Tím</option>
                                <option value="Cam">Cam</option>
                                <option value="Xám">Xám</option>
                                <option value="Nâu">Nâu</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label">Giá <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control variant-price" 
                                   name="variants[${variantIndex}][price]" 
                                   placeholder="0" 
                                   min="0" 
                                   step="1000"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control variant-stock" 
                                   name="variants[${variantIndex}][stock]" 
                                   placeholder="0" 
                                   min="0"
                                   required>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(newVariant);
            variantIndex++;
            
            // Show remove button for first variant if there are multiple variants
            updateRemoveButtons();
        }

        // Remove variant
        function removeVariant(index) {
            const variantItem = document.querySelector(`[data-index="${index}"]`);
            if (variantItem) {
                variantItem.remove();
                updateRemoveButtons();
            }
        }

        // Update remove buttons visibility
        function updateRemoveButtons() {
            const variants = document.querySelectorAll('.variant-item');
            const removeButtons = document.querySelectorAll('.remove-variant');
            
            removeButtons.forEach((button, index) => {
                // Show remove button if there's more than 1 variant
                button.style.display = variants.length > 1 ? 'inline-block' : 'none';
            });
        }

        // Auto-fill price from base price
        document.addEventListener('DOMContentLoaded', function() {
            const basePriceInput = document.getElementById('base_price');
            if (basePriceInput) {
                basePriceInput.addEventListener('input', function() {
                    const basePrice = this.value;
                    if (basePrice) {
                        // Auto-fill first variant price if empty
                        const firstVariantPrice = document.querySelector('input[name="variants[0][price]"]');
                        if (firstVariantPrice && !firstVariantPrice.value) {
                            firstVariantPrice.value = basePrice;
                        }
                    }
                });
            }
        });
    </script>
    <!-- Add Product JS -->
    <script src="/Ecom_website/app/views/admin/assets/js/add-product.js"></script>

</body>
</html>