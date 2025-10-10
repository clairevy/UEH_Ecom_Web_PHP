<?php
/**
 * Status Badge Component - Pure MVC Component
 * Hiển thị trạng thái với data từ database
 */

// Biến được truyền:
// $status - trạng thái (delivered, shipped, pending, cancelled, etc.)
// $text - text hiển thị (tùy chọn, nếu không có sẽ tự động tạo từ $status)
// $type - loại badge (order, review, product, etc.)

// Mapping trạng thái
$statusConfig = [
    'delivered' => ['class' => 'delivered', 'text' => 'Đã giao'],
    'shipped' => ['class' => 'shipped', 'text' => 'Đang giao'],
    'paid' => ['class' => 'paid', 'text' => 'Đã thanh toán'],
    'cancelled' => ['class' => 'canceled', 'text' => 'Đã hủy'],
    'pending' => ['class' => 'pending', 'text' => 'Chờ xử lý'],
    'approved' => ['class' => 'success', 'text' => 'Đã duyệt'],
    'rejected' => ['class' => 'danger', 'text' => 'Từ chối'],
    'active' => ['class' => 'success', 'text' => 'Đang bán'],
    'inactive' => ['class' => 'secondary', 'text' => 'Ngừng bán']
];

$statusInfo = $statusConfig[$status] ?? ['class' => 'secondary', 'text' => ucfirst($status ?? 'Unknown')];
$displayText = $text ?? $statusInfo['text'];
?>

<!-- Status Badge Component -->
<span class="badge badge-<?= $statusInfo['class'] ?> badge-custom" 
      data-status-type="<?= htmlspecialchars($status ?? '') ?>" 
      data-status-text="<?= htmlspecialchars($displayText) ?>">
    <?= htmlspecialchars($displayText) ?>
</span>
