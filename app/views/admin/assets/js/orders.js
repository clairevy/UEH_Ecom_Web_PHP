// JS extracted from orders.html
window.pageConfig = window.pageConfig || {};
window.pageConfig.sidebar = window.pageConfig.sidebar || {};
window.pageConfig.sidebar.activePage = 'orders';

document.addEventListener('DOMContentLoaded', function() {
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        const actionCell = document.createElement('td');
        actionCell.innerHTML = `
            <button class="btn btn-primary-custom btn-sm" onclick="window.location.href='order-details.html'">
                <img src="https://cdn-icons-png.flaticon.com/512/709/709612.png" alt="View" width="14" height="14" class="me-1">
                Xem chi tiết
            </button>
        `;
        row.appendChild(actionCell);
    });
});
