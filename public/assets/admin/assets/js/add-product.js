// Drag and drop handling
function handleDragOver(event) {
    event.preventDefault();
    event.currentTarget.style.borderColor = 'var(--accent-color)';
    event.currentTarget.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
}

function handleDragLeave(event) {
    event.currentTarget.style.borderColor = 'var(--border-color)';
    event.currentTarget.style.backgroundColor = 'transparent';
}

function handleDrop(event) {
    event.preventDefault();
    event.currentTarget.style.borderColor = 'var(--border-color)';
    event.currentTarget.style.backgroundColor = 'transparent';
    
    const files = event.dataTransfer.files;
    const fileInput = document.getElementById('fileInput');
    fileInput.files = files;
    handleFileSelect(fileInput);
}

// File upload handling
function handleFileSelect(input) {
    const files = input.files;
    const imageList = document.getElementById('imageList');
    const previewContainer = document.getElementById('imagePreviewContainer');
    
    if (files.length > 0) {
        Array.from(files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageItem = document.createElement('div');
                    imageItem.className = 'd-flex align-items-center justify-content-between p-2 border rounded mb-2';
                    imageItem.innerHTML = `
                        <div class="d-flex align-items-center">
                            <img src="${e.target.result}" alt="Preview" width="40" height="40" class="rounded me-2">
                            <span class="small">${file.name}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeImage(this)">
                            <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="14" height="14">
                        </button>
                    `;
                    imageList.appendChild(imageItem);
                };
                reader.readAsDataURL(file);
            }
        });
        previewContainer.style.display = 'block';
    }
}

// Remove image
function removeImage(button) {
    button.closest('.d-flex').remove();
    const imageList = document.getElementById('imageList');
    if (imageList.children.length === 0) {
        document.getElementById('imagePreviewContainer').style.display = 'none';
    }
}

// SKU generation
document.addEventListener('DOMContentLoaded', function() {
    const productNameInput = document.getElementById('productName');
    const skuInput = document.getElementById('productSKU');
    
    if (productNameInput && skuInput) {
        productNameInput.addEventListener('input', function() {
            const name = this.value.trim();
            if (name && !skuInput.value) {
                const sku = generateSKU(name);
                skuInput.value = sku;
            }
        });
    }
});

// Generate SKU from product name
function generateSKU(name) {
    const cleaned = name
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toUpperCase()
        .replace(/[^A-Z0-9]/g, '')
        .substring(0, 8);
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    return cleaned + random;
}

// Save draft
function saveDraft() {
    console.log('Saving draft...');
    alert('Đã lưu nháp sản phẩm!');
}

// Form submission
document.addEventListener('DOMContentLoaded', function() {
    const addProductForm = document.getElementById('addProductForm');
    if (addProductForm) {
        addProductForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (confirm('Bạn có chắc chắn muốn thêm sản phẩm này?')) {
                console.log('Adding product...');
                alert('Đã thêm sản phẩm thành công!');
                window.location.href = 'products.html';
            }
        });
    }
});
