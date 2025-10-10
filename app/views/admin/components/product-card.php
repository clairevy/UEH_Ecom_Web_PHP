<?php
/**
 * Product Card Component - Pure MVC Component
 * Hiển thị thông tin sản phẩm với data từ database
 */

// Biến được truyền:
// $product - object sản phẩm từ database
// $showActions - hiển thị actions (edit, delete) - default true
?>

<!-- Product Card Component -->
<div class="col-lg-4 col-md-6 mb-4">
    <div class="card h-100 shadow-custom">
        <div class="position-relative">
            <img src="<?= htmlspecialchars($product->main_image ?? 'https://images.unsplash.com/photo-1708221382764-299d9e3ad257?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') ?>" 
                 class="card-img-top p-4" alt="<?= htmlspecialchars($product->name ?? 'Product') ?>" 
                 style="height: 200px; object-fit: contain;" data-product-image>
            
            <?php if ($showActions ?? true): ?>
                <div class="position-absolute top-0 end-0 p-2">
                    <div class="dropdown">
                        <button class="btn btn-link btn-sm" type="button" data-bs-toggle="dropdown">
                            <img src="https://cdn-icons-png.flaticon.com/512/2311/2311524.png" alt="More" width="20" height="20">
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php?url=edit-product&id=<?= $product->id ?>" data-edit-link>
                                Chỉnh sửa
                            </a></li>
                            <li><a class="dropdown-item text-danger" href="#" 
                                   onclick="deleteProduct(<?= $product->id ?>)" data-delete-action>
                                Xóa
                            </a></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="card-body">
            <h6 class="card-title fw-bold" data-product-name>
                <?= htmlspecialchars($product->name ?? 'Tên sản phẩm') ?>
            </h6>
            <p class="card-text text-muted small" data-product-category>
                <?= htmlspecialchars($product->category_name ?? 'Chưa phân loại') ?>
            </p>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="h5 mb-0 text-success" data-product-price>
                    $<?= number_format($product->base_price ?? 0, 2) ?>
                </span>
            </div>
            
            <div class="border-top pt-3">
                <h6 class="fw-bold mb-2">Tóm tắt</h6>
                <p class="small text-muted mb-3" data-product-summary>
                    <?= htmlspecialchars(substr($product->description ?? 'Mô tả sản phẩm', 0, 100)) ?><?= strlen($product->description ?? '') > 100 ? '...' : '' ?>
                </p>
                
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex align-items-center mb-2">
                            <img src="https://cdn-icons-png.flaticon.com/512/3208/3208676.png" alt="Sales" width="16" height="16" class="me-2">
                            <span class="small">Bán hàng</span>
                            <span class="badge bg-warning text-dark ms-auto" data-sales-count>
                                <?= $product->total_sold ?? 0 ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center mb-2">
                            <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="Stock" width="16" height="16" class="me-2">
                            <span class="small">Tồn kho</span>
                            <span class="badge bg-secondary ms-auto" data-stock-count>
                                <?= $product->stock_quantity ?? 0 ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-2">
                    <span class="badge bg-<?= ($product->is_active ?? false) ? 'success' : 'secondary' ?>">
                        <?= ($product->is_active ?? false) ? 'Đang bán' : 'Ngừng bán' ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
