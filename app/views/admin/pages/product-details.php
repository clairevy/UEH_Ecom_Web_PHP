<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KICKS Admin - Product Details</title>
    
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
                <div class="row">
                    <!-- Product Form -->
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <!-- Product Name -->
                                <div class="form-group">
                                    <label for="productName" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="productName" value="Adidas Ultra boost">
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" rows="4">Long distance running requires a lot from athletes.</textarea>
                                </div>

                                <!-- Category and Collection -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category" class="form-label">Danh Mục <span class="text-danger">*</span></label>
                                            <select class="form-select" id="category" required>
                                                <option value="">-- Chọn danh mục --</option>
                                                <option value="nhan" selected>Nhẫn</option>
                                                <option value="vong-co">Vòng cổ</option>
                                                <option value="bong-tai">Bông tai</option>
                                                <option value="vong-tay">Vòng tay</option>
                                                <option value="lac-chan">Lắc chân</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="collection" class="form-label">Collection <span class="text-muted">(Optional)</span></label>
                                            <select class="form-select" id="collection">
                                                <option value="">-- Không chọn collection --</option>
                                                <option value="summer-2024">Summer Collection 2024</option>
                                                <option value="wedding">Wedding Collection</option>
                                                <option value="luxury">Luxury Collection</option>
                                                <option value="bestseller">Bestseller Collection</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Brand Name -->
                                <div class="form-group">
                                    <label for="brandName" class="form-label">Thương Hiệu</label>
                                    <input type="text" class="form-control" id="brandName" value="Adidas">
                                </div>

                                <!-- SKU and Stock -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sku" class="form-label">SKU</label>
                                            <input type="text" class="form-control" id="sku" value="#33A53">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="stock" class="form-label">Stock Quantity</label>
                                            <input type="number" class="form-control" id="stock" value="21">
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="regularPrice" class="form-label">Regular Price</label>
                                            <input type="text" class="form-control" id="regularPrice" value="$110.40">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="salePrice" class="form-label">Sale Price</label>
                                            <input type="text" class="form-control" id="salePrice" value="$450">
                                        </div>
                                    </div>
                                </div>

                              

                                <!-- Product Status -->
                                <div class="form-group">
                                    <label for="productStatus" class="form-label">Trạng Thái Sản Phẩm</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <span id="statusBadge" class="badge badge-delivered badge-custom">Đang bán</span>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="productStatusToggle" checked style="cursor: pointer;">
                                            <label class="form-check-label" for="productStatusToggle" style="cursor: pointer;">
                                                Kích hoạt sản phẩm
                                            </label>
                                        </div>
                                    </div>
                                    <small class="text-muted">Tắt để ngừng bán sản phẩm này trên website</small>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex gap-2 mt-4">
                                    <button class="btn btn-primary-custom btn-custom px-4" onclick="updateProduct()">
                                        <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" alt="Update" width="16" height="16" class="me-1">
                                        CẬP NHẬT
                                    </button>
                                    <button class="btn btn-danger-custom btn-custom px-4" onclick="deleteProduct()">
                                        <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="16" height="16" class="me-1">
                                        XÓA
                                    </button>
                                    <button class="btn btn-outline-secondary btn-custom px-4" onclick="cancelEdit()">
                                        <img src="https://cdn-icons-png.flaticon.com/512/189/189665.png" alt="Cancel" width="16" height="16" class="me-1">
                                        HỦY
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Images -->
                    <div class="col-lg-4 mb-4">
                        <!-- Primary Image (Thumbnail) -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">
                                    <span class="badge bg-primary me-2">Primary</span>
                                    Ảnh Đại Diện (Thumbnail)
                                </h6>
                                
                                <!-- Current Primary Image -->
                                <div class="text-center mb-3">
                                    <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=200&h=200&fit=crop" 
                                         alt="Primary" id="primaryImagePreview" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                                
                                <!-- Upload Primary -->
                                <div class="border border-primary border-2 border-dashed rounded p-3 text-center" style="cursor: pointer;" onclick="document.getElementById('primaryImageInput').click()">
                                    <input type="file" id="primaryImageInput" accept="image/*" style="display: none;">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1160/1160358.png" alt="Upload" width="32" height="32" class="mb-2">
                                    <p class="text-muted mb-1 small">Upload ảnh chính</p>
                                    <p class="text-muted small mb-0">JPG, PNG (Max 5MB)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Gallery Images -->
                        <div class="card">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">
                                    <span class="badge bg-secondary me-2">Gallery</span>
                                    Ảnh Khác (Gallery)
                                </h6>
                                
                                <!-- Upload Gallery -->
                                <div class="border border-2 border-dashed rounded p-3 text-center mb-3" style="cursor: pointer;" onclick="document.getElementById('galleryImagesInput').click()">
                                    <input type="file" id="galleryImagesInput" accept="image/*" multiple style="display: none;">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="Upload" width="32" height="32" class="mb-2 opacity-50">
                                    <p class="text-muted mb-1 small">Upload nhiều ảnh</p>
                                    <p class="text-muted small mb-0">Chọn nhiều file cùng lúc</p>
                                </div>

                                <!-- Gallery Preview -->
                                <div id="galleryPreview">
                                    <!-- Sample Gallery Images -->
                                    <div class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded">
                                        <div class="d-flex align-items-center">
                                            <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=50&h=50&fit=crop" 
                                                 alt="Gallery" width="40" height="40" class="me-2 rounded">
                                            <span class="small">nhan-detail-1.jpg</span>
                                        </div>
                                        <button class="btn btn-sm btn-danger" onclick="removeGalleryImage(this)">
                                            <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="14" height="14">
                                        </button>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded">
                                        <div class="d-flex align-items-center">
                                            <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=50&h=50&fit=crop" 
                                                 alt="Gallery" width="40" height="40" class="me-2 rounded">
                                            <span class="small">nhan-detail-2.jpg</span>
                                        </div>
                                        <button class="btn btn-sm btn-danger" onclick="removeGalleryImage(this)">
                                            <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="14" height="14">
                                        </button>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded">
                                        <div class="d-flex align-items-center">
                                            <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=50&h=50&fit=crop" 
                                                 alt="Gallery" width="40" height="40" class="me-2 rounded">
                                            <span class="small">nhan-detail-3.jpg</span>
                                        </div>
                                        <button class="btn btn-sm btn-danger" onclick="removeGalleryImage(this)">
                                            <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="14" height="14">
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="text-muted small mt-2">
                                    <strong>Lưu ý:</strong> Ảnh gallery sẽ hiển thị khi khách hàng click vào sản phẩm
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                activePage: 'products',
                links: {
                    dashboard: '../index.html',
                    products: 'products.html',
                    orders: 'orders.html'
                },
                categories: [
                    { name: 'Vòng cổ', count: 12 },
                    { name: 'Vòng tay', count: 10 },
                    { name: 'Bông tai', count: 13 },
                    { name: 'Nhẫn', count: 4 },
                    { name: 'Lắc chân', count: 6 }
                ],
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: 'Product Details',
                breadcrumb: 'Home > All Products > Product Details'
            }
        };
    </script>
    
    <!-- Product Details Page Script -->
    <script src="../assets/js/product-details.js"></script>
    
    <!-- Product Status Toggle Script -->
    <script>
        // Product status toggle functionality
        const statusToggle = document.getElementById('productStatusToggle');
        const statusBadge = document.getElementById('statusBadge');
        
        statusToggle.addEventListener('change', function() {
            if (this.checked) {
                statusBadge.className = 'badge badge-delivered badge-custom';
                statusBadge.textContent = 'Đang bán';
            } else {
                statusBadge.className = 'badge badge-canceled badge-custom';
                statusBadge.textContent = 'Ngừng bán';
            }
        });

        // Update product function
        function updateProduct() {
            const isActive = statusToggle.checked;
            const productData = {
                name: document.getElementById('productName').value,
                description: document.getElementById('description').value,
                category: document.getElementById('category').value,
                collection: document.getElementById('collection').value,
                brandName: document.getElementById('brandName').value,
                sku: document.getElementById('sku').value,
                stock: document.getElementById('stock').value,
                regularPrice: document.getElementById('regularPrice').value,
                salePrice: document.getElementById('salePrice').value,
                isActive: isActive
            };
            
            console.log('Updating product:', productData);
            
            // Here you would send the data to backend
            // For now, show confirmation
            alert(`Sản phẩm đã được cập nhật!\nTrạng thái: ${isActive ? 'Đang bán' : 'Ngừng bán'}`);
        }

        // Delete product function
        function deleteProduct() {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này? Hành động này không thể hoàn tác!')) {
                console.log('Deleting product...');
                // Here you would send delete request to backend
                alert('Sản phẩm đã được xóa!');
                window.location.href = 'products.html';
            }
        }

        // Cancel edit function
        function cancelEdit() {
            if (confirm('Bạn có muốn hủy các thay đổi? Dữ liệu chưa lưu sẽ bị mất.')) {
                window.location.href = 'products.html';
            }
        }

        // Remove gallery image function
        function removeGalleryImage(button) {
            if (confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
                button.closest('.d-flex').remove();
            }
        }
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>