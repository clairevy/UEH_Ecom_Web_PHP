// JS for products page (extracted)
window.pageConfig = window.pageConfig || {};
window.pageConfig.sidebar = window.pageConfig.sidebar || {};
window.pageConfig.sidebar.activePage = 'products';

function initializeProductsPage() {
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => checkbox.checked = selectAll.checked);
        });
    }

    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#productsTable tbody tr');
            rows.forEach(row => { row.style.display = row.textContent.toLowerCase().includes(searchTerm) ? '' : 'none'; });
        });
    }
}

function filterProducts(type) {
    const rows = document.querySelectorAll('#productsTable tbody tr');
    rows.forEach(row => {
        const status = row.querySelector('.badge-custom')?.textContent.trim();
        if (type === 'all') row.style.display = '';
        else if (type === 'in-stock' && status === 'Còn hàng') row.style.display = '';
        else if (type === 'out-of-stock' && status === 'Hết hàng') row.style.display = '';
        else if (type === 'best-seller') { const sold = parseInt(row.cells[5]?.querySelector('.fw-bold')?.textContent || '0'); row.style.display = sold > 50 ? '' : 'none'; }
        else row.style.display = 'none';
    });
}

function editProduct(sku) { window.location.href = 'product-details.html?id=' + sku + '&edit=true'; }
function deleteProduct(sku) { if (confirm('Xóa sản phẩm ' + sku + '?')) { alert('Đã xóa!'); } }

document.addEventListener('DOMContentLoaded', function(){ initializeProductsPage(); });
// JS for products page (extracted)
window.pageConfig = window.pageConfig || {};
window.pageConfig.sidebar = window.pageConfig.sidebar || {};
window.pageConfig.sidebar.activePage = 'products';

function initializeProductsPage() {
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => checkbox.checked = selectAll.checked);
        });
    }

    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#productsTable tbody tr');
            rows.forEach(row => { row.style.display = row.textContent.toLowerCase().includes(searchTerm) ? '' : 'none'; });
        });
    }
}

function filterProducts(type) {
    const rows = document.querySelectorAll('#productsTable tbody tr');
    rows.forEach(row => {
        const status = row.querySelector('.badge-custom')?.textContent.trim();
        if (type === 'all') row.style.display = '';
        else if (type === 'in-stock' && status === 'Còn hàng') row.style.display = '';
        else if (type === 'out-of-stock' && status === 'Hết hàng') row.style.display = '';
        else if (type === 'best-seller') { const sold = parseInt(row.cells[5]?.querySelector('.fw-bold')?.textContent || '0'); row.style.display = sold > 50 ? '' : 'none'; }
        else row.style.display = 'none';
    });
}

function editProduct(sku) { window.location.href = 'product-details.html?id=' + sku + '&edit=true'; }
function deleteProduct(sku) { if (confirm('Xóa sản phẩm ' + sku + '?')) { alert('Đã xóa!'); } }

document.addEventListener('DOMContentLoaded', function(){ initializeProductsPage(); });
