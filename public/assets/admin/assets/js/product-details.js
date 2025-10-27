// Product Images Handler
document.addEventListener('DOMContentLoaded', function() {
    // Primary Image Upload
    const primaryImageInput = document.getElementById('primaryImageInput');
    if (primaryImageInput) {
        primaryImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ảnh vượt quá 5MB!');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('primaryImagePreview').src = event.target.result;
                };
                reader.readAsDataURL(file);
                
                console.log('Primary image uploaded:', file.name);
            }
        });
    }

    // Gallery Images Upload
    const galleryImagesInput = document.getElementById('galleryImagesInput');
    if (galleryImagesInput) {
        galleryImagesInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                Array.from(files).forEach(file => {
                    if (file.size > 5 * 1024 * 1024) {
                        alert(`File ${file.name} vượt quá 5MB!`);
                        return;
                    }
                    
                    // Add to gallery preview
                    addGalleryImagePreview(file);
                });
                
                console.log('Gallery images uploaded:', files.length, 'files');
            }
        });
    }
});

// Add Gallery Image Preview
function addGalleryImagePreview(file) {
    const reader = new FileReader();
    reader.onload = function(event) {
        const galleryPreview = document.getElementById('galleryPreview');
        const imageDiv = document.createElement('div');
        imageDiv.className = 'd-flex align-items-center justify-content-between mb-2 p-2 border rounded';
        imageDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <img src="${event.target.result}" alt="Gallery" width="40" height="40" class="me-2 rounded">
                <span class="small">${file.name}</span>
            </div>
            <button class="btn btn-sm btn-danger" onclick="removeGalleryImage(this)">
                <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="14" height="14">
            </button>
        `;
        galleryPreview.appendChild(imageDiv);
    };
    reader.readAsDataURL(file);
}

// Remove Gallery Image
function removeGalleryImage(btn) {
    if (confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
        btn.closest('.d-flex').remove();
    }
}
