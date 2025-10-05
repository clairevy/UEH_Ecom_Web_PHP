<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhẫn Kim Cương Vàng Trắng 18K - Chi Tiết Sản Phẩm</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Chi tiết sản phẩm</title>
    <link href="<?= asset('css/css.css?v=' . time()) ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">   
    
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header-nosignin.php'; ?>
    <!-- Breadcrumb -->
    <div class="container mt-5 pt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= route('products') ?>" class="text-decoration-none">Trang sức</a></li>
                <?php if (isset($product->categories) && !empty($product->categories)): ?>
                    <li class="breadcrumb-item"><a href="<?= route('products', ['category' => $product->categories[0]->category_id]) ?>" class="text-decoration-none"><?= htmlspecialchars($product->categories[0]->name) ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?= isset($product) ? htmlspecialchars($product->name) : 'Chi tiết sản phẩm' ?></li>
            </ol>
        </nav>
    </div>

    <!-- Product Detail Section -->
    <?php
    // Đảm bảo data được khởi tạo đúng
    $product = isset($data['product']) ? $data['product'] : null;
    $reviewStats = (!empty($product) && !empty($product->review_stats)) ? $product->review_stats : null;
    $reviews = isset($data['reviews']) ? $data['reviews'] : [];
  $relatedProducts = $data['relatedProducts'] ?? []; // Thêm dòng này

    ?>
    
    <div class="container">
        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-6">
                <div class="product-images">
                    <div class="row">
                        <div class="col-2">
                            <div class="thumbnail-container">
                                <?php if (isset($product->images) && !empty($product->images)): ?>
                                    <?php foreach ($product->images as $index => $image): ?>
                                        <div class="thumbnail-item <?= $index === 0 ? 'active' : '' ?>" onclick="changeImage(this, '<?= $image->file_path ?>')">
                                            <img src="<?= $image->file_path ?>" alt="<?= htmlspecialchars($image->alt_text ?: $product->name) ?>">
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Fallback images if no images in database -->
                                    <div class="thumbnail-item active" onclick="changeImage(this, 'https://images.unsplash.com/photo-1603561591411-07134e71a2a9?w=500')">
                                        <img src="https://images.unsplash.com/photo-1603561591411-07134e71a2a9?w=100" alt="Thumbnail 1">
                                    </div>
                                    <div class="thumbnail-item" onclick="changeImage(this, 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=500')">
                                        <img src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=100" alt="Thumbnail 2">
                                    </div>

                                    <div class="thumbnail-item" onclick="changeImage(this, 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=500')">
                                        <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=100" alt="Thumbnail 3">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-10" style="padding-left: 30px;">
                            <div class="main-image">
                                <img id="mainImage" 
                                     src="<?= isset($product->primary_image) ? $product->primary_image->file_path : (isset($product->images[0]) ? $product->images[0]->file_path : 'https://images.unsplash.com/photo-1603561591411-07134e71a2a9?w=500') ?>" 
                                     alt="<?= isset($product) ? htmlspecialchars($product->name) : 'Product Image' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6" style="padding-left: 30px;">
                <h1 class="product-title"><?= isset($product) ? htmlspecialchars($product->name) : 'Tên sản phẩm' ?></h1>
                
                <div class="rating-reviews d-flex align-items-center mb-3">
                    <div class="rating">
                        <?php 
                        $avgRating = ($reviewStats && isset($reviewStats->average_rating)) 
                            ? round($reviewStats->average_rating) 
                            : 5;
                        for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?= $i <= $avgRating ? 'fas' : 'far' ?> fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="text-muted">(<?= ($reviewStats && isset($reviewStats->total_reviews)) ? $reviewStats->total_reviews : 0 ?> đánh giá)</span>
                    <span class="badge bg-success ms-3">Còn hàng</span>
                </div>

                <div class="price"><?= isset($product) ? number_format($product->base_price, 0, ',', '.') : '0' ?> ₫</div>

                <p class="product-description text-muted">
                    <?= isset($product) ? htmlspecialchars($product->description) : 'Mô tả sản phẩm sẽ được hiển thị ở đây.' ?>
                </p>

                <div class="product-options">
                    <div class="option-group">
                        <label>Loại:</label>
                        <div class="type-options">
                            <button class="option-btn active" onclick="selectOption(this)">Vàng Trắng</button>
                            <button class="option-btn" onclick="selectOption(this)">Vàng Vàng</button>
                        </div>
                    </div>

                    <div class="option-group">
                        <label>Size:</label>
                        <div class="size-options">
                            <button class="option-btn" onclick="selectOption(this)">5</button>
                            <button class="option-btn" onclick="selectOption(this)">6</button>
                            <button class="option-btn active" onclick="selectOption(this)">7</button>
                            <button class="option-btn" onclick="selectOption(this)">8</button>
                            <button class="option-btn" onclick="selectOption(this)">9</button>
                        </div>
                    </div>
                </div>

                <div class="quantity-selector">
                    
                    <input type="number" class="quantity-input" value="1" min="1" id="quantityInput">
                    
                    <button class="btn-buy ms-3">Mua Ngay</button>
                </div>

                <div class="delivery-info">
                    <div class="delivery-item">
                        <i class="fas fa-shipping-fast delivery-icon"></i>
                        <div>
                            <strong>Miễn phí vận chuyển</strong><br>
                            <small class="text-muted">Nhập mã bưu điện để kiểm tra tình trạng giao hàng</small>
                        </div>
                    </div>
                    <div class="delivery-item">
                        <i class="fas fa-undo delivery-icon"></i>
                        <div>
                            <strong>Đổi trả miễn phí</strong><br>
                            <small class="text-muted">Đổi trả miễn phí trong 30 ngày. Chi tiết chính sách đổi trả</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="container">
        <div class="product-tabs">
            <div class="tab-navigation">
                <button class="tab-btn active" onclick="switchTab(this, 'description')">Mô tả sản phẩm</button>
                <button class="tab-btn" onclick="switchTab(this, 'additional')">Thông tin bổ sung</button>
                <button class="tab-btn" onclick="switchTab(this, 'reviews')">Đánh giá (<?= ($reviewStats && isset($reviewStats->total_reviews)) ? $reviewStats->total_reviews : 0 ?>)</button>
            </div>

            <div class="tab-content active" id="description">
                <div class="row">
                    <div class="col-md-8">
                    
                        <?php
                        if (isset($product) && !empty($product->description)) {
                            echo '<h4>Mô tả chi tiết sản phẩm</h4>';
                            echo '<p>' . htmlspecialchars($product->description) . '</p>';
                        } else {
                            echo '<h4>Mô tả chi tiết sản phẩm</h4>';
                            echo '<p>Thông tin mô tả sẽ được cập nhật sau.</p>';
                        }?>
                    </div>
                    <div class="col-md-4">
                        <div class="inf-images">
                            <img src="<?php echo (isset($product) && isset($product->images[0])) ? htmlspecialchars($product->images[0]->file_path) : ''; ?>" alt="Chi tiết ">
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="additional">
                <h4>Thông tin kỹ thuật</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td><strong>Chất liệu</strong></td>
                                <td>Vàng trắng 18K (75% vàng, 25% kim loại khác)</td>
                            </tr>
                            <tr>
                                <td><strong>Trọng lượng vàng</strong></td>
                                <td>8.5 gram</td>
                            </tr>
                            <tr>
                                <td><strong>Kim cương chính</strong></td>
                                <td>1.2 carat, IF, màu D, giác cắt Round Brilliant</td>
                            </tr>
                            <tr>
                                <td><strong>Kim cương phụ</strong></td>
                                <td>24 viên, tổng 0.5 carat, VS1-VS2, màu E-F</td>
                            </tr>
                            <tr>
                                <td><strong>Size có sẵn</strong></td>
                                <td>5, 6, 7, 8, 9 (có thể điều chỉnh theo yêu cầu)</td>
                            </tr>
                            <tr>
                                <td><strong>Xuất xứ</strong></td>
                                <td>Việt Nam</td>
                            </tr>
                            <tr>
                                <td><strong>Chứng nhận</strong></td>
                                <td>GIA Certificate, PNJ Warranty</td>
                            </tr>
                            <tr>
                                <td><strong>Bảo hành</strong></td>
                                <td>2 năm toàn diện</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-content" id="reviews">
                <h4>Đánh giá từ khách hàng</h4>
                <?php if ($reviewStats && $reviewStats->total_reviews > 0): ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h2 class="display-4"><?= number_format($reviewStats->average_rating, 1) ?></h2>
                            <div class="rating mb-2">
                                <?php 
                                // require_once __DIR__ . '/../../services/ReviewService.php';
                                $reviewService = new ReviewService();
                                echo $reviewService->renderStars($reviewStats->average_rating);
                                ?>
                            </div>
                            <p class="text-muted">Dựa trên <?= $reviewStats->total_reviews ?> đánh giá</p>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <?php if (!empty($data['product']->reviews)): ?>
                            <?php foreach ($data['product']->reviews as $index => $review): ?>
                            <div class="review-item <?= $index < count($data['product']->reviews) - 1 ? 'border-bottom pb-3 mb-3' : '' ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($review->reviewer_name) ?></h6>
                                        <?php if (!empty($review->title)): ?>
                                        <p class="fw-bold text-dark mb-1">"<?= htmlspecialchars($review->title) ?>"</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="rating">
                                        <?= $reviewService->renderStars($review->rating) ?>
                                    </div>
                                </div>
                                <p class="mb-1"><?= htmlspecialchars($review->comment) ?></p>
                                <small class="text-muted"><?= $reviewService->formatReviewTime($review->created_at) ?></small>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Chưa có đánh giá nào.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có đánh giá nào</h5>
                    <p class="text-muted">Hãy là người đầu tiên đánh giá sản phẩm này!</p>
                </div>
                <?php endif; ?>
                
                <!-- Add Review Form -->
                <div class="mt-5">
                    <h5>Viết đánh giá của bạn</h5>
                    <div class="card">
                        <div class="card-body">
                            <form id="reviewForm" class="needs-validation" novalidate>
                                <input type="hidden" id="productId" value="<?= $data['product']->product_id ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Đánh giá của bạn *</label>
                                    <div class="rating-input">
                                        <input type="radio" name="rating" value="5" id="star5">
                                        <label for="star5"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="4" id="star4">
                                        <label for="star4"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="3" id="star3">
                                        <label for="star3"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="2" id="star2">
                                        <label for="star2"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="1" id="star1">
                                        <label for="star1"><i class="fas fa-star"></i></label>
                                    </div>
                                    <div class="invalid-feedback">Vui lòng chọn số sao đánh giá.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="reviewTitle" class="form-label">Tiêu đề đánh giá *</label>
                                    <input type="text" class="form-control" id="reviewTitle" 
                                           placeholder="Nhập tiêu đề đánh giá..." maxlength="200" required>
                                    <div class="invalid-feedback">Vui lòng nhập tiêu đề đánh giá.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="reviewComment" class="form-label">Nhận xét của bạn *</label>
                                    <textarea class="form-control" id="reviewComment" rows="4" 
                                              placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..." required></textarea>
                                    <div class="invalid-feedback">Vui lòng nhập nhận xét của bạn.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="reviewerName" class="form-label">Tên của bạn *</label>
                                    <input type="text" class="form-control" id="reviewerName" 
                                           placeholder="Nhập tên của bạn..." required>
                                    <div class="invalid-feedback">Vui lòng nhập tên của bạn.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="reviewerEmail" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="reviewerEmail" 
                                           placeholder="Nhập email của bạn..." required>
                                    <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi đánh giá
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (isset($relatedProducts) && !empty($relatedProducts)): ?>
    <div class="container">
        <div class="related-products">
            <h3 class="mb-4">Sản phẩm liên quan</h3>
            <div class="row">
                        <?php foreach ($relatedProducts as $relatedProduct): ?>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="related-product-card">
                                    <a href="<?= route('product/' . ($relatedProduct->slug ?? $relatedProduct->product_id)) ?>" style="text-decoration: none; color: inherit; display: block;">
                                        <div class="product-image">
                                            <img src="<?= $relatedProduct->primary_image ? $relatedProduct->primary_image->file_path : 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=300' ?>" 
                                                 alt="<?= htmlspecialchars($relatedProduct->name) ?>">
                                            <button class="wishlist-btn" onclick="event.preventDefault(); event.stopPropagation();"><i class="far fa-heart"></i></button>
                                            <button class="compare-btn" onclick="event.preventDefault(); event.stopPropagation();"><i class="fas fa-eye"></i></button>
                                            <button class="add-to-cart-overlay" onclick="event.preventDefault(); event.stopPropagation();">Thêm vào giỏ</button>
                                        </div>
                                        <h6 class="mt-3"><?= htmlspecialchars($relatedProduct->name) ?></h6>
                                        <div class="rating mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star"></i>
                                            <?php endfor; ?>
                                            <small class="text-muted ms-1">(<?= rand(50, 200) ?>)</small>
                                        </div>
                                        <div class="product-price">
                                            <span><?= number_format($relatedProduct->base_price, 0, ',', '.') ?> ₫</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>


<!-- Footer -->
<footer class="bg-dark text-white py-5">
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <h5>Exclusive</h5>
            <ul class="list-unstyled">
                <li><a href="#" class="text-light">Collections</a></li>
                <li><a href="#" class="text-light">New Arrivals</a></li>
                <li><a href="#" class="text-light">Best Sellers</a></li>
            </ul>
        </div>
        <div class="col-md-3">
            <h5>Support</h5>
            <ul class="list-unstyled">
                <li><a href="#" class="text-light">Customer Service</a></li>
                <li><a href="#" class="text-light">Size Guide</a></li>
                <li><a href="#" class="text-light">Care Instructions</a></li>
                <li><a href="#" class="text-light">Returns & Exchanges</a></li>
            </ul>
        </div>
        <div class="col-md-3">
            <h5>Account</h5>
            <ul class="list-unstyled">
                <li><a href="#" class="text-light">My Account</a></li>
                <li><a href="#" class="text-light">Order History</a></li>
                <li><a href="#" class="text-light">Wishlist</a></li>
                <li><a href="#" class="text-light">Newsletter</a></li>
            </ul>
        </div>
        <div class="col-md-3">
            <h5>Contact us</h5>
            <p class="text-light"></p>
            <div class="social-icons">
                <i class="fab fa-facebook me-2"></i>
                <i class="fab fa-twitter me-2"></i>
                <i class="fab fa-instagram me-2"></i>
                <i class="fab fa-linkedin"></i>
            </div>
        </div>
    </div>
</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<!-- Test if JS file loads -->
<script>
console.log('🧪 Testing JS file loading...');
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
console.log('🧪 Testing JS file loading...');
</script>
<!-- Test if JS file loads -->
<script src="<?= asset('js/product-detail.js') ?>" defer></script>

<style>
/* Rating Input Styles */
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input label {
    color: #ddd;
    font-size: 1.5rem;
    cursor: pointer;
    margin-right: 5px;
    transition: color 0.2s;
}

.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input[type="radio"]:checked ~ label {
    color: #ffc107;
}

.rating-input label:hover {
    transform: scale(1.1);
}

/* Review Cards */
.review-item {
    transition: background-color 0.2s;
    padding: 15px;
    border-radius: 8px;
}

.review-item:hover {
    background-color: #f8f9fa;
}

/* Tab Content Visibility */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}
</style>

