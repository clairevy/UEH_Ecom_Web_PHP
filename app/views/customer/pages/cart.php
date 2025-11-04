<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Giỏ hàng'; ?> - Jewelry Store</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .cart-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 20px 0;
            transition: all 0.3s ease;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item:hover {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px 15px;
        }
        
        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .quantity-btn {
            width: 35px;
            height: 35px;
            border: 2px solid #667eea;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .quantity-btn:hover {
            background: #667eea;
            color: white;
        }
        
        .quantity-input {
            width: 60px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 5px;
        }
        
        .price-text {
            font-size: 1.1rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            position: sticky;
            top: 100px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 8px;
        }
        
        .summary-row.total {
            border-top: 2px solid rgba(255,255,255,0.3);
            margin-top: 15px;
            padding-top: 15px;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .btn-primary {
            background: white;
            color: #667eea;
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #f8f9fa;
            color: #5a67d8;
            transform: translateY(-2px);
        }
        
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-cart i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        .remove-btn {
            color: #dc3545;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .remove-btn:hover {
            color: #c82333;
        }
    </style>
</head>
<body class="bg-light">
    
    <!-- Include Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>
    
    <div class="container mt-5 pt-4">
        <!-- Cart Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">
                <i class="fas fa-shopping-cart text-primary me-2"></i>
                Giỏ hàng của bạn
            </h2>
            <?php if (!empty($cartItems)): ?>
                <button id="clearCartBtn" class="btn btn-outline-danger">
                    <i class="fas fa-trash me-2"></i>
                    Xóa tất cả
                </button>
            <?php endif; ?>
        </div>
        
        <div id="alertContainer"></div>
        
        <?php if (empty($cartItems)): ?>
            <!-- Empty Cart -->
            <div class="cart-container">
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h4 class="text-muted mb-3">Giỏ hàng của bạn đang trống</h4>
                    <p class="text-muted mb-4">Hãy thêm một số sản phẩm tuyệt vời vào giỏ hàng của bạn</p>
                    <a href="/Ecom_website/products" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>
                        Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="cart-container">
                        <h5 class="fw-bold mb-4">Sản phẩm trong giỏ hàng (<?php echo count($cartItems); ?> sản phẩm)</h5>
                        
                        <div id="cartItemsContainer">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="cart-item" data-cart-key="<?php echo $item['cart_key']; ?>">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <img src="<?php echo isset($item['product']->primary_image) ? asset($item['product']->primary_image->file_path) : asset('images/placeholder.jpg'); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['product']->name); ?>" 
                                                 class="product-image">
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <h6 class="fw-semibold mb-1"><?php echo htmlspecialchars($item['product']->name); ?></h6>
                                            <p class="text-muted small mb-1">Mã SP: <?php echo $item['product']->product_id; ?></p>
                                            <?php if ($item['size']): ?>
                                                <small class="text-muted">Size: <?php echo htmlspecialchars($item['size']); ?></small>
                                            <?php endif; ?>
                                            <?php if ($item['color']): ?>
                                                <small class="text-muted ms-2">Màu: <?php echo htmlspecialchars($item['color']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="price-text">
                                                <?php echo number_format($item['product']->base_price, 0, ',', '.'); ?>đ
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="quantity-controls">
                                                <button class="quantity-btn decrease-qty" 
                                                        data-product-id="<?php echo $item['product']->product_id; ?>"
                                                        data-size="<?php echo $item['size']; ?>"
                                                        data-color="<?php echo $item['color']; ?>">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" 
                                                       class="quantity-input" 
                                                       value="<?php echo $item['quantity']; ?>"
                                                       min="1" 
                                                       max="<?php echo $item['product']->stock_quantity; ?>"
                                                       data-product-id="<?php echo $item['product']->product_id; ?>"
                                                       data-size="<?php echo $item['size']; ?>"
                                                       data-color="<?php echo $item['color']; ?>">
                                                <button class="quantity-btn increase-qty"
                                                        data-product-id="<?php echo $item['product']->product_id; ?>"
                                                        data-size="<?php echo $item['size']; ?>"
                                                        data-color="<?php echo $item['color']; ?>">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-1">
                                            <button class="remove-btn" 
                                                    data-product-id="<?php echo $item['product']->product_id; ?>"
                                                    data-size="<?php echo $item['size']; ?>"
                                                    data-color="<?php echo $item['color']; ?>"
                                                    title="Xóa sản phẩm">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="summary-card">
                        <h5 class="fw-bold mb-4">
                            <i class="fas fa-calculator me-2"></i>
                            Tóm tắt đơn hàng
                        </h5>
                        
                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <span id="cartSubtotal"><?php echo number_format($cartSummary['subtotal'], 0, ',', '.'); ?>đ</span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span id="cartShipping">
                                <?php if ($cartSummary['shipping'] == 0): ?>
                                    <span class="text-success">Miễn phí</span>
                                <?php else: ?>
                                    <?php echo number_format($cartSummary['shipping'], 0, ',', '.'); ?>đ
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Thuế VAT (10%):</span>
                            <span id="cartTax"><?php echo number_format($cartSummary['tax'], 0, ',', '.'); ?>đ</span>
                        </div>
                        
                        <div class="summary-row total">
                            <span>Tổng cộng:</span>
                            <span id="cartTotal"><?php echo number_format($cartSummary['total'], 0, ',', '.'); ?>đ</span>
                        </div>
                        
                        <hr style="border-color: rgba(255,255,255,0.3);">
                        
                        <a href="/Ecom_website/checkout" class="btn btn-primary mb-3">
                            <i class="fas fa-credit-card me-2"></i>
                            Tiến hành thanh toán
                        </a>
                        
                        <a href="/Ecom_website/products" class="btn btn-outline-light w-100">
                            <i class="fas fa-arrow-left me-2"></i>
                            Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Update quantity functions
        document.addEventListener('click', function(e) {
            if (e.target.closest('.decrease-qty')) {
                e.preventDefault();
                const btn = e.target.closest('.decrease-qty');
                const input = btn.parentElement.querySelector('.quantity-input');
                const currentQty = parseInt(input.value);
                if (currentQty > 1) {
                    updateCartQuantity(btn.dataset.productId, currentQty - 1, btn.dataset.size, btn.dataset.color);
                }
            }
            
            if (e.target.closest('.increase-qty')) {
                e.preventDefault();
                const btn = e.target.closest('.increase-qty');
                const input = btn.parentElement.querySelector('.quantity-input');
                const currentQty = parseInt(input.value);
                const maxQty = parseInt(input.max);
                if (currentQty < maxQty) {
                    updateCartQuantity(btn.dataset.productId, currentQty + 1, btn.dataset.size, btn.dataset.color);
                }
            }
            
            if (e.target.closest('.remove-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.remove-btn');
                removeFromCart(btn.dataset.productId, btn.dataset.size, btn.dataset.color);
            }
        });
        
        // Quantity input change
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('quantity-input')) {
                const input = e.target;
                const newQty = parseInt(input.value);
                const maxQty = parseInt(input.max);
                
                if (newQty > 0 && newQty <= maxQty) {
                    updateCartQuantity(input.dataset.productId, newQty, input.dataset.size, input.dataset.color);
                } else {
                    showAlert(false, 'Số lượng không hợp lệ');
                    location.reload();
                }
            }
        });
        
        // Clear cart
        document.getElementById('clearCartBtn')?.addEventListener('click', function() {
            if (confirm('Bạn có chắc muốn xóa tất cả sản phẩm trong giỏ hàng?')) {
                clearCart();
            }
        });
        
        // Update cart quantity
        async function updateCartQuantity(productId, quantity, size = '', color = '') {
            try {
                // Show loading state
                const cartKey = `${productId}_${size}_${color}`;
                const cartItem = document.querySelector(`[data-cart-key="${cartKey}"]`);
                if (cartItem) {
                    cartItem.style.opacity = '0.6';
                }
                
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('quantity', quantity);
                formData.append('size', size);
                formData.append('color', color);
                
                const response = await fetch('/Ecom_website/cart/update', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Update quantity input
                    const input = cartItem.querySelector('.quantity-input');
                    input.value = quantity;
                    
                    // Update cart summary via AJAX
                    await updateCartSummary();
                    
                    
                } else {
                    showAlert(false, result.message);
                    // Revert input value
                    location.reload();
                }
                
                // Remove loading state
                if (cartItem) {
                    cartItem.style.opacity = '1';
                }
                
            } catch (error) {
                console.error('Update cart error:', error);
                showAlert(false, 'Có lỗi xảy ra khi cập nhật giỏ hàng!');
                location.reload();
            }
        }
        
        // Remove from cart
        async function removeFromCart(productId, size = '', color = '') {
            try {
                const cartKey = `${productId}_${size}_${color}`;
                const cartItem = document.querySelector(`[data-cart-key="${cartKey}"]`);
                
                // Confirm removal
                if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                    return;
                }
                
                // Show loading state
                if (cartItem) {
                    cartItem.style.opacity = '0.6';
                }
                
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('size', size);
                formData.append('color', color);
                
                const response = await fetch('/Ecom_website/cart/remove', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Remove item from DOM with animation
                    if (cartItem) {
                        cartItem.style.transition = 'all 0.5s ease';
                        cartItem.style.opacity = '0';
                        cartItem.style.transform = 'translateX(-100%)';
                        
                        setTimeout(() => {
                            cartItem.remove();
                            updateCartSummary();
                            
                            // Check if cart is empty
                            const remainingItems = document.querySelectorAll('.cart-item');
                            if (remainingItems.length === 0) {
                                location.reload(); // Reload to show empty cart state
                            }
                        }, 500);
                    }
                    
                    showAlert(true, result.message);
                } else {
                    showAlert(false, result.message);
                    if (cartItem) {
                        cartItem.style.opacity = '1';
                    }
                }
                
            } catch (error) {
                console.error('Remove cart error:', error);
                showAlert(false, 'Có lỗi xảy ra khi xóa sản phẩm!');
            }
        }
        
        // Clear cart
        async function clearCart() {
            try {
                const response = await fetch('/Ecom_website/cart/clear', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert(true, result.message);
                    setTimeout(() => location.reload(), 1000); // Reload to show empty cart state
                } else {
                    showAlert(false, result.message);
                }
                
            } catch (error) {
                console.error('Clear cart error:', error);
                showAlert(false, 'Có lỗi xảy ra khi xóa giỏ hàng!');
            }
        }

        // Update cart summary
        async function updateCartSummary() {
            try {
                const response = await fetch('/Ecom_website/cart/summary', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.data.summary) {
                    const summary = result.data.summary;
                    
                    // Update cart badge if exists
                    const cartBadge = document.querySelector('.cart-count');
                    if (cartBadge) {
                        cartBadge.textContent = result.data.totalItems;
                        cartBadge.style.display = result.data.totalItems > 0 ? 'inline' : 'none';
                    }
                    
                    // Update cart summary prices using correct IDs
                    const subtotalElement = document.getElementById('cartSubtotal');
                    const shippingElement = document.getElementById('cartShipping');
                    const taxElement = document.getElementById('cartTax');
                    const totalElement = document.getElementById('cartTotal');
                    
                    if (subtotalElement) {
                        subtotalElement.textContent = new Intl.NumberFormat('vi-VN').format(summary.subtotal) + 'đ';
                    }
                    
                    if (shippingElement) {
                        if (summary.shipping === 0) {
                            shippingElement.innerHTML = '<span class="text-success">Miễn phí</span>';
                        } else {
                            shippingElement.textContent = new Intl.NumberFormat('vi-VN').format(summary.shipping) + 'đ';
                        }
                    }
                    
                    if (taxElement) {
                        taxElement.textContent = new Intl.NumberFormat('vi-VN').format(summary.tax) + 'đ';
                    }
                    
                    if (totalElement) {
                        totalElement.textContent = new Intl.NumberFormat('vi-VN').format(summary.total) + 'đ';
                    }
                    
                    console.log('Cart summary updated successfully');
                }
                
            } catch (error) {
                console.error('Update cart summary error:', error);
            }
        }
        
        // Show alert function
        function showAlert(success, message) {
            const container = document.getElementById('alertContainer');
            container.innerHTML = `
                <div class="alert alert-${success ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${success ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    </script>
</body>
</html>