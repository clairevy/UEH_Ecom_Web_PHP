// JS extracted from discounts.html
window.pageConfig = window.pageConfig || {};
window.pageConfig.sidebar = window.pageConfig.sidebar || {};
window.pageConfig.sidebar.activePage = 'discounts';

function addNewDiscount() {
    alert('Tính năng thêm mã giảm giá mới sẽ được triển khai!');
}

function viewDiscount(discountId) { alert('Xem chi tiết mã giảm giá: ' + discountId); }
function editDiscount(discountId) { alert('Chỉnh sửa mã giảm giá: ' + discountId); }
function copyDiscountCode(discountId) {
    const row = document.querySelector(`tr:nth-child(${discountId})`);
    const code = row ? (row.querySelector('.discount-code') || {}).textContent : '';
    if (code) navigator.clipboard.writeText(code.trim()).then(() => alert('Đã sao chép mã: ' + code));
}
function duplicateDiscount(discountId) { if (confirm('Bạn có muốn nhân bản mã giảm giá này?')) { alert('Mã đã nhân bản!'); location.reload(); } }
function toggleDiscount(discountId) { if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái?')) { alert('Trạng thái đã cập nhật!'); location.reload(); } }
function deleteDiscount(discountId) { if (confirm('Bạn có chắc chắn muốn xóa mã này?')) { alert('Mã đã xóa!'); location.reload(); } }

function bulkActivate() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Vui lòng chọn ít nhất một mã giảm giá!');
        return;
    }
    alert(`Đã kích hoạt ${checkedBoxes.length} mã giảm giá!`);
    location.reload();
}

function bulkDeactivate() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Vui lòng chọn ít nhất một mã giảm giá!');
        return;
    }
    alert(`Đã vô hiệu hóa ${checkedBoxes.length} mã giảm giá!`);
    location.reload();
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Vui lòng chọn ít nhất một mã giảm giá!');
        return;
    }
    if (confirm(`Bạn có chắc chắn muốn xóa ${checkedBoxes.length} mã giảm giá đã chọn?`)) {
        alert('Đã xóa các mã giảm giá đã chọn!');
        location.reload();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const search = document.getElementById('discountSearch');
    if (search) search.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('#discountsTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
    });

    const filter = document.getElementById('statusFilter');
    if (filter) filter.addEventListener('change', function() {
        const v = this.value;
        document.querySelectorAll('#discountsTable tbody tr').forEach(row => {
            const status = (row.querySelector('.badge') || {}).textContent || '';
            if (v === 'all') row.style.display = '';
            else if (v === 'active' && status.toLowerCase().includes('hoạt')) row.style.display = '';
            else if (v === 'inactive' && status.toLowerCase().includes('tạm')) row.style.display = '';
            else if (v === 'expired' && status.toLowerCase().includes('hết')) row.style.display = '';
            else row.style.display = 'none';
        });
    });

    const selectAll = document.getElementById('selectAll');
    if (selectAll) selectAll.addEventListener('change', function() {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
    });
});
