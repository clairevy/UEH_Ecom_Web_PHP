<?php
$title = $data['title'] ?? 'Thanh toán';
$cartItems = $data['cartItems'] ?? [];
$cartSummary = $data['cartSummary'] ?? [];
$userInfo = $data['userInfo'] ?? null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Jewelry Store</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <html xmlns:th="http://www.thymeleaf.org">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?= asset('css/css.css?v=' . time()) ?>" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">  
    
    
    <style>
        body {
            background-color: var(--cream);
            background-image: linear-gradient(rgb(255, 255, 255), rgba(255, 255, 255, 0.755)),
                url("https://images.unsplash.com/photo-1608042314453-ae338d80c427?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MjR8fGpld2Vscnl8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&q=60&w=600");
            background-size: cover;
            background-attachment: fixed;
        }
        
        .checkout-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem 0;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 2rem 0;
        }
        
        .step {
            display: flex;
            align-items: center;
            margin: 0 1rem;
            position: relative;
        }
        
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -2rem;
            top: 50%;
            transform: translateY(-50%);
            width: 2rem;
            height: 2px;
            background: var(--light-gold);
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--light-gray);
            color: var(--dark-brown);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 0.8rem;
            border: 2px solid var(--light-gold);
            transition: all 0.3s ease;
        }
        
        .step.active .step-number {
            background: var(--gold);
            color: white;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.4);
            transform: scale(1.1);
        }
        
        .step.completed .step-number {
            background: var(--dark-gold);
            color: white;
            border-color: var(--dark-gold);
        }
        
        .checkout-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(212, 175, 55, 0.2);
            margin-bottom: 1.5rem;
            backdrop-filter: blur(8px);
            transition: all 0.3s ease;
        }
        
        .checkout-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(212, 175, 55, 0.15);
        }
        
        .checkout-card .card-header {
            background: linear-gradient(45deg, var(--cream), white);
            border-bottom: 2px solid var(--light-gold);
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }
        
        .checkout-card .card-body {
            padding: 1.8rem;
        }
        
        .section-title {
            color: var(--dark-brown);
            font-weight: 600;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            font-size: 16px;
            letter-spacing: 0.5px;
        }
        
        .section-title i {
            width: 24px;
            margin-right: 0.8rem;
            color: var(--gold);
        }
        
        .payment-option {
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
            border: 2px solid rgba(212, 175, 55, 0.2) !important;
        }
        
        .payment-option:hover {
            border-color: var(--gold) !important;
            box-shadow: 0 8px 24px rgba(212, 175, 55, 0.15);
            transform: translateY(-2px);
        }
        
        .form-check-input:checked + .form-check-label .payment-option {
            border-color: var(--gold) !important;
            background: linear-gradient(45deg, var(--cream), white);
        }
        
        .form-check-input:checked {
            background-color: var(--gold);
            border-color: var(--gold);
        }
        
        .summary-card {
            position: sticky;
            top: 100px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.98));
            border: 1px solid var(--light-gold);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.9rem 0;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            color: var(--dark-brown);
            font-size: 0.95rem;
        }
        
        .summary-row:last-child {
            border-bottom: none;
        }
        
        .summary-row.total {
            font-weight: 700;
            font-size: 1.2rem;
            border-top: 2px solid var(--light-gold);
            margin-top: 1rem;
            padding-top: 1.2rem;
            color: var(--dark-brown);
        }
        
        .summary-row.total span:last-child {
            color: var(--gold);
        }
        
        .product-item {
            display: flex;
            align-items: center;
            padding: 1.2rem 0;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            transition: all 0.3s ease;
        }
        
        .product-item:hover {
            transform: translateX(5px);
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.05));
        }
        
        .product-item:last-child {
            border-bottom: none;
        }
        
        .product-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 1.2rem;
            border: 2px solid var(--light-gold);
            transition: all 0.3s ease;
        }
        
        .product-item:hover .product-image {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2);
        }
        
        .btn-checkout {
            background: var(--gold);
            border: none;
            padding: 1.2rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-checkout::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        
        .btn-checkout:hover {
            background: var(--dark-gold);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
        }
        
        .btn-checkout:hover::before {
            left: 100%;
        }
        
        .form-control, .form-select {
            border: 2px solid rgba(212, 175, 55, 0.2);
            border-radius: 8px;
            padding: 0.85rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            color: var(--dark-brown);
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
            background: white;
        }
        
        .form-control::placeholder {
            color: #999;
            font-style: italic;
        }
        
        .form-label {
            color: var(--dark-brown);
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.4rem;
        }
        
        @media (max-width: 768px) {
            .step-indicator {
                flex-direction: column;
                gap: 1rem;
            }
            
            .step {
                margin: 0;
            }
            
            .summary-card {
                position: static;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>
    
    <div class="checkout-header">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3">
                    <li class="breadcrumb-item"><a href="<?= url('/') ?>" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="<?= url('/cart') ?>" class="text-decoration-none">Giỏ hàng</a></li>
                    <li class="breadcrumb-item active">Thanh toán</li>
                </ol>
            </nav>
            
            <h2 class="mb-0">
                <i class="fas fa-credit-card me-2 text-primary"></i>
                Thanh toán đơn hàng
            </h2>
            
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step completed">
                    <div class="step-number">1</div>
                    <span>Giỏ hàng</span>
                </div>
                <div class="step active">
                    <div class="step-number">2</div>
                    <span>Thanh toán</span>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <span>Hoàn thành</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <form id="checkoutForm" class="needs-validation" novalidate>
                    <!-- Customer Information -->
                    <div class="checkout-card">
                        <div class="card-header">
                            <h5 class="section-title">
                                <i class="fas fa-user"></i>
                                Thông tin khách hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" 
                                           value="<?= $userInfo ? htmlspecialchars($userInfo->name) : '' ?>" 
                                           placeholder="Nhập họ và tên đầy đủ" required>
                                    <div class="invalid-feedback">Vui lòng nhập họ và tên</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?= $userInfo ? htmlspecialchars($userInfo->email) : '' ?>" 
                                           placeholder="example@email.com" required>
                                    <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="<?= $userInfo ? htmlspecialchars($userInfo->phone ?? '') : '' ?>" 
                                           placeholder="0123456789" required>
                                    <div class="invalid-feedback">Vui lòng nhập số điện thoại</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                    <select class="form-select" name="province" required>
                                        <option value="">Chọn tỉnh/thành phố</option>
                                        <option value="hanoi">Hà Nội</option>
                                        <option value="hcm">TP. Hồ Chí Minh</option>
                                        <option value="danang">Đà Nẵng</option>
                                        <option value="haiphong">Hải Phòng</option>
                                        <option value="cantho">Cần Thơ</option>
                                    </select>
                                    <div class="invalid-feedback">Vui lòng chọn tỉnh/thành phố</div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="address" rows="3" 
                                              placeholder="Số nhà, tên đường, phường/xã, quận/huyện" 
                                              required><?= $userInfo ? htmlspecialchars($userInfo->address ?? '') : '' ?></textarea>
                                    <div class="invalid-feedback">Vui lòng nhập địa chỉ giao hàng</div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">Ghi chú đơn hàng</label>
                                    <textarea class="form-control" name="notes" rows="2" 
                                              placeholder="Ghi chú về đơn hàng, thời gian giao hàng mong muốn..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-card">
                        <div class="card-header">
                            <h5 class="section-title">
                                <i class="fas fa-credit-card"></i>
                                Phương thức thanh toán
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check payment-method">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                        <label class="form-check-label w-100" for="cod">
                                            <div class="payment-option p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-truck text-success fs-4 me-3"></i>
                                                    <div>
                                                        <div class="fw-bold">Thanh toán khi nhận hàng</div>
                                                        <small class="text-muted">Trả tiền mặt khi nhận hàng</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-check payment-method">
                                        <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer">
                                        <label class="form-check-label w-100" for="bank_transfer">
                                            <div class="payment-option p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-university text-primary fs-4 me-3"></i>
                                                    <div>
                                                        <div class="fw-bold">Chuyển khoản ngân hàng</div>
                                                        <small class="text-muted">Chuyển khoản trước khi giao hàng</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="checkout-card">
                        <div class="card-header">
                            <h5 class="section-title">
                                <i class="fas fa-shield-alt"></i>
                                Chính sách & Bảo đảm
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-shipping-fast text-primary fs-2 mb-2"></i>
                                    <div class="fw-bold">Giao hàng nhanh</div>
                                    <small class="text-muted">Giao hàng trong 1-2 ngày</small>
                                </div>
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-undo text-success fs-2 mb-2"></i>
                                    <div class="fw-bold">Đổi trả 30 ngày</div>
                                    <small class="text-muted">Đổi trả miễn phí trong 30 ngày</small>
                                </div>
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-certificate text-warning fs-2 mb-2"></i>
                                    <div class="fw-bold">Bảo hành chính hãng</div>
                                    <small class="text-muted">Bảo hành toàn diện 2 năm</small>
                                </div>
                            </div>
                        </div>
                    </div>
           
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="checkout-card summary-card">
                    <div class="card-header">
                        <h5 class="section-title">
                            <i class="fas fa-receipt"></i>
                            Tóm tắt đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Cart Items -->
                        <div class="order-items mb-4" style="max-height: 400px; overflow-y: auto;">
                            <?php if (!empty($cartItems)): ?>
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="product-item">
                                          <?php 
                                // Get primary image URL using the Product model method
                                           $productModel = new Product();
                                            $imageUrl = $productModel->getPrimaryImageUrl($item['product']->product_id);
                                        ?>
                                             <img src="<?php echo $imageUrl; ?>" 
                                              alt="<?php echo htmlspecialchars($item['product']->name); ?>" 
                                              class="product-image">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold mb-1"><?= htmlspecialchars($item['product']->name) ?></div>
                                            <div class="text-muted small mb-2">
                                                <?php if ($item['size']): ?>
                                                    <span class="badge bg-light text-dark me-1">Size: <?= htmlspecialchars($item['size']) ?></span>
                                                <?php endif; ?>
                                                <?php if ($item['color']): ?>
                                                    <span class="badge bg-light text-dark me-1">Màu: <?= htmlspecialchars($item['color']) ?></span>
                                                <?php endif; ?>
                                                <span class="badge bg-primary">SL: <?= $item['quantity'] ?></span>
                                            </div>
                                            <div class="fw-bold text-primary"><?= number_format($item['subtotal'], 0, ',', '.') ?>₫</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-shopping-cart fs-2 mb-2"></i>
                                    <div>Không có sản phẩm nào</div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Order Summary -->
                        <div class="order-summary">
                            <div class="summary-row">
                                <span>Tạm tính:</span>
                                <span class="fw-semibold"><?= number_format($cartSummary['subtotal'] ?? 0, 0, ',', '.') ?>₫</span>
                            </div>
                            <div class="summary-row">
                                <span>Phí vận chuyển:</span>
                                <span class="fw-semibold">
                                    <?php if (($cartSummary['shipping'] ?? 0) == 0): ?>
                                        <span class="text-success">Miễn phí</span>
                                    <?php else: ?>
                                        <?= number_format($cartSummary['shipping'], 0, ',', '.') ?>₫
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="summary-row">
                                <span>Thuế VAT (10%):</span>
                                <span class="fw-semibold"><?= number_format($cartSummary['tax'] ?? 0, 0, ',', '.') ?>₫</span>
                            </div>
                            <div class="summary-row total">
                                <span>Tổng cộng:</span>
                                <span class="text-primary"><?= number_format($cartSummary['total'] ?? 0, 0, ',', '.') ?>₫</span>
                            </div>
                        </div>

                        <!-- Free Shipping Notice -->
                        <?php if (($cartSummary['subtotal'] ?? 0) < 500000): ?>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-truck me-2"></i>
                                <small>Mua thêm <?= number_format(500000 - ($cartSummary['subtotal'] ?? 0), 0, ',', '.') ?>₫ để được miễn phí vận chuyển!</small>
                            </div>
                        <?php endif; ?>

                        <!-- Place Order Button -->
                        <button type="submit" form="checkoutForm" class="btn btn-primary btn-checkout mt-3">
                            <i class="fas fa-lock me-2"></i>
                            Đặt hàng ngay
                        </button>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Thanh toán được bảo mật với SSL
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (($cartSummary['subtotal'] ?? 0) < 500000): ?>
            <div class="alert alert-info mt-3 mb-0">
                <small>
                    <i class="fas fa-info-circle me-1"></i>
                    Mua thêm <?= number_format(500000 - ($cartSummary['subtotal'] ?? 0)) ?>₫ để được miễn phí vận chuyển
                </small>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center">
            <div class="modal-body py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0">Đang xử lý đơn hàng...</p>
            </div>
        </div>
    </div>
</div>
<style>
.payment-method .form-check-input:checked + .form-check-label .payment-option {
    border-color: #0d6efd !important;
    background-color: rgba(13, 110, 253, 0.1);
}

.payment-option {
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-option:hover {
    border-color: #0d6efd !important;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.order-items::-webkit-scrollbar {
    width: 4px;
}

.order-items::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.order-items::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.order-items::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkoutForm');
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const bankDetails = document.getElementById('bank-details');
    const qrDetails = document.getElementById('qr-details');
    const loadingModalEl = document.getElementById('loadingModal');
    const loadingModal = loadingModalEl ? new bootstrap.Modal(loadingModalEl) : null;

    // Payment method change handler (guard elements exist)
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (bankDetails) bankDetails.classList.add('d-none');
            if (qrDetails) qrDetails.classList.add('d-none');

            if (this.value === 'bank_transfer' && bankDetails) {
                bankDetails.classList.remove('d-none');
            } else if (this.value === 'qr_code' && qrDetails) {
                qrDetails.classList.remove('d-none');
            }
        });
    });

    // Form submission handler (guard form exists)
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate form
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            // Show loading modal if available
            if (loadingModal) loadingModal.show();

            // Prepare form data
            const formData = new FormData(form);

            // Submit checkout (use url helper)
            fetch('<?= url('/checkout/process') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (loadingModal) loadingModal.hide();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đặt hàng thành công!',
                        text: 'Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn sớm nhất.',
                        confirmButtonText: 'Xem đơn hàng',
                        confirmButtonColor: '#0d6efd',
                        allowOutsideClick: false
                    }).then(() => {
                        if (data.data && data.data.redirect) {
                            window.location.href = data.data.redirect;
                        } else {
                            window.location.href = '<?= url('/') ?>';
                        }
                    });
                } else {
                    let errorMessage = data.message || 'Có lỗi xảy ra khi đặt hàng';
                    if (data.data && typeof data.data === 'object') {
                        errorMessage = 'Vui lòng kiểm tra lại thông tin:\n';
                        Object.values(data.data).forEach(error => {
                            errorMessage += '• ' + error + '\n';
                        });
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi đặt hàng',
                        text: errorMessage,
                        confirmButtonText: 'Thử lại',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                if (loadingModal) loadingModal.hide();
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi kết nối',
                    text: 'Không thể kết nối đến máy chủ. Vui lòng thử lại.',
                    confirmButtonText: 'Thử lại',
                    confirmButtonColor: '#dc3545'
                });
            });
        });
    }
});
</script>
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-gem me-2"></i>JEWELRY</h5>
                    <p class="text-light">Chuyên cung cấp trang sức cao cấp với chất lượng tốt nhất.</p>
                </div>
                <div class="col-md-4">
                    <h5>Liên hệ</h5>
                    <ul class="list-unstyled text-light">
                        <li><i class="fas fa-phone me-2"></i>1900 1234</li>
                        <li><i class="fas fa-envelope me-2"></i>info@jewelry.com</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>123 Đường ABC, Quận 1, TP.HCM</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Theo dõi chúng tôi</h5>
                    <div class="social-icons">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-2x"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-youtube fa-2x"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
