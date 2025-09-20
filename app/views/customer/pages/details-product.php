<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhẫn Kim Cương Vàng Trắng 18K - Chi Tiết Sản Phẩm</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/app/assets/css.css">
    
    <style>
        .related-product-card a:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
        .related-product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .related-product-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fas fa-gem me-2"></i>JEWELRY</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#category">Category</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#collection">Collection</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <!-- Search -->
                <div class="search-container me-3">
                    <input type="text" class="search-input" placeholder="What are you looking for?">
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <!-- Auth Links -->
                <div class="me-3">
                    <a href="#signin" class="text-decoration-none me-3" style="color: #666;">Sign in</a>
                    <!-- <a href="#signup" class="text-decoration-none" style="color: #666;">Sign up</a> -->
                </div>
                
                <!-- Icons -->
                <div class="nav-icons">
                    <i class="far fa-heart"></i>
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
        </div>
    </div>
</nav>
    <!-- Breadcrumb -->
    <div class="container mt-5 pt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/Ecom_website/customer" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/Ecom_website/customer/products" class="text-decoration-none">Trang sức</a></li>
                <?php if (isset($productCategories) && !empty($productCategories)): ?>
                    <li class="breadcrumb-item"><a href="/Ecom_website/customer/products?category=<?= $productCategories[0]->category_id ?>" class="text-decoration-none"><?= htmlspecialchars($productCategories[0]->name) ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?= isset($product) ? htmlspecialchars($product->name) : 'Chi tiết sản phẩm' ?></li>
            </ol>
        </nav>
    </div>

    <!-- Product Detail Section -->
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
                        $avgRating = isset($reviewStats->average_rating) ? round($reviewStats->average_rating) : 5;
                        for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?= $i <= $avgRating ? 'fas' : 'far' ?> fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="text-muted">(<?= isset($reviewStats->total_reviews) ? $reviewStats->total_reviews : 0 ?> đánh giá)</span>
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
                <button class="tab-btn" onclick="switchTab(this, 'reviews')">Đánh giá (150)</button>
            </div>

            <div class="tab-content active" id="description">
                <div class="row">
                    <div class="col-md-8">
                        <h4>Mô tả chi tiết sản phẩm</h4>
                        <p>Nhẫn kim cương vàng trắng 18K là sự kết hợp hoàn hảo giữa vẻ đẹp cổ điển và phong cách hiện đại. Được chế tác từ vàng trắng 18K cao cấp với độ tinh khiết tuyệt đối, chiếc nhẫn này mang đến vẻ đẹp lộng lẫy và sang trọng.</p>
                        
                        <p>Kim cương chính ở trung tâm có trọng lượng 1.2 carat với độ tinh khiết IF (Internally Flawless) và màu sắc D (không màu), tạo nên sự rực rỡ và lấp lánh đặc biệt. Xung quanh kim cương chính là 24 viên kim cương nhỏ được sắp xếp tinh tế, tăng thêm vẻ rực rỡ cho toàn bộ thiết kế.</p>
                        
                        <h5>Đặc điểm nổi bật:</h5>
                        <ul>
                            <li>Chất liệu: Vàng trắng 18K</li>
                            <li>Kim cương chính: 1.2 carat, độ tinh khiết IF, màu D</li>
                            <li>Kim cương phụ: 24 viên, tổng trọng lượng 0.5 carat</li>
                            <li>Thiết kế: Cổ điển kết hợp hiện đại</li>
                            <li>Bảo hành: 2 năm toàn diện</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <div class="inf-images">
                            <img src="https://images.unsplash.com/photo-1603561591411-07134e71a2a9?w=400" alt="Chi tiết nhẫn">
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
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h2 class="display-4">4.8</h2>
                            <div class="rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="text-muted">Dựa trên 150 đánh giá</p>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="review-item border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <h6>Minh Anh</h6>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <p class="mb-1">"Nhẫn rất đẹp, kim cương lấp lánh, chất lượng tuyệt vời. Giao hàng nhanh, đóng gói cẩn thận."</p>
                            <small class="text-muted">2 ngày trước</small>
                        </div>
                        
                        <div class="review-item border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <h6>Thu Hương</h6>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <p class="mb-1">"Thiết kế sang trọng, đúng như mô tả. Rất hài lòng với sản phẩm này!"</p>
                            <small class="text-muted">5 ngày trước</small>
                        </div>
                        
                        <div class="review-item">
                            <div class="d-flex justify-content-between">
                                <h6>Văn Hùng</h6>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                            </div>
                            <p class="mb-1">"Sản phẩm đẹp, chất lượng tốt. Tuy nhiên thời gian giao hàng hơi lâu."</p>
                            <small class="text-muted">1 tuần trước</small>
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
                                    <a href="/Ecom_website/customer/product-detail/<?= $relatedProduct->product_id ?>" style="text-decoration: none; color: inherit; display: block;">
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


<script>
    // Đổi ảnh chính khi bấm thumbnail
    function changeImage(element, imageUrl) {
        document.getElementById("mainImage").src = imageUrl;

        // Bỏ active ở tất cả thumbnail
        document.querySelectorAll(".thumbnail-item").forEach(item => {
            item.classList.remove("active");
        });
        // Active thumbnail được chọn
        element.classList.add("active");
    }

    // Chọn option (màu, size)
    function selectOption(element) {
        let parent = element.parentNode;
        // Bỏ active ở nhóm hiện tại
        parent.querySelectorAll(".option-btn").forEach(btn => {
            btn.classList.remove("active");
        });
        // Active option được chọn
        element.classList.add("active");
    }

    // Chuyển tab (mô tả / bổ sung / đánh giá)
    function switchTab(element, tabId) {
        // Reset button active
        document.querySelectorAll(".tab-btn").forEach(btn => {
            btn.classList.remove("active");
        });
        element.classList.add("active");

        // Reset content active
        document.querySelectorAll(".tab-content").forEach(content => {
            content.classList.remove("active");
        });
        document.getElementById(tabId).classList.add("active");
    }

    // Search functionality
    document.querySelector('.search-btn').addEventListener('click', function() {
        const searchTerm = document.querySelector('.search-input').value;
        if (searchTerm.trim()) {
            window.location.href = `/Ecom_website/customer/products?search=${encodeURIComponent(searchTerm.trim())}`;
        }
    });

    // Handle Enter key in search
    document.querySelector('.search-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.querySelector('.search-btn').click();
        }
    });
</script>

