// JS extracted from customer-details.html
window.pageConfig = window.pageConfig || {};
window.pageConfig.sidebar = window.pageConfig.sidebar || {};
window.pageConfig.sidebar.brandName = 'JEWELLERY';
window.pageConfig.sidebar.activePage = 'customers';

function toggleStatus() {
    if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái tài khoản?')) {
        alert('Trạng thái đã được cập nhật!');
        location.reload();
    }
}

function deleteCustomer() {
    if (confirm('Bạn có chắc chắn muốn xóa khách hàng này? Hành động này không thể hoàn tác!')) {
        alert('Khách hàng đã được xóa!');
        window.location.href = 'customers.html';
    }
}

function addNewAddress() {
    alert('Chức năng thêm địa chỉ sẽ được triển khai!');
}
