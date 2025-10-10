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
                    <th>Phương Thức Thanh Toán</th>
                    <th>Trạng Thái</th>
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
                                <a href="index.php?url=order-details&id=<?= $order->id ?>" class="text-decoration-none fw-bold text-primary">
                                    #<?= $order->order_id ?? $order->id ?>
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
                            <td>
                                <?php
                                $paymentMethods = [
                                    'paypal' => ['class' => 'bg-info text-dark', 'text' => 'PayPal'],
                                    'credit_card' => ['class' => 'bg-primary', 'text' => 'Thẻ tín dụng'],
                                    'bank_transfer' => ['class' => 'bg-success', 'text' => 'Chuyển khoản'],
                                    'cod' => ['class' => 'bg-warning text-dark', 'text' => 'COD']
                                ];
                                $payment = $paymentMethods[$order->payment_method] ?? $paymentMethods['cod'];
                                ?>
                                <span class="badge <?= $payment['class'] ?>"><?= $payment['text'] ?></span>
                            </td>
                            <td>
                                <?php
                                $statusConfig = [
                                    'delivered' => ['class' => 'delivered', 'text' => 'Đã giao'],
                                    'shipped' => ['class' => 'shipped', 'text' => 'Đang giao'],
                                    'paid' => ['class' => 'paid', 'text' => 'Đã thanh toán'],
                                    'cancelled' => ['class' => 'canceled', 'text' => 'Đã hủy'],
                                    'pending' => ['class' => 'pending', 'text' => 'Chờ xử lý']
                                ];
                                $status = $statusConfig[$order->order_status] ?? $statusConfig['pending'];
                                ?>
                                <span class="badge badge-<?= $status['class'] ?> badge-custom">
                                    <?= $status['text'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="text-end">
                                    <strong class="text-success">$<?= number_format($order->total_amount, 2) ?></strong>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="viewOrder(<?= $order->id ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                            onclick="updateStatus(<?= $order->id ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteOrder(<?= $order->id ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
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

<script>
function viewOrder(orderId) {
    window.location.href = 'index.php?url=order-details&id=' + orderId;
}

function updateStatus(orderId) {
    const newStatus = prompt('Nhập trạng thái mới (delivered, shipped, paid, cancelled, pending):');
    if (newStatus) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?url=orders&action=updateStatus&id=' + orderId;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = newStatus;
        
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteOrder(orderId) {
    if (confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')) {
        window.location.href = 'index.php?url=orders&action=delete&id=' + orderId;
    }
}
</script>