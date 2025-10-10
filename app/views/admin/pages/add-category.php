<?php
/**
 * Add Category View - Pure MVC View
 * Form để thêm danh mục mới
 */

// Biến được truyền từ Controller:
// $message - thông báo thành công
// $error - thông báo lỗi
?>

<!-- Add Category Form -->
<div class="table-card">
    <div class="table-header">
        <h5 class="table-title">Thêm Danh Mục Mới</h5>
        <div class="d-flex gap-2">
            <a href="index.php?url=categories" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    
    <div class="p-4">
        <?php if (isset($message) && $message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?url=categories" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên Danh Mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required 
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô Tả</label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Mô tả chi tiết về danh mục..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="icon_url" class="form-label">Icon URL</label>
                        <input type="url" class="form-control" id="icon_url" name="icon_url" 
                               placeholder="https://example.com/icon.png"
                               value="<?= htmlspecialchars($_POST['icon_url'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                   <?= isset($_POST['is_active']) ? 'checked' : 'checked' ?>>
                            <label class="form-check-label" for="is_active">
                                Kích hoạt danh mục
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="image" class="form-label">Hình Ảnh Danh Mục</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text">Chọn hình ảnh đại diện cho danh mục (JPG, PNG, GIF)</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <img id="imagePreview" src="https://via.placeholder.com/200x150?text=No+Image" 
                                     alt="Preview" class="img-fluid rounded" style="max-height: 150px;">
                                <p class="text-muted small mt-2">Preview hình ảnh</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Lưu Danh Mục
                </button>
                <a href="index.php?url=categories" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Image preview functionality
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    if (!name) {
        e.preventDefault();
        alert('Vui lòng nhập tên danh mục!');
        document.getElementById('name').focus();
    }
});
</script>
