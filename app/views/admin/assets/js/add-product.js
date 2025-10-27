/**
 * Add Product JavaScript - Client-side functionality
 * Tuân thủ nguyên tắc Separation of Concerns
 * - Chỉ xử lý UI/UX và client-side validation
 * - Business logic ở Server-side (Controller/Model)
 */

// Class để quản lý Product Form
class ProductFormManager {
    constructor() {
        this.form = document.getElementById('addProductForm');
        this.fileInput = document.getElementById('product_images');
        this.imagePreviewContainer = document.getElementById('imagePreviewContainer');
        this.imageList = document.getElementById('imageList');
        this.uploadArea = document.querySelector('.upload-area');
        this.selectedFiles = null;
        
        this.init();
    }

    init() {
        if (this.form) {
            // Form submit validation (client-side only, server sẽ validate lại)
            this.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }

        // Auto-generate SKU from product name
        this.setupSKUGenerator();
    }

    /**
     * Xử lý form submit - Client-side validation only
     */
    handleFormSubmit(e) {
        // Basic client-side validation
        const name = document.getElementById('name').value.trim();
        const basePrice = document.getElementById('base_price').value;
        const sku = document.getElementById('sku').value.trim();

        let errors = [];

        if (name.length < 3) {
            errors.push('Tên sản phẩm phải có ít nhất 3 ký tự');
        }

        if (!basePrice || parseFloat(basePrice) <= 0) {
            errors.push('Giá sản phẩm phải lớn hơn 0');
        }

        if (!sku) {
            errors.push('SKU không được để trống');
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
     * Setup auto SKU generator
     */
    setupSKUGenerator() {
        const nameInput = document.getElementById('name');
        const skuInput = document.getElementById('sku');

        if (nameInput && skuInput) {
            nameInput.addEventListener('input', function() {
                // Only generate if SKU is empty
                if (!skuInput.value) {
                    const name = this.value.trim();
                    if (name) {
                        const sku = generateSKU(name);
                        skuInput.value = sku;
                    }
                }
            });
        }
    }
}

// =================== FILE UPLOAD HANDLING ===================

/**
 * Handle file selection
 */
function handleFileSelect(input) {
    const files = input.files;
    const imageList = document.getElementById('imageList');
    const previewContainer = document.getElementById('imagePreviewContainer');
    
    if (!files || files.length === 0) {
        previewContainer.style.display = 'none';
        return;
    }

    // Clear previous previews
    imageList.innerHTML = '';
    previewContainer.style.display = 'block';

    // Preview each file
    Array.from(files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            // Validate file size (5MB)
            if (file.size > 5242880) {
                alert(`File ${file.name} quá lớn. Kích thước tối đa: 5MB`);
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const imageItem = createImagePreviewItem(e.target.result, file.name, index);
                imageList.appendChild(imageItem);
            };
            reader.readAsDataURL(file);
        }
    });
}

/**
 * Create image preview item
 */
function createImagePreviewItem(imageSrc, fileName, index) {
    const div = document.createElement('div');
    div.className = 'd-flex align-items-center justify-content-between mb-2 p-2 border rounded-custom';
    div.setAttribute('data-index', index);
    
    div.innerHTML = `
        <div class="d-flex align-items-center">
            <img src="${imageSrc}" alt="Preview" width="40" height="40" class="rounded me-2" style="object-fit: cover;">
            <div>
                <div class="small fw-bold">${truncateFileName(fileName, 20)}</div>
                ${index === 0 ? '<small class="text-primary">Ảnh chính</small>' : ''}
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImagePreview(this)">
            <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Remove" width="12" height="12">
        </button>
    `;
    
    return div;
}

/**
 * Remove image preview
 */
function removeImagePreview(button) {
    const imageItem = button.closest('.d-flex');
    const imageList = document.getElementById('imageList');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const fileInput = document.getElementById('product_images');
    
    imageItem.remove();
    
    // If no more images, hide preview container and clear file input
    if (imageList.children.length === 0) {
        previewContainer.style.display = 'none';
        fileInput.value = '';
    }
}

// =================== DRAG & DROP HANDLING ===================

/**
 * Handle drag over
 */
function handleDragOver(event) {
    event.preventDefault();
    event.currentTarget.style.borderColor = 'var(--accent-color, #28a745)';
    event.currentTarget.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
}

/**
 * Handle drag leave
 */
function handleDragLeave(event) {
    event.currentTarget.style.borderColor = 'var(--border-color, #ddd)';
    event.currentTarget.style.backgroundColor = 'transparent';
}

/**
 * Handle drop
 */
function handleDrop(event) {
    event.preventDefault();
    event.currentTarget.style.borderColor = 'var(--border-color, #ddd)';
    event.currentTarget.style.backgroundColor = 'transparent';
    
    const files = event.dataTransfer.files;
    const fileInput = document.getElementById('product_images');
    
    // Assign dropped files to file input
    fileInput.files = files;
    handleFileSelect(fileInput);
}

// =================== UTILITY FUNCTIONS ===================

/**
 * Generate SKU from product name
 */
function generateSKU(name) {
    // Remove Vietnamese accents
    const cleaned = name
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toUpperCase()
        .replace(/[^A-Z0-9]/g, '')
        .substring(0, 6);
    
    // Add random number
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    
    return cleaned + random;
}

/**
 * Truncate filename for display
 */
function truncateFileName(fileName, maxLength) {
    if (fileName.length <= maxLength) {
        return fileName;
    }
    
    const ext = fileName.split('.').pop();
    const nameWithoutExt = fileName.substring(0, fileName.length - ext.length - 1);
    const truncated = nameWithoutExt.substring(0, maxLength - ext.length - 4) + '...';
    
    return truncated + '.' + ext;
}

// =================== INITIALIZE ===================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Product Form Manager
    const productFormManager = new ProductFormManager();
    
    console.log('Add Product page initialized - MVC/OOP compliant');
});
