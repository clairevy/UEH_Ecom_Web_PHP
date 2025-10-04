// Wait for component manager to initialize
if (window.ComponentManager) {
    window.ComponentManager.init().then(() => {
        console.log('Categories management page initialized');
        initializeCategoriesPage();
    });
}

// Categories page initialization
function initializeCategoriesPage() {
    // Select All Checkbox
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        });
    }

    // Category Search
    const searchInput = document.getElementById('categorySearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#categoriesTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
}

// Filter Categories
function filterCategories(type) {
    const rows = document.querySelectorAll('#categoriesTable tbody tr');
    rows.forEach(row => {
        const status = row.querySelector('.badge-custom')?.textContent.trim();
        
        if (type === 'all') {
            row.style.display = '';
        } else if (type === 'active' && status === 'Hoạt động') {
            row.style.display = '';
        } else if (type === 'inactive' && status === 'Tạm dừng') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Edit Category
function editCategory(categoryId) {
    console.log('Editing category:', categoryId);
    window.location.href = 'add-category.html?id=' + categoryId + '&edit=true';
}

// Delete Category
function deleteCategory(categoryId) {
    if (confirm('Bạn có chắc chắn muốn xóa danh mục này?')) {
        console.log('Deleting category:', categoryId);
        alert('Đã xóa danh mục thành công!');
        location.reload();
    }
}

// View Products in Category
function viewCategoryProducts(categoryId) {
    window.location.href = 'products.html?category=' + categoryId;
}

// Toggle Category Status
function toggleCategoryStatus(categoryId) {
    if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái của danh mục này?')) {
        console.log('Toggling status for category:', categoryId);
        alert('Đã thay đổi trạng thái!');
        location.reload();
    }
}
