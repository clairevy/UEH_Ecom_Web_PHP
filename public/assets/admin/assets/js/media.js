/**
 * Media Library Page - JavaScript
 * Handles file upload, preview, and media management
 */

// Global variables
let selectedFiles = [];

// Initialize page when DOM is ready
if (typeof ComponentManager !== 'undefined' && ComponentManager.init) {
    ComponentManager.init().then(() => {
        initializeMediaPage();
    }).catch(err => {
        console.error('ComponentManager init failed:', err);
        initializeMediaPage();
    });
} else {
    document.addEventListener('DOMContentLoaded', function() {
        initializeMediaPage();
    });
}

/**
 * Initialize all media page functionality
 */
function initializeMediaPage() {
    console.log('Initializing Media Library Page...');
    
    initializeUploadZone();
    initializeViewModeToggle();
    initializeFilters();
    initializeCheckboxes();
}

/**
 * Initialize upload zone with drag & drop and click functionality
 */
function initializeUploadZone() {
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    
    if (!uploadZone || !fileInput) {
        console.error('Upload zone elements not found');
        return;
    }
    
    // Click to upload
    uploadZone.addEventListener('click', function(e) {
        e.preventDefault();
        fileInput.click();
    });
    
    // Drag over
    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadZone.classList.add('dragover');
    });
    
    // Drag leave
    uploadZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadZone.classList.remove('dragover');
    });
    
    // Drop
    uploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadZone.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFiles(files);
        }
    });
    
    // File input change
    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleFiles(e.target.files);
        }
    });
    
    console.log('Upload zone initialized');
}

/**
 * Handle selected files - create preview
 */
function handleFiles(files) {
    const purpose = document.getElementById('uploadPurpose').value;
    
    if (!purpose) {
        alert('Vui lòng chọn mục đích upload trước!');
        document.getElementById('fileInput').value = '';
        return;
    }
    
    console.log('Files selected:', files.length);
    
    const filesPreviewSection = document.getElementById('filesPreviewSection');
    const filesPreviewGrid = document.getElementById('filesPreviewGrid');
    
    // Clear previous previews
    selectedFiles = [];
    filesPreviewGrid.innerHTML = '';
    
    let validFileCount = 0;
    
    Array.from(files).forEach((file, index) => {
        // Validate file size (50MB max)
        if (file.size > 50 * 1024 * 1024) {
            alert(`File ${file.name} vượt quá 50MB!`);
            return;
        }
        
        // Validate file type
        const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
        const validVideoTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/quicktime', 'video/x-msvideo'];
        const allValidTypes = [...validImageTypes, ...validVideoTypes];
        
        if (!allValidTypes.includes(file.type)) {
            alert(`File ${file.name} không đúng định dạng cho phép!`);
            return;
        }
        
        const fileId = Date.now() + index;
        
        // Add to selected files array
        selectedFiles.push({
            file: file,
            id: fileId,
            purpose: purpose
        });
        
        validFileCount++;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewCol = document.createElement('div');
            previewCol.className = 'col-lg-3 col-md-4 col-sm-6';
            previewCol.setAttribute('data-file-id', fileId);
            
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
            const isVideo = validVideoTypes.includes(file.type);
            
            let previewContent = '';
            
            if (isVideo) {
                previewContent = `
                    <div class="card h-100">
                        <div class="position-relative">
                            <video class="card-img-top" style="height: 200px; object-fit: cover;" controls>
                                <source src="${e.target.result}" type="${file.type}">
                                Your browser does not support video.
                            </video>
                            <span class="position-absolute top-0 end-0 badge bg-danger m-2">VIDEO</span>
                        </div>
                        <div class="card-body p-2">
                            <h6 class="card-title small fw-bold text-truncate mb-1" title="${file.name}">${file.name}</h6>
                            <p class="card-text small text-muted mb-2">
                                📹 ${fileSize} MB
                            </p>
                            <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeFilePreview(${fileId})">
                                <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Remove" width="14" height="14" class="me-1">
                                Xóa
                            </button>
                        </div>
                    </div>
                `;
            } else {
                previewContent = `
                    <div class="card h-100">
                        <div class="position-relative">
                            <img src="${e.target.result}" class="card-img-top" alt="${file.name}" style="height: 200px; object-fit: cover;">
                            <span class="position-absolute top-0 end-0 badge bg-info m-2">IMAGE</span>
                        </div>
                        <div class="card-body p-2">
                            <h6 class="card-title small fw-bold text-truncate mb-1" title="${file.name}">${file.name}</h6>
                            <p class="card-text small text-muted mb-2">
                                🖼️ ${fileSize} MB
                            </p>
                            <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeFilePreview(${fileId})">
                                <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Remove" width="14" height="14" class="me-1">
                                Xóa
                            </button>
                        </div>
                    </div>
                `;
            }
            
            previewCol.innerHTML = previewContent;
            filesPreviewGrid.appendChild(previewCol);
        };
        
        reader.readAsDataURL(file);
    });
    
    // Show preview section and update counts
    if (validFileCount > 0) {
        filesPreviewSection.style.display = 'block';
        document.getElementById('fileCount').textContent = validFileCount;
        document.getElementById('saveFileCount').textContent = validFileCount;
    }
}

/**
 * Remove individual file from preview
 */
function removeFilePreview(fileId) {
    // Remove from array
    selectedFiles = selectedFiles.filter(f => f.id !== fileId);
    
    // Remove from DOM
    const previewCol = document.querySelector(`[data-file-id="${fileId}"]`);
    if (previewCol) {
        previewCol.remove();
    }
    
    // Update counts
    document.getElementById('fileCount').textContent = selectedFiles.length;
    document.getElementById('saveFileCount').textContent = selectedFiles.length;
    
    // Hide section if no files
    if (selectedFiles.length === 0) {
        clearAllFiles();
    }
}

/**
 * Clear all selected files
 */
function clearAllFiles() {
    selectedFiles = [];
    document.getElementById('filesPreviewSection').style.display = 'none';
    document.getElementById('filesPreviewGrid').innerHTML = '';
    document.getElementById('fileInput').value = '';
    document.getElementById('fileCount').textContent = '0';
    document.getElementById('saveFileCount').textContent = '0';
}

/**
 * Save all files to media library
 */
function saveAllFiles() {
    if (selectedFiles.length === 0) {
        alert('Không có file nào để lưu!');
        return;
    }
    
    const purpose = document.getElementById('uploadPurpose').value;
    const note = document.getElementById('uploadNote').value;
    const mediaContainer = document.getElementById('mediaContainer');
    
    console.log('Saving files:', selectedFiles.length, 'Purpose:', purpose, 'Note:', note);
    
    // Add loading state
    const saveBtn = event.target;
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<span class="loading-spinner me-2"></span>Đang lưu...';
    
    selectedFiles.forEach(fileObj => {
        const file = fileObj.file;
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const mediaItem = document.createElement('div');
            mediaItem.className = 'media-item fade-in';
            mediaItem.setAttribute('data-type', file.type.startsWith('image/') ? 'image' : 'video');
            mediaItem.setAttribute('data-date', new Date().toISOString().split('T')[0]);
            
            const today = new Date();
            const formattedDate = today.toLocaleDateString('vi-VN');
            const fileSize = (file.size / 1024).toFixed(0);
            const newId = fileObj.id;
            
            if (file.type.startsWith('image/')) {
                mediaItem.innerHTML = `
                    <input type="checkbox" class="form-check-input media-item-checkbox">
                    <img src="${e.target.result}" alt="${file.name}">
                    <div class="media-item-actions">
                        <button class="btn btn-sm btn-primary" onclick="viewMedia(${newId})" title="Xem">
                            <img src="https://cdn-icons-png.flaticon.com/512/709/709612.png" alt="View" width="14" height="14">
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteMedia(${newId})" title="Xóa">
                            <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="14" height="14">
                        </button>
                    </div>
                    <div class="media-item-info">
                        <div class="fw-bold small">${file.name}</div>
                        <div class="text-muted" style="font-size: 11px;">${fileSize} KB • ${formattedDate}</div>
                        <div class="badge bg-info mt-1" style="font-size: 10px;">${purpose.toUpperCase()}</div>
                    </div>
                `;
            } else {
                mediaItem.innerHTML = `
                    <input type="checkbox" class="form-check-input media-item-checkbox">
                    <video style="width: 100%; height: 200px; object-fit: cover;" controls>
                        <source src="${e.target.result}" type="${file.type}">
                    </video>
                    <div class="media-item-actions">
                        <button class="btn btn-sm btn-primary" onclick="viewMedia(${newId})" title="Xem">
                            <img src="https://cdn-icons-png.flaticon.com/512/709/709612.png" alt="View" width="14" height="14">
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteMedia(${newId})" title="Xóa">
                            <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="14" height="14">
                        </button>
                    </div>
                    <div class="media-item-info">
                        <div class="fw-bold small">${file.name}</div>
                        <div class="text-muted" style="font-size: 11px;">${fileSize} KB • ${formattedDate}</div>
                        <div class="badge bg-danger mt-1" style="font-size: 10px;">VIDEO • ${purpose.toUpperCase()}</div>
                    </div>
                `;
            }
            
            // Insert at the beginning
            mediaContainer.insertBefore(mediaItem, mediaContainer.firstChild);
            
            // Add checkbox event listener
            const checkbox = mediaItem.querySelector('.media-item-checkbox');
            checkbox.addEventListener('change', updateBulkActions);
        };
        
        reader.readAsDataURL(file);
    });
    
    // Show success message and clear preview
    setTimeout(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
        
        alert(`Đã lưu ${selectedFiles.length} file thành công vào thư viện media!`);
        clearAllFiles();
        
        // Reset purpose and note
        document.getElementById('uploadPurpose').value = '';
        document.getElementById('uploadNote').value = '';
    }, 1000);
}

/**
 * Initialize view mode toggle (Grid/List)
 */
function initializeViewModeToggle() {
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const mediaContainer = document.getElementById('mediaContainer');
    
    if (!gridViewBtn || !listViewBtn || !mediaContainer) return;
    
    gridViewBtn.addEventListener('click', () => {
        mediaContainer.classList.remove('media-list-view');
        mediaContainer.classList.add('media-grid');
        gridViewBtn.classList.add('active');
        listViewBtn.classList.remove('active');
    });
    
    listViewBtn.addEventListener('click', () => {
        mediaContainer.classList.remove('media-grid');
        mediaContainer.classList.add('media-list-view');
        listViewBtn.classList.add('active');
        gridViewBtn.classList.remove('active');
    });
}

/**
 * Initialize filters and search
 */
function initializeFilters() {
    const searchInput = document.getElementById('mediaSearch');
    const fileTypeFilter = document.getElementById('fileTypeFilter');
    const dateFilter = document.getElementById('dateFilter');
    const sortFilter = document.getElementById('sortFilter');
    
    if (searchInput) searchInput.addEventListener('input', filterMedia);
    if (fileTypeFilter) fileTypeFilter.addEventListener('change', filterMedia);
    if (dateFilter) dateFilter.addEventListener('change', filterMedia);
    if (sortFilter) sortFilter.addEventListener('change', sortMedia);
}

/**
 * Filter media items
 */
function filterMedia() {
    const searchTerm = document.getElementById('mediaSearch').value.toLowerCase();
    const typeFilter = document.getElementById('fileTypeFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    const mediaItems = document.querySelectorAll('.media-item');
    
    mediaItems.forEach(item => {
        const fileName = item.querySelector('.fw-bold')?.textContent.toLowerCase() || '';
        const itemType = item.getAttribute('data-type');
        const itemDate = new Date(item.getAttribute('data-date'));
        const today = new Date();
        
        let showItem = true;
        
        // Search filter
        if (searchTerm && !fileName.includes(searchTerm)) {
            showItem = false;
        }
        
        // Type filter
        if (typeFilter !== 'all' && itemType !== typeFilter) {
            showItem = false;
        }
        
        // Date filter
        if (dateFilter === 'today') {
            const isToday = itemDate.toDateString() === today.toDateString();
            if (!isToday) showItem = false;
        } else if (dateFilter === 'week') {
            const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            if (itemDate < weekAgo) showItem = false;
        } else if (dateFilter === 'month') {
            const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
            if (itemDate < monthAgo) showItem = false;
        }
        
        item.style.display = showItem ? '' : 'none';
    });
}

/**
 * Sort media items
 */
function sortMedia() {
    const sortBy = document.getElementById('sortFilter').value;
    const container = document.getElementById('mediaContainer');
    const items = Array.from(container.children);
    
    items.sort((a, b) => {
        if (sortBy === 'newest') {
            return new Date(b.getAttribute('data-date')) - new Date(a.getAttribute('data-date'));
        } else if (sortBy === 'oldest') {
            return new Date(a.getAttribute('data-date')) - new Date(b.getAttribute('data-date'));
        } else if (sortBy === 'name') {
            const nameA = a.querySelector('.fw-bold')?.textContent || '';
            const nameB = b.querySelector('.fw-bold')?.textContent || '';
            return nameA.localeCompare(nameB);
        }
        return 0;
    });
    
    items.forEach(item => container.appendChild(item));
}

/**
 * Initialize checkbox functionality
 */
function initializeCheckboxes() {
    const checkboxes = document.querySelectorAll('.media-item-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
}

/**
 * Update bulk action buttons state
 */
function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.media-item-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (bulkDeleteBtn) {
        bulkDeleteBtn.disabled = checkedBoxes.length === 0;
    }
}

/**
 * View media item (modal or new tab)
 */
function viewMedia(id) {
    console.log('Viewing media:', id);
    // TODO: Implement modal view or open in new tab
    alert('Xem chi tiết media #' + id);
}

/**
 * Delete single media item
 */
function deleteMedia(id) {
    if (confirm('Bạn có chắc chắn muốn xóa file này?')) {
        console.log('Deleting media:', id);
        // TODO: Implement actual delete
        const mediaItem = document.querySelector(`[onclick="deleteMedia(${id})"]`)?.closest('.media-item');
        if (mediaItem) {
            mediaItem.remove();
            alert('Đã xóa file thành công!');
        }
    }
}

/**
 * Bulk delete selected media items
 */
function bulkDeleteMedia() {
    const checkedBoxes = document.querySelectorAll('.media-item-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('Vui lòng chọn ít nhất một file để xóa!');
        return;
    }
    
    if (confirm(`Bạn có chắc chắn muốn xóa ${checkedBoxes.length} file đã chọn?`)) {
        console.log('Bulk deleting:', checkedBoxes.length, 'items');
        
        checkedBoxes.forEach(checkbox => {
            const mediaItem = checkbox.closest('.media-item');
            if (mediaItem) {
                mediaItem.remove();
            }
        });
        
        alert(`Đã xóa ${checkedBoxes.length} file thành công!`);
        updateBulkActions();
    }
}

// Export functions for global access
window.removeFilePreview = removeFilePreview;
window.clearAllFiles = clearAllFiles;
window.saveAllFiles = saveAllFiles;
window.viewMedia = viewMedia;
window.deleteMedia = deleteMedia;
window.bulkDeleteMedia = bulkDeleteMedia;

console.log('Media Library JavaScript loaded');
