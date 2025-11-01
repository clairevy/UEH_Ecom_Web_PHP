<?php 
// Include URL helper if not already included
if (!function_exists('url')) {
    require_once __DIR__ . '/../../../../helpers/url_helper.php';
}

// Include session helper for login checking
require_once __DIR__ . '/../../../../helpers/session_helper.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhẫn Kim Cương Vàng Trắng 18K - Chi Tiết Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <html xmlns:th="http://www.thymeleaf.org">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?= asset('css/css.css?v=' . time()) ?>" rel="stylesheet">  
    
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>
    
    <div class="main-content">
        <!-- Breadcrumb -->
        <div class="container" style="padding-top: 1rem;">
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
                    <?php if (isset($product->variant_options) && !empty($product->variant_options['colors'])): ?>
                    <div class="option-group">
                        <label>Loại:</label>
                        <div class="color-options type-options">
                            <?php foreach ($product->variant_options['colors'] as $index => $color): ?>
                                <button class="option-btn <?= $index === 0 ? 'active' : '' ?>" onclick="selectOption(this)">
                                    <?= htmlspecialchars($color) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($product->variant_options) && !empty($product->variant_options['sizes'])): ?>
                    <div class="option-group">
                        <label>Size:</label>
                        <div class="size-options type-options">
                            <?php foreach ($product->variant_options['sizes'] as $index => $size): ?>
                                <button class="option-btn <?= $index === 0 ? 'active' : '' ?>" onclick="selectOption(this)">
                                    <?= htmlspecialchars($size) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="quantity-selector">
                    <input type="number" class="quantity-input" value="1" min="1" id="quantityInput">
                    
                    <div class="action-buttons mt-3">
                        <button class="btn-add-to-cart me-3" onclick="addToCart()" id="addToCartBtn">
                            <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng
                        </button>
                        <button class="btn-buy" onclick="buyNow()" id="buyNowBtn">
                            <i class="fas fa-bolt me-2"></i>Mua Ngay
                        </button>
                    </div>
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
                    
                    <?php if (SessionHelper::isLoggedIn()): ?>
                        <!-- Form cho user đã đăng nhập -->
                        <div class="card">
                            <div class="card-body">
                                <form id="reviewForm" class="needs-validation" novalidate>
                                    <input type="hidden" id="productId" value="<?= $data['product']->product_id ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Đánh giá của bạn *</label>
                                        <div class="rating-input">
                                            <input type="radio" name="rating" value="5" id="star5" required>
                                            <label for="star5"><i class="fas fa-star"></i></label>
                                            <input type="radio" name="rating" value="4" id="star4" required>
                                            <label for="star4"><i class="fas fa-star"></i></label>
                                            <input type="radio" name="rating" value="3" id="star3" required>
                                            <label for="star3"><i class="fas fa-star"></i></label>
                                            <input type="radio" name="rating" value="2" id="star2" required>
                                            <label for="star2"><i class="fas fa-star"></i></label>
                                            <input type="radio" name="rating" value="1" id="star1" required>
                                            <label for="star1"><i class="fas fa-star"></i></label>
                                        </div>
                                        <div class="invalid-feedback">Vui lòng chọn số sao đánh giá.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="reviewTitle" class="form-label">Tiêu đề đánh giá *</label>
                                        <input type="text" class="form-control" id="reviewTitle" 
                                               placeholder="Nhập tiêu đề đánh giá..." maxlength="255" required>
                                        <div class="invalid-feedback">Vui lòng nhập tiêu đề đánh giá.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="reviewComment" class="form-label">Nhận xét của bạn *</label>
                                        <textarea class="form-control" id="reviewComment" rows="4" 
                                                  placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..." 
                                                  maxlength="1000" required></textarea>
                                        <div class="invalid-feedback">Vui lòng nhập nhận xét của bạn.</div>
                                        <small class="form-text text-muted">Tối đa 1000 ký tự</small>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Gửi đánh giá
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Thông báo cho khách vãng lai -->
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted mb-3">Đăng nhập để viết đánh giá</h5>
                                <p class="text-muted mb-4">Bạn cần đăng nhập tài khoản để có thể viết đánh giá về sản phẩm này.</p>
                                <a href="<?= url('/login') ?>" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập ngay
                                </a>
                                <a href="<?= url('/register') ?>" class="btn btn-outline-primary ms-2">
                                    <i class="fas fa-user-plus me-2"></i>Đăng ký tài khoản
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
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
                                            <button class="wishlist-btn" onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist(<?= $relatedProduct->product_id ?>);"><i class="far fa-heart"></i></button>
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


    </div> <!-- End main-content -->

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
<!-- Test if JS file loads -->
<script src="<?= asset('js/product-detail.js') ?>" defer></script>

<style>
/* Navigation Spacing Fix */
body {
    padding-top: 0; /* Reset any default padding */
}

.main-content {
    margin-top: 100px; /* Space for fixed navigation */
}

/* Responsive navigation spacing */
@media (max-width: 991px) {
    .main-content {
        margin-top: 80px; /* Less space on mobile */
    }
}

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

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn-add-to-cart {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    color: white;
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    flex: 1;
    min-width: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-add-to-cart:hover {
    background: linear-gradient(45deg, #218838, #1ba085);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    color: white;
}

.btn-add-to-cart:active {
    transform: translateY(0);
}

.btn-add-to-cart:disabled {
    background: #6c757d;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.btn-buy {
    background: linear-gradient(45deg, #dc3545, #fd7e14);
    border: none;
    color: white;
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    flex: 1;
    min-width: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-buy:hover {
    background: linear-gradient(45deg, #c82333, #fd6c14);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
    color: white;
}

@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-add-to-cart,
    .btn-buy {
        width: 100%;
        min-width: auto;
    }
}

/* Tab Content Visibility */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}
</style>

<script>
/**
 * Add product to cart
 */
function addToCart() {
    const productId = <?= isset($product->product_id) ? $product->product_id : 0 ?>;
    const quantity = parseInt(document.getElementById('quantityInput').value) || 1;
    const addToCartBtn = document.getElementById('addToCartBtn');
    
    // Get selected options
    const selectedColor = document.querySelector('.color-options .option-btn.active')?.textContent?.trim() || '';
    const selectedSize = document.querySelector('.size-options .option-btn.active')?.textContent?.trim() || '';
    
    // Debug logging
    console.log('Selected color:', selectedColor);
    console.log('Selected size:', selectedSize);
    console.log('Product ID:', productId);
    console.log('Quantity:', quantity);
    
    // Validate product
    if (!productId) {
        showToast('error', 'Không tìm thấy thông tin sản phẩm');
        return;
    }
    
    if (quantity < 1) {
        showToast('error', 'Số lượng phải lớn hơn 0');
        return;
    }

    // Show loading state
    const originalHtml = addToCartBtn.innerHTML;
    addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang thêm...';
    addToCartBtn.disabled = true;

    // Prepare form data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('color', selectedColor);
    formData.append('size', selectedSize);

    // Submit to cart
    const cartUrl = '<?= url('cart/add') ?>';
    console.log('Cart URL:', cartUrl);
    
    fetch(cartUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Restore button state
        addToCartBtn.innerHTML = originalHtml;
        addToCartBtn.disabled = false;

        if (data.success) {
            // Update cart badge if function exists
            if (typeof updateCartBadge === 'function') {
                updateCartBadge();
            }
            
            // Show success message with action buttons
            Swal.fire({
                icon: 'success',
                title: 'Thêm vào giỏ hàng thành công!',
                text: `Đã thêm ${quantity} sản phẩm vào giỏ hàng`,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-shopping-cart me-1"></i>Xem giỏ hàng',
                cancelButtonText: 'Tiếp tục mua sắm',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= url('cart') ?>';
                }
            });
        } else {
            showToast('error', data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
        }
    })
    .catch(error => {
        // Restore button state
        addToCartBtn.innerHTML = originalHtml;
        addToCartBtn.disabled = false;
        
        console.error('Error:', error);
        showToast('error', 'Có lỗi kết nối xảy ra. Vui lòng thử lại.');
    });
}

/**
 * Buy now - Add to cart and redirect to checkout
 */
function buyNow() {
    const productId = <?= isset($product->product_id) ? $product->product_id : 0 ?>;
    const quantity = parseInt(document.getElementById('quantityInput').value) || 1;
    
    // Get selected options
    const selectedColor = document.querySelector('.color-options .option-btn.active')?.textContent || '';
    const selectedSize = document.querySelector('.size-options .option-btn.active')?.textContent || '';
    
    console.log('BuyNow - Selected Color:', selectedColor);
    console.log('BuyNow - Selected Size:', selectedSize);
    
    // Validate product
    if (!productId) {
        showToast('error', 'Không tìm thấy thông tin sản phẩm');
        return;
    }
    
    if (quantity < 1) {
        showToast('error', 'Số lượng phải lớn hơn 0');
        return;
    }

    // Show loading
    Swal.fire({
        title: 'Đang xử lý...',
        text: 'Vui lòng đợi trong giây lát',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Prepare form data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('color', selectedColor);
    formData.append('size', selectedSize);

    // Use buyNow endpoint
    const buyNowUrl = '<?= url('cart/buynow') ?>';
    
    fetch(buyNowUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        console.log('BuyNow - Server Response:', data);
        
        if (data.success) {
            // Update cart badge
            if (typeof updateCartBadge === 'function') {
                updateCartBadge();
            }
            
            // Redirect to checkout
            console.log('BuyNow - Redirecting to checkout...');
            window.location.href = '<?= url('checkout') ?>';
        } else {
            console.log('BuyNow - Error:', data.message);
            showToast('error', data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        showToast('error', 'Có lỗi kết nối xảy ra. Vui lòng thử lại.');
    });
}

/**
 * Show toast notification
 */
function showToast(type, message) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: type,
        title: message
    });
}

/**
 * Select product option (color/size)
 */
function selectOption(element) {
    // Remove active class from siblings
    const siblings = element.parentElement.querySelectorAll('.option-btn');
    siblings.forEach(btn => btn.classList.remove('active'));
    
    // Add active class to clicked element
    element.classList.add('active');
}

// Initialize quantity controls
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantityInput');
    
    if (quantityInput) {
        quantityInput.addEventListener('change', function() {
            const value = parseInt(this.value);
            if (isNaN(value) || value < 1) {
                this.value = 1;
            }
        });
    }

    // Initialize review form handler
    initializeReviewForm();
});

/**
 * Initialize review form
 */
function initializeReviewForm() {
    const reviewForm = document.getElementById('reviewForm');
    if (!reviewForm) return;

    reviewForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Validate form
        if (!this.checkValidity()) {
            event.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        // Check if rating is selected
        const rating = this.querySelector('input[name="rating"]:checked');
        if (!rating) {
            showToast('error', 'Vui lòng chọn số sao đánh giá');
            return;
        }

        // Get form data
        const formData = new FormData();
        formData.append('product_id', document.getElementById('productId').value);
        formData.append('rating', rating.value);
        formData.append('title', document.getElementById('reviewTitle').value.trim());
        formData.append('comment', document.getElementById('reviewComment').value.trim());

        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';

        // Submit review
        fetch('<?= url('/api/reviews/add') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // Check if response is ok
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            // Check content type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.error('Non-JSON response:', text);
                    throw new Error('Server returned non-JSON response');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: data.message,
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reset form
                    reviewForm.reset();
                    reviewForm.classList.remove('was-validated');
                    
                    // Reset star rating display
                    const stars = reviewForm.querySelectorAll('.rating-input label');
                    stars.forEach(star => star.style.color = '#ddd');
                });
            } else {
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message || 'Có lỗi xảy ra khi gửi đánh giá',
                });
                
                // Handle redirect if needed (e.g., to login)
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                }
            }
        })
        .catch(error => {
            console.error('Review submission error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Lỗi kết nối',
                text: 'Không thể gửi đánh giá, vui lòng kiểm tra kết nối mạng.',
            });
        })
        .finally(() => {
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });

    // Handle star rating visual feedback
    const ratingInputs = reviewForm.querySelectorAll('input[name="rating"]');
    const ratingLabels = reviewForm.querySelectorAll('.rating-input label');
    
    ratingInputs.forEach((input, index) => {
        input.addEventListener('change', function() {
            const rating = parseInt(this.value);
            ratingLabels.forEach((label, labelIndex) => {
                if (labelIndex >= (5 - rating)) {
                    label.style.color = '#ffc107';
                } else {
                    label.style.color = '#ddd';
                }
            });
        });
    });
}

// Wishlist functions
async function toggleWishlist(productId) {
    const button = event.target.closest('button');
    const icon = button.querySelector('i');
    const wasInWishlist = icon.classList.contains('fas');
    
    try {
        // Temporarily update UI for immediate feedback
        if (wasInWishlist) {
            icon.classList.remove('fas');
            icon.classList.add('far');
            button.style.color = '';
        } else {
            icon.classList.remove('far');
            icon.classList.add('fas');
            button.style.color = '#dc3545';
        }
        
        // Call API
        const response = await fetch('/Ecom_website/wishlist/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `product_id=${productId}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update UI based on server response
            if (data.data.action === 'added') {
                icon.classList.remove('far');
                icon.classList.add('fas');
                button.style.color = '#dc3545';
                showToast('success', 'Đã thêm vào danh sách yêu thích!');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                button.style.color = '';
                showToast('info', 'Đã xóa khỏi danh sách yêu thích!');
            }
            
            // Update wishlist count in header
            updateWishlistCount(data.data.wishlist_count);
        } else {
            // Revert UI changes on error
            if (wasInWishlist) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                button.style.color = '#dc3545';
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                button.style.color = '';
            }
            
            if (data.message === 'Vui lòng đăng nhập để sử dụng danh sách yêu thích') {
                showToast('warning', data.message);
                setTimeout(() => {
                    window.location.href = '/Ecom_website/signin';
                }, 2000);
            } else {
                showToast('error', data.message || 'Có lỗi xảy ra!');
            }
        }
    } catch (error) {
        console.error('Wishlist toggle error:', error);
        // Revert UI changes on error
        if (wasInWishlist) {
            icon.classList.remove('far');
            icon.classList.add('fas');
            button.style.color = '#dc3545';
        } else {
            icon.classList.remove('fas');
            icon.classList.add('far');  
            button.style.color = '';
        }
        showToast('error', 'Lỗi kết nối!');
    }
}

function updateWishlistCount(count) {
    const wishlistBadge = document.querySelector('.wishlist-count');
    if (wishlistBadge) {
        wishlistBadge.textContent = count;
        wishlistBadge.style.display = count > 0 ? 'inline' : 'none';
    }
}

function showToast(type, message) {
    if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({
            icon: type,
            title: message
        });
    } else {
        alert(message);
    }
}
</script>

