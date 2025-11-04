<?php
/**
 * Orders View - Pure MVC View
 * Hiển thị data từ database, không có hardcode
 */

// Biến được truyền từ Controller:
// $orders - danh sách orders từ database
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn Hàng - Trang Sức Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
<!-- Recent Purchases -->
<div class="table-card">
    <div class="table-header">
        
        <div class="dropdown">
            <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                <img src="https://cdn-icons-png.flaticon.com/512/2311/2311524.png" alt="More" width="20" height="20">
            </button>
           
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-nowrap">Mã Đơn</th>
                    <th class="d-none d-md-table-cell text-nowrap">Khách Hàng</th>
                    <th class="d-none d-lg-table-cell text-nowrap">Ngày Đặt</th>
                    <th class="d-none d-xl-table-cell text-nowrap">TT Thanh Toán</th>
                    <th class="text-nowrap">TT Đơn Hàng</th>
                    <th class="text-nowrap">Tổng Tiền</th>
                    <th class="text-nowrap">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <a href="index.php?url=order-details&id=<?= $order->order_id ?>" class="text-decoration-none fw-bold text-primary text-nowrap">
                                    #<?= $order->order_id ?? $order->order_id ?>
                                </a>
                                <!-- Mobile info -->
                                <div class="d-md-none small mt-1">
                                    <div class="text-muted"><?= htmlspecialchars($order->customer_name ?? 'Khách hàng') ?></div>
                                    <div class="text-muted"><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div class="d-flex align-items-center">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User" width="24" height="24" class="rounded-circle me-2 flex-shrink-0">
                                    <div class="min-w-0">
                                        <div class="fw-bold text-truncate"><?= htmlspecialchars($order->customer_name ?? 'Khách hàng') ?></div>
                                        <small class="text-muted text-truncate d-none d-lg-block"><?= htmlspecialchars($order->customer_email ?? '') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <div class="text-nowrap small">
                                    <?= date('d/m/Y', strtotime($order->created_at)) ?>
                                    <br>
                                    <small class="text-muted"><?= date('H:i', strtotime($order->created_at)) ?></small>
                                </div>
                            </td>
                           
                            <!-- Trạng Thái Thanh Toán -->
                            <td class="d-none d-xl-table-cell">
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
                                        style="width: auto; min-width: 120px;">
                                    <option value="unpaid" <?= $paymentStatus === 'unpaid' ? 'selected' : '' ?> 
                                            class="text-warning"> Chưa TT</option>
                                    <option value="paid" <?= $paymentStatus === 'paid' ? 'selected' : '' ?> 
                                            class="text-success"> Đã TT</option>
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
                                        style="width: auto; min-width: 120px;">
                                    <option value="pending" <?= $orderStatus === 'pending' ? 'selected' : '' ?>>
                                        <span class="d-none d-lg-inline">Chờ xác nhận</span>
                                        
                                    </option>
                                    <option value="confirmed" <?= $orderStatus === 'confirmed' ? 'selected' : '' ?>>
                                        <span class="d-none d-lg-inline">Đã xác nhận</span>
                                       
                                    </option>
                                    <option value="shipping" <?= $orderStatus === 'shipping' ? 'selected' : '' ?>>
                                        <span class="d-none d-lg-inline">Đang giao</span>
                                        
                                    </option>
                                    <option value="delivered" <?= $orderStatus === 'delivered' ? 'selected' : '' ?>>
                                        <span class="d-none d-lg-inline">Đã giao</span>
                                       
                                    </option>
                                    <option value="cancelled" <?= $orderStatus === 'cancelled' ? 'selected' : '' ?>>
                                        <span class="d-none d-lg-inline"> Đã hủy</span>
                                        
                                    </option>
                                </select>
                            </td>

                            <td>
                                <div class="text-end text-nowrap">
                                    <strong class="text-success small">$<?= number_format($order->total_amount, 0) ?></strong>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-success btn-sm p-1" 
                                            onclick="editOrder(<?= $order->order_id ?>)" title="Chỉnh sửa">
                                        <img src="https://cdn-icons-png.flaticon.com/512/1159/1159633.png" alt="Edit" width="14" height="14">
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm p-1" 
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
            </main>
        </div>
    </div>

    <!-- Component Manager -->
    <script src="app/views/admin/components/component-manager.js"></script>
    
    <!-- Page Configuration -->
    <script>
        window.pageConfig = {
            sidebar: {
                brandName: 'Trang Sức',
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
                categories: [],
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: 'Đơn Hàng',
                breadcrumb: 'Home > Đơn Hàng'
            }
        };
    </script>
    
    <!-- Orders Management Functions -->
    <script>
        // Chỉnh sửa đơn hàng
        function editOrder(orderId) {
            // Redirect đến trang chi tiết đơn hàng để chỉnh sửa
            window.location.href = '<?= BASE_URL ?>/admin/index.php?url=order-details&id=' + orderId;
        }

        // Xóa đơn hàng
        function deleteOrder(orderId) {
            // Hiển thị modal xác nhận xóa
            const modal = new bootstrap.Modal(document.getElementById('deleteOrderModal'));
            document.getElementById('deleteOrderId').value = orderId;
            modal.show();
        }

        // Xác nhận xóa đơn hàng
        function confirmDeleteOrder() {
            const orderId = document.getElementById('deleteOrderId').value;
            const checkbox = document.getElementById('confirmDeleteCheckbox');
            
            if (!checkbox.checked) {
                alert('Vui lòng xác nhận bằng cách check vào ô');
                return;
            }

            // Gửi request xóa
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= BASE_URL ?>/admin/index.php?url=orders&action=delete&id=' + orderId;
            document.body.appendChild(form);
            form.submit();
        }

        // Cập nhật trạng thái thanh toán
        function updatePaymentStatus(orderId, status) {
            if (confirm('Bạn có chắc muốn cập nhật trạng thái thanh toán?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= BASE_URL ?>/admin/index.php?url=orders&action=updatePayment&id=' + orderId;
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'payment_status';
                input.value = status;
                form.appendChild(input);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Cập nhật trạng thái đơn hàng
        function updateOrderStatus(orderId, status) {
            if (confirm('Bạn có chắc muốn cập nhật trạng thái đơn hàng?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= BASE_URL ?>/admin/index.php?url=orders&action=updateStatus&id=' + orderId;
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'order_status';
                input.value = status;
                form.appendChild(input);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <!-- Modal Xác Nhận Xóa Đơn Hàng -->
    <div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteOrderModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Xác Nhận Xóa Đơn Hàng
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3"><strong>Cảnh báo:</strong> Hành động này sẽ xóa vĩnh viễn đơn hàng và không thể hoàn tác!</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmDeleteCheckbox">
                        <label class="form-check-label" for="confirmDeleteCheckbox">
                            Tôi hiểu và muốn xóa đơn hàng này
                        </label>
                    </div>
                    <input type="hidden" id="deleteOrderId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDeleteOrder()">
                        <i class="fas fa-trash me-2"></i>Xóa Đơn Hàng
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="app/views/admin/assets/js/main.js"></script>
</body>
</html>