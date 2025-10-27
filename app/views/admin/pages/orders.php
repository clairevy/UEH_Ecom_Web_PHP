<?php
/**
 * Orders View - Pure MVC View
 * Hiển thị data từ database, không có hardcode
 */

// Biến được truyền từ Controller:
// $orders - danh sách orders từ database
?>

<!-- Recent Purchases -->
<div class="table-card">
    <div class="table-header">
        <h5 class="table-title">Đơn Hàng Gần Đây</h5>
        <div class="dropdown">
            <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                <img src="https://cdn-icons-png.flaticon.com/512/2311/2311524.png" alt="More" width="20" height="20">
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Xuất CSV</a></li>
                <li><a class="dropdown-item" href="#">Xuất PDF</a></li>
                <li><a class="dropdown-item" href="#">In</a></li>
            </ul>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" class="form-check-input">
                    </th>
                    <th>Mã Đơn Hàng</th>
                    <th>Khách Hàng</th>
                    <th>Ngày Đặt</th>
                    <th>Trạng Thái Thanh Toán</th>
                    <th>Trạng Thái Đơn Hàng</th>
                    <th>Tổng Tiền</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td>
                                <a href="index.php?url=order-details&id=<?= $order->order_id ?>" class="text-decoration-none fw-bold text-primary">
                                    #<?= $order->order_id ?? $order->order_id ?>
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User" width="24" height="24" class="rounded-circle me-2">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($order->customer_name ?? 'Khách hàng') ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($order->customer_email ?? '') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-nowrap">
                                    <?= date('d/m/Y', strtotime($order->created_at)) ?>
                                    <br>
                                    <small class="text-muted"><?= date('H:i', strtotime($order->created_at)) ?></small>
                                </div>
                            </td>
                           
                            <!-- Trạng Thái Thanh Toán -->
                            <td>
                                <?php
                                $paymentStatus = $order->payment_status ?? 'unpaid';
                                $paymentConfig = [
                                    'paid' => ['class' => 'success', 'text' => 'Đã thanh toán'],
                                    'unpaid' => ['class' => 'warning', 'text' => 'Chưa thanh toán']
                                ];
                                $currentPayment = $paymentConfig[$paymentStatus] ?? $paymentConfig['unpaid'];
                                ?>
                                <select class="form-select form-select-sm badge-select" 
                                        onchange="updatePaymentStatus(<?= $order->order_id ?>, this.value)"
                                        style="width: auto; min-width: 140px;">
                                    <option value="unpaid" <?= $paymentStatus === 'unpaid' ? 'selected' : '' ?> 
                                            class="text-warning">⚠️ Chưa thanh toán</option>
                                    <option value="paid" <?= $paymentStatus === 'paid' ? 'selected' : '' ?> 
                                            class="text-success">✓ Đã thanh toán</option>
                                </select>
                            </td>

                            <!-- Trạng Thái Đơn Hàng -->
                            <td>
                                <?php
                                $orderStatus = $order->order_status ?? 'pending';
                                $orderConfig = [
                                    'pending' => ['class' => 'warning', 'text' => 'Chờ xác nhận'],
                                    'confirmed' => ['class' => 'info', 'text' => 'Đã xác nhận'],
                                    'shipping' => ['class' => 'primary', 'text' => 'Đang giao hàng'],
                                    'delivered' => ['class' => 'success', 'text' => 'Đã giao hàng'],
                                    'cancelled' => ['class' => 'danger', 'text' => 'Đã hủy']
                                ];
                                $currentOrder = $orderConfig[$orderStatus] ?? $orderConfig['pending'];
                                ?>
                                <select class="form-select form-select-sm badge-select" 
                                        onchange="updateOrderStatus(<?= $order->order_id ?>, this.value)"
                                        style="width: auto; min-width: 150px;">
                                    <option value="pending" <?= $orderStatus === 'pending' ? 'selected' : '' ?>>
                                        ⏳ Chờ xác nhận
                                    </option>
                                    <option value="confirmed" <?= $orderStatus === 'confirmed' ? 'selected' : '' ?>>
                                        ✓ Đã xác nhận
                                    </option>
                                    <option value="shipping" <?= $orderStatus === 'shipping' ? 'selected' : '' ?>>
                                        🚚 Đang giao hàng
                                    </option>
                                    <option value="delivered" <?= $orderStatus === 'delivered' ? 'selected' : '' ?>>
                                        ✅ Đã giao hàng
                                    </option>
                                    <option value="cancelled" <?= $orderStatus === 'cancelled' ? 'selected' : '' ?>>
                                        ❌ Đã hủy
                                    </option>
                                </select>
                            </td>

                            <td>
                                <div class="text-end">
                                    <strong class="text-success">$<?= number_format($order->total_amount, 2) ?></strong>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary" 
                                            onclick="viewOrderDetails(<?= $order->order_id ?>)" title="Xem chi tiết">
                                        <img src="https://cdn-icons-png.flaticon.com/512/709/709612.png" alt="View" width="14" height="14">
                                    </button>
                                    <button type="button" class="btn btn-outline-success" 
                                            onclick="editOrder(<?= $order->order_id ?>)" title="Chỉnh sửa">
                                        <img src="https://cdn-icons-png.flaticon.com/512/1159/1159633.png" alt="Edit" width="14" height="14">
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteOrder(<?= $order->order_id ?>)" title="Xóa">
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
                                <p>Chưa có đơn hàng nào</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Orders JavaScript được load từ file riêng (MVC Best Practice) -->
<!-- File: app/views/admin/assets/js/orders.js -->

<style>
/* Custom styling for select dropdown */
.badge-select {
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.badge-select:hover {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.badge-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>

<!-- Load Orders JavaScript -->
<script src="app/views/admin/assets/js/orders.js"></script>