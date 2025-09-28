<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JEWELRY - Danh sách sản phẩm</title>
    <link href="<?= asset('css/css.css?v=' . time()) ?>" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS --> 
        <title>Danh sách sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">   
    
</head>
<body>
    <!-- Header -->
    <!-- Navigation -->
<nav th:fragment="header" class="navbar navbar-expand-lg fixed-top">
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
                    <input type="text" class="search-input" placeholder="What are you looking for?" 
                           value="<?= isset($filters['search']) ? htmlspecialchars($filters['search']) : '' ?>">
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

    <!-- Banner Section -->
    <section class="banner-section">
        <div class="banner-content">
            <!-- <h2>Luxury Jewelry Collection</h2> -->
            <!-- <p>Discover our exquisite handcrafted pieces</p> -->
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <div class="row gap-6">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 col-md-4">
                <!-- Category Filter -->
                <div class="filter-card">
                    <div class="filter-header">
                        <i class="fas fa-list me-2"></i>Category
                    </div>
                    <div class="filter-body">
                        <?php if (isset($categories) && !empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" 
                                           value="<?= $category->category_id ?>" 
                                           id="category_<?= $category->category_id ?>"
                                           <?= (isset($filters['category']) && $filters['category'] == $category->category_id) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="category_<?= $category->category_id ?>">
                                        <?= htmlspecialchars($category->name) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback static categories -->
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="rings" id="rings">
                            <label class="form-check-label" for="rings">Nhẫn</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="necklaces" id="necklaces">
                            <label class="form-check-label" for="necklaces">Dây chuyền</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="earrings" id="earrings">
                            <label class="form-check-label" for="earrings">Bông tai</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="bracelets" id="bracelets">
                            <label class="form-check-label" for="bracelets">Vòng tay</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="watches" id="watches">
                            <label class="form-check-label" for="watches">Đồng hồ</label>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Material Filter -->
                <div class="filter-card">
                    <div class="filter-header">
                        <i class="fas fa-gem me-2"></i>Chất liệu
                    </div>
                    <div class="filter-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="gold" id="gold">
                            <label class="form-check-label" for="gold">Vàng</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="silver" id="silver">
                            <label class="form-check-label" for="silver">Bạc</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="diamond" id="diamond">
                            <label class="form-check-label" for="diamond">Kim cương</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="pearl" id="pearl">
                            <label class="form-check-label" for="pearl">Ngọc trai</label>
                        </div>
                    </div>
                </div>

                <!-- Brand Filter -->
                <div class="filter-card">
                    <div class="filter-header">
                        <i class="fas fa-star me-2"></i>Thương hiệu
                    </div>
                    <div class="filter-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="luxury-gold" id="luxury-gold">
                            <label class="form-check-label" for="luxury-gold">Luxury Gold</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="diamond-star" id="diamond-star">
                            <label class="form-check-label" for="diamond-star">Diamond Star</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="pearl-beauty" id="pearl-beauty">
                            <label class="form-check-label" for="pearl-beauty">Pearl Beauty</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="elegant" id="elegant">
                            <label class="form-check-label" for="elegant">Elegant</label>
                        </div>
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="filter-card">
                    <div class="filter-header">
                        <i class="fas fa-dollar-sign me-2"></i>Price Range
                    </div>
                    <div class="filter-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="priceRange" value="all" id="all-price" checked>
                            <label class="form-check-label" for="all-price">All Price</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="priceRange" value="0-1000000" id="under-1m">
                            <label class="form-check-label" for="under-1m">Dưới 1,000,000₫</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="priceRange" value="1000000-5000000" id="1m-5m">
                            <label class="form-check-label" for="1m-5m">1,000,000₫ - 5,000,000₫</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="priceRange" value="5000000-10000000" id="5m-10m">
                            <label class="form-check-label" for="5m-10m">5,000,000₫ - 10,000,000₫</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="priceRange" value="10000000-999999999" id="over-10m">
                            <label class="form-check-label" for="over-10m">Trên 10,000,000₫</label>
                        </div>
                        <div class="price-range-inputs">
                            <input type="number" class="form-control" placeholder="Min price" id="minPrice">
                            <input type="number" class="form-control" placeholder="Max price" id="maxPrice">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Section -->
            <div class="col-lg-9 col-md-8">
                <!-- Product Header -->
                <div class="product-header">
                    <div class="row align-items-center mb-3">
                        <!-- <div class="col-md-6">
                            <h3 class="mb-0">Sản phẩm trang sức</h3>
                        </div> -->
                        <div class="results-info col-md-6">
                            <strong id="resultsCount"><?= isset($total) ? $total : 0 ?></strong> sản phẩm được tìm thấy
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="sortSelect">
                                <option value="popular">Most Popular</option>
                                <option value="price_asc">Price: Low to High</option>
                                <option value="price_desc">Price: High to Low</option>
                                <option value="newest">Newest</option>
                                <option value="rating">Best Rating</option>
                            </select>
                        </div>
                    </div>

                    <!-- Active Filters -->
                                        
                </div>

                <!-- Product Grid -->
                <div class="row" id="productGrid">
                    <?php if (isset($products) && !empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <div class="col-lg-4 col-md-6 mb-4 d-flex justify-content-center">
                                <div class="list-card-product">
                                    <a href="<?= route('product/' . ($product->slug ?? $product->product_id)) ?>" style="text-decoration: none; color: inherit; display: block;">
                                        <div class="position-relative">
                                            <img src="<?= $product->primary_image ? $product->primary_image->file_path : asset('images/placeholder.svg') ?>" 
                                                 class="card-img-top" alt="<?= htmlspecialchars($product->name) ?>">
                                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">NEW</span>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title mb-2"><?= htmlspecialchars($product->name) ?></h5>
                                            <div class="mb-2">
                                                <span class="price fw-bold"><?= number_format($product->base_price, 0, ',', '.') ?>₫</span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="rating text-warning">
                                                    ★★★★★
                                                </span>
                                                <span class="text-muted ms-1">(<?= rand(10, 200) ?>)</span>
                                            </div>
                                            <div class="product-actions">
                                                <button class="btn btn-icon btn-sm me-2" title="Yêu thích" onclick="event.preventDefault(); event.stopPropagation();"><i class="fa-regular fa-heart"></i></button>
                                                <button class="btn btn-icon btn-sm" title="Thêm vào giỏ" onclick="event.preventDefault(); event.stopPropagation();"><i class="fa-solid fa-cart-plus"></i></button>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5 text-muted">
                            Không tìm thấy sản phẩm phù hợp.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if (isset($totalPages) && $totalPages > 1): ?>
                <nav aria-label="Product pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= max(1, $currentPage - 1) ?><?= isset($filters['search']) && !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?>" tabindex="-1">Previous</a>
                        </li>
                        
                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= isset($filters['search']) && !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= min($totalPages, $currentPage + 1) ?><?= isset($filters['search']) && !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

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

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        // Hàm áp dụng filter và reload trang với parameters mới
        function applyFilters() {
            const url = new URL(window.location);
            
            // Get category filters
            const categories = Array.from(document.querySelectorAll('input[type=checkbox][id^="category_"]'))
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            if (categories.length > 0) {
                url.searchParams.set('category', categories[0]); // For now, support single category
            } else {
                url.searchParams.delete('category');
            }

            // Get price range
            const priceRange = document.querySelector('input[name="priceRange"]:checked')?.value;
            if (priceRange && priceRange !== 'all') {
                const [minPrice, maxPrice] = priceRange.split('-');
                url.searchParams.set('min_price', minPrice);
                url.searchParams.set('max_price', maxPrice);
            } else {
                url.searchParams.delete('min_price');
                url.searchParams.delete('max_price');
            }

            // Get custom price
            const customMinPrice = document.getElementById('minPrice')?.value;
            const customMaxPrice = document.getElementById('maxPrice')?.value;
            if (customMinPrice) {
                url.searchParams.set('min_price', customMinPrice);
            }
            if (customMaxPrice) {
                url.searchParams.set('max_price', customMaxPrice);
            }

            // Get sort
            const sort = document.getElementById('sortSelect')?.value;
            if (sort) {
                url.searchParams.set('sort', sort);
            }

            // Reset to page 1 when filtering
            url.searchParams.set('page', '1');

            // Redirect to new URL
            window.location.href = url.toString();
        }

        // Gắn sự kiện cho filter và sort
        function attachFilterEvents() {
            // Checkbox, radio, select
            document.querySelectorAll('.filter-card input, #sortSelect').forEach(el => {
                el.addEventListener('change', applyFilters);
            });
            
            // Custom price inputs with debounce
            let priceTimeout;
            document.getElementById('minPrice')?.addEventListener('input', function() {
                clearTimeout(priceTimeout);
                priceTimeout = setTimeout(applyFilters, 1000); // Wait 1 second after user stops typing
            });
            document.getElementById('maxPrice')?.addEventListener('input', function() {
                clearTimeout(priceTimeout);
                priceTimeout = setTimeout(applyFilters, 1000);
            });
        }

        // Search functionality
        document.querySelector('.search-btn').addEventListener('click', function() {
            const searchTerm = document.querySelector('.search-input').value;
            if (searchTerm.trim()) {
                const url = new URL(window.location);
                url.searchParams.set('search', searchTerm.trim());
                url.searchParams.set('page', '1'); // Reset to page 1
                window.location.href = url.toString();
            }
        });

        // Handle Enter key in search
        document.querySelector('.search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('.search-btn').click();
            }
        });

        // Khởi tạo trang
        document.addEventListener('DOMContentLoaded', function() {
            attachFilterEvents();
        });
    </script>
</body>
</html>