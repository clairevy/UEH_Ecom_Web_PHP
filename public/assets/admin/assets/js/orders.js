// Orders page specific JavaScript
window.pageConfig = window.pageConfig || {};
window.pageConfig.sidebar = window.pageConfig.sidebar || {};
window.pageConfig.sidebar.activePage = 'orders';

document.addEventListener('DOMContentLoaded', function() {
    console.log('Orders page JavaScript loaded');
    
    // Initialize orders page functionality
    initializeOrdersPage();
});

function initializeOrdersPage() {
    // Search functionality
    const searchInput = document.getElementById('orderSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const orderId = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const customerName = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                
                if (orderId.includes(searchTerm) || customerName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Status filter functionality
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const selectedStatus = this.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const statusBadge = row.querySelector('.badge');
                const statusText = statusBadge?.textContent.toLowerCase() || '';
                
                if (selectedStatus === 'all') {
                    row.style.display = '';
                } else if (statusText.includes(selectedStatus)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Bulk actions
    const bulkActions = document.querySelectorAll('.bulk-action');
    bulkActions.forEach(action => {
        action.addEventListener('click', function() {
            const selectedRows = document.querySelectorAll('tbody input[type="checkbox"]:checked');
            
            if (selectedRows.length === 0) {
                alert('Vui lòng chọn ít nhất một đơn hàng!');
                return;
            }
            
            const actionType = this.dataset.action;
            if (confirm(`Bạn có chắc chắn muốn ${actionType} ${selectedRows.length} đơn hàng?`)) {
                // Implement bulk action logic here
                console.log(`Bulk ${actionType} for ${selectedRows.length} orders`);
            }
        });
    });
}

// Order management functions
function viewOrder(orderId) {
    window.location.href = `index.php?url=order-details&id=${orderId}`;
}

function updateOrderStatus(orderId) {
    const newStatus = prompt('Nhập trạng thái mới (delivered, shipped, paid, cancelled, pending):');
    if (newStatus) {
        // Implement status update logic
        console.log(`Updating order ${orderId} to status: ${newStatus}`);
    }
}

function deleteOrder(orderId) {
    if (confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')) {
        // Implement delete logic
        console.log(`Deleting order ${orderId}`);
    }
}
