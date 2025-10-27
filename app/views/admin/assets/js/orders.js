/**
 * Orders Page JavaScript - MVC Compliant
 * Tách riêng JavaScript từ view (Separation of Concerns)
 */

// =================== ORDER MANAGEMENT FUNCTIONS ===================

/**
 * Xem chi tiết đơn hàng
 */
function viewOrderDetails(orderId) {
    window.location.href = 'index.php?url=order-details&id=' + orderId;
}

/**
 * Chỉnh sửa đơn hàng - điều hướng đến trang chi tiết
 */
function editOrder(orderId) {
    console.log('editOrder called with orderId:', orderId);
    window.location.href = 'index.php?url=order-details&id=' + orderId;
}

/**
 * Xóa đơn hàng
 */
function deleteOrder(orderId) {
    if (confirm('Bạn có chắc chắn muốn xóa đơn hàng này? Hành động này không thể hoàn tác!')) {
        // Tạo form POST để delete
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?url=orders&action=delete&id=' + orderId;
        document.body.appendChild(form);
        form.submit();
    }
}

// =================== STATUS UPDATE FUNCTIONS ===================

/**
 * Cập nhật trạng thái thanh toán
 */
function updatePaymentStatus(orderId, newStatus) {
    const selectElement = event.target;
    const originalValue = selectElement.getAttribute('data-original');
    const statusText = newStatus === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
    
    if (confirm(`Bạn có chắc chắn muốn thay đổi trạng thái thanh toán thành "${statusText}"?`)) {
        // Disable dropdown để tránh click lại
        selectElement.disabled = true;
        selectElement.style.opacity = '0.6';
        
        // Tạo form và submit
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
        // Revert về giá trị cũ
        selectElement.value = originalValue;
    }
}

/**
 * Cập nhật trạng thái đơn hàng
 */
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
        // Disable dropdown
        selectElement.disabled = true;
        selectElement.style.opacity = '0.6';
        
        // Tạo form và submit
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
        // Revert
        selectElement.value = originalValue;
    }
}

// =================== INITIALIZATION ===================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Orders page JavaScript loaded');
    
    // Store original values for dropdowns
    document.querySelectorAll('.badge-select').forEach(select => {
        select.setAttribute('data-original', select.value);
        select.addEventListener('focus', function() {
            this.setAttribute('data-original', this.value);
        });
    });
    
    // Test functions
    console.log('editOrder function:', typeof editOrder);
    console.log('viewOrderDetails function:', typeof viewOrderDetails);
});
