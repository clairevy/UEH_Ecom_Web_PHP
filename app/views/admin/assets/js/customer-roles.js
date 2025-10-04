// Role management functions
function addNewRole() {
    alert('Chức năng tạo vai trò mới sẽ được triển khai!');
}

function editRole(roleId) {
    alert('Chỉnh sửa vai trò: ' + roleId);
}

function deleteRole(roleId) {
    if (confirm('Bạn có chắc chắn muốn xóa vai trò này? Tất cả người dùng có vai trò này sẽ cần được gán vai trò khác!')) {
        console.log('Deleting role:', roleId);
        alert('Vai trò đã được xóa: ' + roleId);
        location.reload();
    }
}

function viewRoleUsers(roleId) {
    window.location.href = 'customers.html?role=' + roleId;
}

function exportRoles() {
    console.log('Exporting roles...');
    alert('Xuất danh sách vai trò thành công!');
}

function roleSettings() {
    alert('Cài đặt vai trò sẽ được triển khai!');
}

function toggleRoleStatus(roleId) {
    if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái vai trò này?')) {
        console.log('Toggling role status:', roleId);
        alert('Đã thay đổi trạng thái!');
        location.reload();
    }
}

function duplicateRole(roleId) {
    if (confirm('Bạn có muốn nhân bản vai trò này?')) {
        console.log('Duplicating role:', roleId);
        alert('Đã nhân bản vai trò!');
        location.reload();
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('roleSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.role-card');
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});
