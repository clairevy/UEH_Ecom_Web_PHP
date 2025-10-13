<?php
$title = $data['title'] ?? 'Thanh toán';
$cartItems = $data['cartItems'] ?? [];
$cartSummary = $data['cartSummary'] ?? [];
$userInfo = $data['userInfo'] ?? null;
?>

<div class="container-fluid px-0">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="bg-light py-3">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/Ecom_website/" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/Ecom_website/cart" class="text-decoration-none">Giỏ hàng</a></li>
                <li class="breadcrumb-item active">Thanh toán</li>
            </ol>
        </div>
    </nav>

    <div class="container py-4">
        <h2 class="mb-4"><i class="fas fa-credit-card me-2"></i>Thanh toán</h2>

        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <form id="checkoutForm" class="needs-validation" novalidate>
                    <!-- Customer Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Thông tin khách hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" 
                                           value="<?= $userInfo ? htmlspecialchars($userInfo->name) : '' ?>" required>
                                    <div class="invalid-feedback">Vui lòng nhập họ và tên</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?= $userInfo ? htmlspecialchars($userInfo->email) : '' ?>" required>
                                    <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="<?= $userInfo ? htmlspecialchars($userInfo->phone ?? '') : '' ?>" required>
                                    <div class="invalid-feedback">Vui lòng nhập số điện thoại</div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="address" rows="3" 
                                              placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" required><?= $userInfo ? htmlspecialchars($userInfo->address ?? '') : '' ?></textarea>
                                    <div class="invalid-feedback">Vui lòng nhập địa chỉ giao hàng</div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Ghi chú đơn hàng</label>
                                    <textarea class="form-control" name="notes" rows="2" 
                                              placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Phương thức thanh toán</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-check payment-method">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                        <label class="form-check-label w-100" for="cod">
                                            <div class="payment-option p-3 border rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-truck text-primary me-2"></i>
                                                    <div>
                                                        <strong>Thanh toán khi nhận hàng</strong>
                                                        <small class="d-block text-muted">Trả tiền mặt khi nhận hàng</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <div class="form-check payment-method">
                                        <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer">
                                        <label class="form-check-label w-100" for="bank_transfer">
                                            <div class="payment-option p-3 border rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-university text-success me-2"></i>
                                                    <div>
                                                        <strong>Chuyển khoản ngân hàng</strong>
                                                        <small class="d-block text-muted">Chuyển khoản trước khi giao hàng</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="form-check payment-method">
                                        <input class="form-check-input" type="radio" name="payment_method" id="qr_code" value="qr_code">
                                        <label class="form-check-label w-100" for="qr_code">
                                            <div class="payment-option p-3 border rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-qrcode text-warning me-2"></i>
                                                    <div>
                                                        <strong>Thanh toán QR Code</strong>
                                                        <small class="d-block text-muted">Quét mã QR để thanh toán</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Details -->
                            <div id="bank-details" class="mt-3 p-3 bg-light rounded d-none">
                                <h6 class="text-success">Thông tin chuyển khoản:</h6>
                                <p class="mb-1"><strong>Ngân hàng:</strong> Vietcombank</p>
                                <p class="mb-1"><strong>Số tài khoản:</strong> 0123456789</p>
                                <p class="mb-1"><strong>Chủ tài khoản:</strong> CONG TY TNHH ABC</p>
                                <p class="mb-0"><strong>Nội dung:</strong> [Mã đơn hàng] - [Tên khách hàng]</p>
                            </div>

                            <div id="qr-details" class="mt-3 text-center d-none">
                                <h6 class="text-warning">Quét mã QR để thanh toán:</h6>
                                <div class="qr-code-placeholder bg-light p-4 d-inline-block rounded">
                                    <i class="fas fa-qrcode fa-5x text-muted"></i>
                                    <p class="mt-2 mb-0 small text-muted">Mã QR sẽ được tạo sau khi đặt hàng</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-cart me-2"></i>Đặt hàng
                        </button>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 1rem;">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <!-- Cart Items -->
                        <div class="order-items mb-3" style="max-height: 300px; overflow-y: auto;">
                            <?php if (!empty($cartItems)): ?>
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                        <div class="flex-shrink-0">
                                            <img src="/Ecom_website/public/assets/images/products/<?= htmlspecialchars($item['product']->main_image) ?>" 
                                                 alt="<?= htmlspecialchars($item['product']->name) ?>"
                                                 class="rounded" width="60" height="60" style="object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1"><?= htmlspecialchars($item['product']->name) ?></h6>
                                            <small class="text-muted">
                                                <?php if ($item['size']): ?>Size: <?= htmlspecialchars($item['size']) ?> | <?php endif; ?>
                                                <?php if ($item['color']): ?>Màu: <?= htmlspecialchars($item['color']) ?> | <?php endif; ?>
                                                SL: <?= $item['quantity'] ?>
                                            </small>
                                            <div class="fw-bold text-primary"><?= number_format($item['subtotal']) ?>₫</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Order Summary -->
                        <div class="order-summary">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính:</span>
                                <span><?= number_format($cartSummary['subtotal'] ?? 0) ?>₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển:</span>
                                <span class="text-success">
                                    <?php if (($cartSummary['shipping'] ?? 0) == 0): ?>
                                        Miễn phí
                                    <?php else: ?>
                                        <?= number_format($cartSummary['shipping']) ?>₫
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Thuế VAT (10%):</span>
                                <span><?= number_format($cartSummary['tax'] ?? 0) ?>₫</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5 text-primary">
                                <span>Tổng cộng:</span>
                                <span><?= number_format($cartSummary['total'] ?? 0) ?>₫</span>
                            </div>
                        </div>

                        <!-- Free Shipping Notice -->
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
            </div>
        </div>
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
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));

    // Payment method change handler
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            // Hide all payment details
            bankDetails.classList.add('d-none');
            qrDetails.classList.add('d-none');

            // Show relevant payment details
            if (this.value === 'bank_transfer') {
                bankDetails.classList.remove('d-none');
            } else if (this.value === 'qr_code') {
                qrDetails.classList.remove('d-none');
            }
        });
    });

    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate form
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        // Show loading modal
        loadingModal.show();

        // Prepare form data
        const formData = new FormData(form);

        // Submit checkout
        fetch('/Ecom_website/checkout/process', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingModal.hide();
            
            if (data.success) {
                // Show success message and redirect
                Swal.fire({
                    icon: 'success',
                    title: 'Đặt hàng thành công!',
                    text: 'Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn sớm nhất.',
                    confirmButtonText: 'Xem đơn hàng',
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    if (data.data && data.data.redirect) {
                        window.location.href = data.data.redirect;
                    } else {
                        window.location.href = '/Ecom_website/';
                    }
                });
            } else {
                // Show error message
                let errorMessage = data.message || 'Có lỗi xảy ra khi đặt hàng';
                
                if (data.data && typeof data.data === 'object') {
                    errorMessage += ':\n';
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
            loadingModal.hide();
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

    // Form validation styling
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    });
});
</script>