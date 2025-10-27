// Real-time preview updates
document.addEventListener('DOMContentLoaded', function() {
    const categoryNameInput = document.getElementById('categoryName');
    if (categoryNameInput) {
        categoryNameInput.addEventListener('input', function() {
            const name = this.value || 'Tên Danh Mục';
            const previewName = document.getElementById('previewName');
            if (previewName) {
                previewName.textContent = name;
            }
        });
    }

    const categoryDescInput = document.getElementById('categoryDescription');
    if (categoryDescInput) {
        categoryDescInput.addEventListener('input', function() {
            const desc = this.value || 'Mô tả danh mục...';
            const previewDesc = document.getElementById('previewDescription');
            if (previewDesc) {
                previewDesc.textContent = desc;
            }
        });
    }

    const categorySlugInput = document.getElementById('categorySlug');
    if (categorySlugInput && categoryNameInput) {
        categoryNameInput.addEventListener('input', function() {
            const slug = generateSlug(this.value);
            categorySlugInput.value = slug;
        });
    }

    // Form submission
    const addCategoryForm = document.getElementById('addCategoryForm');
    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (confirm('Bạn có chắc chắn muốn thêm danh mục này?')) {
                console.log('Adding category...');
                alert('Đã thêm danh mục thành công!');
                window.location.href = 'categories.html';
            }
        });
    }
});

// Generate slug from name
function generateSlug(text) {
    return text
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

// Save as draft
function saveDraft() {
    console.log('Saving draft...');
    alert('Đã lưu nháp!');
}

// Handle image upload
function handleImageUpload(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('categoryImagePreview');
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    }
}
