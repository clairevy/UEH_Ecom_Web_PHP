<?php
/**
 * Order Details View - Pure MVC View
 * Hiển thị chi tiết đơn hàng từ database
 * Hỗ trợ cả VIEW mode và EDIT mode (giống product-details pattern)
 * 
 * Biến được truyền từ Controller:
 * - $order: Thông tin đơn hàng từ database
 * - $orderItems: Danh sách sản phẩm trong đơn
 * - $calculations: Totals (subtotal, shipping, discount, total)
 * - $title, $pageTitle, $breadcrumb
 * - $editMode: true/false (cho phép chỉnh sửa hay chỉ xem)
 */

$editMode = $editMode ?? true; // Mặc định là edit mode (để admin có thể sửa)
$order = $order ?? null;

if (!$order) {
    echo "<div class='alert alert-danger'>Không tìm thấy đơn hàng!</div>";
    return;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Chi Tiết Đơn Hàng') ?> - KICKS Admin</title>
    
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
                    <a href="index.php?url=orders" class="btn btn-outline-secondary btn-sm">
                        <img src="https://cdn-icons-png.flaticon.com/512/271/271220.png" alt="Back" width="14" height="14" class="me-1">
                        Quay lại Danh Sách
                    </a>
                </div>

                <!-- Order Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="mb-1">Đơn Hàng: <span class="badge bg-primary px-3 py-2">#<?= $order->order_id ?></span></h3>
                        <div class="d-flex align-items-center text-muted">
                            <img src="https://cdn-icons-png.flaticon.com/512/2693/2693507.png" alt="Calendar" width="16" height="16" class="me-2">
                            <span><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></span>
                        </div>
                    </div>
                    <div>
                        <?php
                        $statusConfig = [
                            'pending' => ['class' => 'warning', 'text' => 'Chờ xác nhận'],
                            'confirmed' => ['class' => 'info', 'text' => 'Đã xác nhận'],
                            'shipping' => ['class' => 'primary', 'text' => 'Đang giao hàng'],
                            'delivered' => ['class' => 'success', 'text' => 'Đã giao hàng'],
                            'cancelled' => ['class' => 'danger', 'text' => 'Đã hủy']
                        ];
                        $currentStatus = $statusConfig[$order->order_status ?? 'pending'] ?? $statusConfig['pending'];
                        ?>
                        <span class="badge bg-<?= $currentStatus['class'] ?> px-3 py-2">
                            <?= $currentStatus['text'] ?>
                        </span>
                    </div>
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

                <!-- Order Information Cards -->
                <div class="row mb-4">
                    <!-- Customer Info -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Customer" width="32" height="32">
                                    </div>
                                </div>
                                <h6 class="fw-bold text-center mb-3">Thông Tin Khách Hàng</h6>
                                
                                <div class="mb-2">
                                    <label class="small text-muted">Họ Tên:</label>
                                    <div class="fw-bold"><?= htmlspecialchars($order->full_name ?? $order->customer_name ?? 'N/A') ?></div>
                                </div>
                                
                                <div class="mb-2">
                                    <label class="small text-muted">Email:</label>
                                    <div class="small"><?= htmlspecialchars($order->email ?? $order->customer_email ?? 'N/A') ?></div>
                                </div>
                                
                                <div class="mb-2">
                                    <label class="small text-muted">Điện Thoại:</label>
                                    <div class="small"><?= htmlspecialchars($order->phone ?? 'N/A') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Status Management (EDIT MODE) -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <img src="https://cdn-icons-png.flaticon.com/512/3063/3063822.png" alt="Order" width="32" height="32">
                                    </div>
                                </div>
                                <h6 class="fw-bold text-center mb-3">Trạng Thái Đơn Hàng</h6>
                                
                                <?php if ($editMode): ?>
                                    <!-- Editable: Dropdown -->
                                    <div class="mb-3">
                                        <label class="small text-muted mb-1">Trạng Thái Thanh Toán:</label>
                                        <select class="form-select form-select-sm" 
                                                onchange="updatePaymentStatus(<?= $order->order_id ?>, this.value)"
                                                data-original="<?= $order->payment_status ?? 'unpaid' ?>">
                                            <option value="unpaid" <?= ($order->payment_status ?? 'unpaid') === 'unpaid' ? 'selected' : '' ?>>
                                                ⚠️ Chưa thanh toán
                                            </option>
                                            <option value="paid" <?= ($order->payment_status ?? 'unpaid') === 'paid' ? 'selected' : '' ?>>
                                                ✓ Đã thanh toán
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="small text-muted mb-1">Trạng Thái Đơn:</label>
                                        <select class="form-select form-select-sm" 
                                                onchange="updateOrderStatus(<?= $order->order_id ?>, this.value)"
                                                data-original="<?= $order->order_status ?? 'pending' ?>">
                                            <option value="pending" <?= ($order->order_status ?? 'pending') === 'pending' ? 'selected' : '' ?>>⏳ Chờ xác nhận</option>
                                            <option value="confirmed" <?= ($order->order_status ?? '') === 'confirmed' ? 'selected' : '' ?>>✓ Đã xác nhận</option>
                                            <option value="shipping" <?= ($order->order_status ?? '') === 'shipping' ? 'selected' : '' ?>>🚚 Đang giao hàng</option>
                                            <option value="delivered" <?= ($order->order_status ?? '') === 'delivered' ? 'selected' : '' ?>>✅ Đã giao hàng</option>
                                            <option value="cancelled" <?= ($order->order_status ?? '') === 'cancelled' ? 'selected' : '' ?>>❌ Đã hủy</option>
                                        </select>
                                    </div>
                                <?php else: ?>
                                    <!-- Read-only -->
                                    <div class="mb-2">
                                        <label class="small text-muted">Thanh toán:</label>
                                        <div><?= ($order->payment_status ?? 'unpaid') === 'paid' ? '✓ Đã thanh toán' : '⚠️ Chưa thanh toán' ?></div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="small text-muted">Trạng thái:</label>
                                        <div><?= $currentStatus['text'] ?></div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="text-center small text-muted mt-2">
                                    Ngày đặt: <?= date('d/m/Y', strtotime($order->created_at)) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <img src="https://cdn-icons-png.flaticon.com/512/2072/2072130.png" alt="Delivery" width="32" height="32">
                                    </div>
                                </div>
                                <h6 class="fw-bold text-center mb-3">Địa Chỉ Giao Hàng</h6>
                                
                                <div class="small">
                                    <div class="mb-1"><strong><?= htmlspecialchars($order->street ?? 'N/A') ?></strong></div>
                                    <div class="mb-1 text-muted"><?= htmlspecialchars($order->ward ?? '') ?></div>
                                    <div class="mb-1 text-muted"><?= htmlspecialchars($order->province ?? '') ?></div>
                                    <div class="text-muted"><?= htmlspecialchars($order->country ?? 'Vietnam') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="table-card">
                    <div class="table-header">
                        <h5 class="table-title">Sản Phẩm Trong Đơn Hàng</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sản Phẩm</th>
                                    <th class="text-center">Số Lượng</th>
                                    <th class="text-end">Đơn Giá</th>
                                    <th class="text-end">Thành Tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orderItems)): ?>
                                    <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://via.placeholder.com/40x40?text=P" 
                                                         alt="Product" 
                                                         width="40" 
                                                         height="40" 
                                                         class="rounded me-3">
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($item->product_name ?? 'N/A') ?></div>
                                                        <?php if (!empty($item->variant_name)): ?>
                                                            <div class="small text-muted">Variant: <?= htmlspecialchars($item->variant_name) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary"><?= $item->quantity ?></span>
                                            </td>
                                            <td class="text-end"><?= number_format($item->unit_price_snapshot, 0, ',', '.') ?> VND</td>
                                            <td class="text-end fw-bold text-primary">
                                                <?= number_format($item->total_price ?? ($item->quantity * $item->unit_price_snapshot), 0, ',', '.') ?> VND
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            Không có sản phẩm trong đơn hàng này
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Order Summary -->
                    <div class="card-footer bg-light">
                        <div class="row justify-content-end">
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Tạm tính:</span>
                                    <span class="fw-bold"><?= number_format($calculations['subtotal'], 0, ',', '.') ?> VND</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Phí vận chuyển:</span>
                                    <span class="fw-bold"><?= number_format($calculations['shipping_fee'], 0, ',', '.') ?> VND</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Giảm giá:</span>
                                    <span class="fw-bold text-danger">
                                        <?php if ($calculations['discount_amount'] > 0): ?>
                                            -<?= number_format($calculations['discount_amount'], 0, ',', '.') ?> VND
                                            <?php if (!empty($calculations['discount_code'])): ?>
                                                <br><small class="text-muted">(<?= htmlspecialchars($calculations['discount_code']) ?>)</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            0 VND
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold fs-6">Tổng Cộng:</span>
                                    <span class="fw-bold fs-5 text-success"><?= number_format($calculations['total'], 0, ',', '.') ?> VND</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="index.php?url=orders" class="btn btn-outline-secondary">
                        <img src="https://cdn-icons-png.flaticon.com/512/271/271220.png" alt="Back" width="16" height="16" class="me-1">
                        Quay Lại
                    </a>
                    <button class="btn btn-primary" onclick="printOrder()">
                        <img src="https://cdn-icons-png.flaticon.com/512/2965/2965937.png" alt="Print" width="16" height="16" class="me-1">
                        In Đơn Hàng
                    </button>
                </div>
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
                activePage: 'orders',
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
                title: '<?= htmlspecialchars($pageTitle ?? 'Chi Tiết Đơn Hàng') ?>',
                breadcrumb: '<?= htmlspecialchars($breadcrumb ?? 'Home > Đơn Hàng > Chi Tiết') ?>'
            }
        };

        const editMode = <?= $editMode ? 'true' : 'false' ?>;

        // Print order function
        function printOrder() {
            window.print();
        }

        // Update payment status (Inline edit - MVC compliant)
        function updatePaymentStatus(orderId, newStatus) {
            const selectElement = event.target;
            const originalValue = selectElement.getAttribute('data-original');
            const statusText = newStatus === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
            
            if (confirm(`Bạn có chắc chắn muốn thay đổi trạng thái thanh toán thành "${statusText}"?`)) {
                selectElement.disabled = true;
                selectElement.style.opacity = '0.6';
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php?url=orders&action=updatePayment&id=' + orderId;
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'payment_status';
                statusInput.value = newStatus;
                
                form.appendChild(statusInput);
                document.body.appendChild(form);
                form.submit();
            } else {
                selectElement.value = originalValue;
            }
        }

        // Update order status (Inline edit - MVC compliant)
        function updateOrderStatus(orderId, newStatus) {
            const selectElement = event.target;
            const originalValue = selectElement.getAttribute('data-original');
            const statusTexts = {
                'pending': 'Chờ xác nhận',
                'confirmed': 'Đã xác nhận',
                'shipping': 'Đang giao hàng',
                'delivered': 'Đã giao hàng',
                'cancelled': 'Đã hủy'
            };
            
            const statusText = statusTexts[newStatus] || newStatus;
            
            if (confirm(`Bạn có chắc chắn muốn thay đổi trạng thái đơn hàng thành "${statusText}"?`)) {
                selectElement.disabled = true;
                selectElement.style.opacity = '0.6';
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php?url=orders&action=updateOrder&id=' + orderId;
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'order_status';
                statusInput.value = newStatus;
                
                form.appendChild(statusInput);
                document.body.appendChild(form);
                form.submit();
            } else {
                selectElement.value = originalValue;
            }
        }
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="app/views/admin/assets/js/main.js"></script>
</body>
</html>
