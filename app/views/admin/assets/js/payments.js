// JS extracted from payments.html
window.pageConfig = window.pageConfig || {};
window.pageConfig.sidebar = window.pageConfig.sidebar || {};
window.pageConfig.sidebar.activePage = 'payments';

function viewPayment(paymentId) { alert('Xem chi tiết giao dịch: ' + paymentId); }
function viewInvoice(paymentId) { alert('Xem hóa đơn: ' + paymentId); }
function confirmPayment(paymentId) { if (confirm('Xác nhận giao dịch?')) { alert('Đã xác nhận: ' + paymentId); location.reload(); } }
function cancelPayment(paymentId) { if (confirm('Hủy giao dịch?')) { alert('Đã hủy: ' + paymentId); location.reload(); } }
function refundPayment(paymentId) {
    const amount = prompt('Nhập số tiền hoàn (VND):');
    if (amount && !isNaN(amount) && parseFloat(amount) > 0) {
        if (confirm(`Bạn có chắc chắn muốn hoàn ${amount} VND cho ${paymentId}?`)) { alert('Đã gửi yêu cầu hoàn tiền!'); location.reload(); }
    } else if (amount !== null) alert('Vui lòng nhập số tiền hợp lệ!');
}

function retryPayment(paymentId) {
    if (confirm('Bạn có muốn tạo giao dịch mới cho đơn hàng này?')) {
        alert('Đã tạo giao dịch mới: ' + paymentId);
        location.reload();
    }
}

function viewRefund(paymentId) {
    alert('Xem chi tiết hoàn tiền cho giao dịch: ' + paymentId);
}

function paymentReport() {
    alert('Đang tạo báo cáo chi tiết...');
}

function reconciliation() {
    alert('Đang thực hiện đối soát với ngân hàng...');
}

function bulkReconcile() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Vui lòng chọn ít nhất một giao dịch!');
        return;
    }
    if (confirm(`Đối soát ${checkedBoxes.length} giao dịch đã chọn?`)) {
        alert('Đã đối soát các giao dịch!');
        location.reload();
    }
}

function exportPayments() {
    alert('Xuất danh sách giao dịch thành công!');
}

function filterByDateRange() {
    alert('Chức năng lọc theo khoảng thời gian sẽ được triển khai!');
}

document.addEventListener('DOMContentLoaded', function() {
    const search = document.getElementById('paymentSearch');
    if (search) search.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('#paymentsTable tbody tr').forEach(row => row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none');
    });

    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) statusFilter.addEventListener('change', function() {
        const v = this.value;
        document.querySelectorAll('#paymentsTable tbody tr').forEach(row => {
            const status = (row.querySelector('.badge') || {}).textContent.toLowerCase();
            if (v === 'all') row.style.display = '';
            else if (v === 'completed' && status.includes('thành c')) row.style.display = '';
            else if (v === 'pending' && status.includes('đang')) row.style.display = '';
            else if (v === 'failed' && status.includes('thất')) row.style.display = '';
            else if (v === 'refunded' && status.includes('hoàn')) row.style.display = '';
            else row.style.display = 'none';
        });
    });

    const methodFilter = document.getElementById('methodFilter');
    if (methodFilter) methodFilter.addEventListener('change', function() {
        const v = this.value;
        document.querySelectorAll('#paymentsTable tbody tr').forEach(row => {
            const methodText = row.cells[4]?.textContent.toLowerCase() || '';
            if (v === 'all') row.style.display = '';
            else if (v === 'card' && methodText.includes('visa')) row.style.display = '';
            else if (v === 'bank' && methodText.includes('chuy')) row.style.display = '';
            else if (v === 'momo' && methodText.includes('momo')) row.style.display = '';
            else if (v === 'zalopay' && methodText.includes('zalopay')) row.style.display = '';
            else if (v === 'cod' && methodText.includes('cod')) row.style.display = '';
            else row.style.display = 'none';
        });
    });

    const selectAll = document.getElementById('selectAll');
    if (selectAll) selectAll.addEventListener('change', function() { document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked); });
});
