/**
 * Add Category JavaScript - Client-side functionality
 * Tuân thủ nguyên tắc Separation of Concerns và OOP
 * - Chỉ xử lý UI/UX và client-side validation
 * - Business logic ở Server-side (Controller/Model)
 */

// Class để quản lý Category Form
class CategoryFormManager {
    constructor() {
        this.form = document.getElementById('addCategoryForm');
        this.fileInput = document.getElementById('category_image');
        this.imagePreviewContainer = document.getElementById('imagePreviewContainer');
        this.previewImg = document.getElementById('previewImg');
        
        this.init();
    }

    init() {
        if (this.form) {
            // Form submit validation (client-side only)
            this.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }

        // Auto-generate slug (optional)
        this.setupSlugGenerator();
    }

    /**
     * Xử lý form submit - Client-side validation only
     */
    handleFormSubmit(e) {
        const name = document.getElementById('name').value.trim();

        let errors = [];

        if (name.length < 2) {
            errors.push('Tên danh mục phải có ít nhất 2 ký tự');
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert('Vui lòng kiểm tra lại:\n' + errors.join('\n'));
            return false;
        }

        // Hiển thị loading indicator
        const submitBtn = this.form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';
        }

        // Let form submit naturally to server
        return true;
    }

    /**
     * Setup auto slug generator (optional feature)
     */
    setupSlugGenerator() {
        const nameInput = document.getElementById('name');
        
        if (nameInput) {
            nameInput.addEventListener('input', function() {
                // Could auto-generate slug here if needed
                console.log('Category name:', this.value);
            });
        }
    }
}

// =================== FILE UPLOAD HANDLING ===================

/**
 * Handle file selection
 */
function handleCategoryFileSelect(input) {
    const file = input.files[0];
    const previewContainer = document.getElementById('imagePreviewContainer');
    const previewImg = document.getElementById('previewImg');
    
    if (!file) {
        previewContainer.style.display = 'none';
        return;
    }

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        alert('Chỉ chấp nhận file ảnh: JPG, PNG, GIF, WEBP');
        input.value = '';
        return;
    }

    // Validate file size (2MB)
    if (file.size > 2097152) {
        alert('Kích thước file không được vượt quá 2MB');
        input.value = '';
        return;
    }

    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImg.src = e.target.result;
        previewContainer.style.display = 'block';
    };
    reader.readAsDataURL(file);
}

/**
 * Clear selected image
 */
function clearCategoryImage() {
    const fileInput = document.getElementById('category_image');
    const previewContainer = document.getElementById('imagePreviewContainer');
    
    if (fileInput) fileInput.value = '';
    if (previewContainer) previewContainer.style.display = 'none';
}

// =================== DRAG & DROP HANDLING ===================

/**
 * Handle drag over
 */
function handleCategoryDragOver(event) {
    event.preventDefault();
    event.currentTarget.style.borderColor = 'var(--accent-color, #28a745)';
    event.currentTarget.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
}

/**
 * Handle drag leave
 */
function handleCategoryDragLeave(event) {
    event.currentTarget.style.borderColor = 'var(--border-color, #ddd)';
    event.currentTarget.style.backgroundColor = 'transparent';
}

/**
 * Handle drop
 */
function handleCategoryDrop(event) {
    event.preventDefault();
    event.currentTarget.style.borderColor = 'var(--border-color, #ddd)';
    event.currentTarget.style.backgroundColor = 'transparent';
    
    const files = event.dataTransfer.files;
    const fileInput = document.getElementById('category_image');
    
    if (files.length > 0) {
        fileInput.files = files;
        handleCategoryFileSelect(fileInput);
    }
}

// =================== UTILITY FUNCTIONS ===================

/**
 * Generate slug from category name
 */
function generateSlug(name) {
    return name
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

// =================== INITIALIZE ===================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Category Form Manager
    const categoryFormManager = new CategoryFormManager();
    
    console.log('Add Category page initialized - MVC/OOP compliant');
});
