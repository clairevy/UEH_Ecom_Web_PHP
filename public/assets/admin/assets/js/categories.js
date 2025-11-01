// Categories Page - Pure UI JavaScript (No Business Logic)
// Only handles UI interactions, no data processing

// Wait for component manager to initialize
if (window.ComponentManager) {
    window.ComponentManager.init().then(() => {
        console.log('Categories management page initialized');
        initializeCategoriesPage();
    });
}

// Categories page initialization - UI only
function initializeCategoriesPage() {
    // Select All Checkbox - UI interaction only
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        });
    }

    // Category Search - Client-side filtering only
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

    // Image Preview - UI enhancement only
    const imageInput = document.getElementById('categoryImage');
    const previewContainer = document.getElementById('categoryImagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (imageInput && previewContainer && previewImg) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });
    }
}

// Filter Categories - UI filtering only (no server calls)
function filterCategories(type) {
    const rows = document.querySelectorAll('#categoriesTable tbody tr');
    rows.forEach(row => {
        const status = row.querySelector('.badge-custom')?.textContent.trim();
        
        if (type === 'all') {
            row.style.display = '';
        } else if (type === 'active' && status === 'Hoạt động') {
            row.style.display = '';
        } else if (type === 'inactive' && status === 'Không hoạt động') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Note: editCategory() and deleteCategory() functions are defined in the view file
// to ensure they use the correct Front Controller URLs and maintain MVC separation