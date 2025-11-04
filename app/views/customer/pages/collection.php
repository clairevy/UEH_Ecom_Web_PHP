<?php
$title = $data['title'] ?? 'Collection';
$collection = $data['collection'] ?? null;
$products = $data['products'] ?? [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Jewelry Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/css.css?v=' . time()) ?>" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100vh;
            width: 100vw;
            margin: 0;
            padding: 0;
            overflow: hidden;
            position: fixed;
        }

        body {
            font-family: "Inter", sans-serif;
        }

        .container-fluid {
            padding: 0;
            margin: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        /* Hero Section */
        .hero-section {
            
            background-size: cover;
            background-position: center;
            min-height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            position: relative;
            margin-top: 80px;
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
        }

        .hero-content {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 300;
            letter-spacing: 8px;
            margin-bottom: 2rem;
            color: #3e2723;
            white-space: nowrap;
            overflow: visible;
        }

        .hero-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #666;
            margin-bottom: 2rem;
            opacity: 1;
            transition: opacity 0.5s ease-out;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-description.fade-out {
            opacity: 0;
        }

        .btn-view-details {
            background: transparent;
            border: 2px solid #d4af37;
            color: #d4af37;
            padding: 0.8rem 3rem;
            font-size: 1rem;
            letter-spacing: 2px;
            transition: all 0.5s ease;
            cursor: pointer;
            border-radius: 0;
            opacity: 1;
            white-space: nowrap;
        }

        .btn-view-details:hover {
            background: #d4af37;
            color: white;
        }

        /* Product Cards Section */
        .products-section {
            position: absolute;
            bottom: -100%;
            left: 0;
            right: 0;
            padding: 3rem 0;
            opacity: 0;
            transition: all 0.8s ease-out;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 10;
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .products-section.show {
            bottom: 0;
            opacity: 1;
        }

        .product-carousel {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            overflow: visible;
            position: relative;
            z-index: 1;
        }

        .carousel-nav {
            width: 50px;
            height: 50px;
            border: 2px solid #d4af37;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            flex-shrink: 0;
            color: #d4af37;
            font-size: 1.5rem;
        }

        .carousel-nav:hover {
            background: #d4af37;
            color: white;
        }

        .carousel-nav:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        #collectionTitle {
            text-transform: <?= $collection ? 'uppercase' : 'none' ?>;
            text-decoration: none !important;
            font-family: 'Times New Roman', Times, serif;
        }

        .cards-container {
            display: flex;
            gap: 2rem;
            overflow: visible;
            padding: 20px 10px;
            margin: -20px -10px;
        }

        .product-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(212, 175, 55, 0.15);
            width: 280px;
            flex-shrink: 0;
            transition: all 0.3s ease;
            border: 1px solid rgba(212, 175, 55, 0.1);
            position: relative;
            z-index: 1;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 32px rgba(212, 175, 55, 0.25);
            z-index: 2;
        }

        .product-image {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, #f5f3ed 0%, #f8f6f0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #3e2723;
            letter-spacing: 1px;
            font-family: 'Playfair Display', serif;
        }

        .product-description {
            font-size: 0.85rem;
            color: #666;
            line-height: 1.5;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: 600;
            color: #d4af37;
        }

        .product-actions {
            display: flex;
            gap: 0.5rem;
        }

        .icon-btn {
            width: 35px;
            height: 35px;
            border: 1px solid rgba(212, 175, 55, 0.3);
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: #d4af37;
        }

        .icon-btn:hover {
            background: #d4af37;
            color: white;
            border-color: #d4af37;
            transform: translateY(-2px);
        }

        @media (max-width: 1200px) {
            .hero-title {
                font-size: 2.8rem;
                letter-spacing: 6px;
            }
        }

        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.5rem;
                letter-spacing: 4px;
            }
            
            .product-carousel {
                gap: 1.5rem;
            }
            
            .product-card {
                width: 260px;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                margin-top: 60px;
                min-height: 70vh;
                padding: 1.5rem;
            }
            
            .hero-content {
                padding: 0 1rem;
            }
            
            .hero-title {
                font-size: 2rem;
                letter-spacing: 3px;
                white-space: normal;
                word-wrap: break-word;
            }

            .hero-description {
                font-size: 1rem;
            }

            .product-carousel {
                gap: 1rem;
                padding: 0 1rem;
            }

            .cards-container {
                gap: 1rem;
            }

            .product-card {
                width: 240px;
            }

            .carousel-nav {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.5rem;
                letter-spacing: 2px;
            }
            
            .hero-description {
                font-size: 0.9rem;
            }

            .btn-view-details {
                padding: 0.7rem 2rem;
                font-size: 0.9rem;
            }

            .product-card {
                width: 220px;
            }
            
            .product-carousel {
                padding: 0 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Hero Section -->
    <div class="container-fluid p-0">
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title" id="collectionTitle"><?= $collection ? strtoupper($collection->collection_name) : 'COLLECTION' ?></h1>
                <p class="hero-description" id="collectionDescription">
                    <?= $collection ? $collection->description : 'Khám phá bộ sưu tập trang sức đẳng cấp với những thiết kế độc đáo và chất lượng hoàn hảo.' ?>
                </p>
                <button class="btn-view-details" onclick="showProducts()" type="button">Xem chi tiết</button>
            </div>

            <!-- Products Section -->
            <div class="products-section" id="productsSection">
                <div class="product-carousel">
                    <button class="carousel-nav" id="prevBtn" onclick="previousSlide()">
                        <span>‹</span>
                    </button>
                    
                    <div class="cards-container" id="cardsContainer">
                        <!-- Products will be dynamically loaded here -->
                    </div>

                    <button class="carousel-nav" id="nextBtn" onclick="nextSlide()">
                        <span>›</span>
                    </button>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // PHP data to JavaScript
        const collectionData = <?= $collection ? json_encode($collection) : 'null' ?>;
        const productsData = <?= json_encode($products) ?>;
        
        let currentIndex = 0;
        let productsShown = false;
        const productsPerPage = 3;

        function formatPrice(price, salePrice = null) {
            if (salePrice && parseFloat(salePrice) < parseFloat(price)) {
                return `<span class="text-decoration-line-through text-muted">$${parseFloat(price).toFixed(1)}</span> <span class="text-danger">$${parseFloat(salePrice).toFixed(1)}</span>`;
            }
            return `$${parseFloat(price).toFixed(1)}`;
        }

        function renderProducts() {
            if (!productsData || productsData.length === 0) {
                return;
            }

            const container = document.getElementById('cardsContainer');
            container.innerHTML = '';

            const visibleProducts = productsData.slice(currentIndex, currentIndex + productsPerPage);

            visibleProducts.forEach(product => {
                const card = document.createElement('div');
                card.className = 'product-card';
                
                // Get main image
                const mainImage = product.main_image || '/public/assets/images/placeholder.svg';
                const imageUrl = mainImage.startsWith('/') ? mainImage : `/public/uploads/products/${product.product_id}/${mainImage}`;
                
                card.innerHTML = `
                    <div class="product-image">
                        <img src="${imageUrl}" alt="${product.name}" 
                             onerror="this.src='/public/assets/images/placeholder.svg'">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">${product.name}</h3>
                        <p class="product-description">${product.description || 'Sản phẩm chất lượng cao với thiết kế tinh tế và sang trọng.'}</p>
                        <div class="product-footer">
                            <div class="product-price">${formatPrice(product.price, product.sale_price)}</div>
                            <div class="product-actions">
                                <button class="icon-btn" onclick="addToWishlist(${product.product_id})" title="Thêm vào yêu thích">
                                    <i class="far fa-heart"></i>
                                </button>
                                <button class="icon-btn" onclick="addToCart(${product.product_id})" title="Thêm vào giỏ hàng">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });

            updateNavigationButtons();
        }

        function updateNavigationButtons() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex + productsPerPage >= productsData.length;
        }

        function showProducts() {
            if (!productsShown && productsData && productsData.length > 0) {
                const description = document.getElementById('collectionDescription');
                const button = document.querySelector('.btn-view-details');
                const productsSection = document.getElementById('productsSection');
                
                // Fade out description and button
                description.classList.add('fade-out');
                if (button) {
                    button.style.opacity = '0';
                    button.style.pointerEvents = 'none';
                }
                
                setTimeout(() => {
                    productsSection.classList.add('show');
                }, 500);
                
                productsShown = true;
            }
        }

        function nextSlide() {
            if (productsData && currentIndex + productsPerPage < productsData.length) {
                currentIndex++;
                renderProducts();
            }
        }

        function previousSlide() {
            if (currentIndex > 0) {
                currentIndex--;
                renderProducts();
            }
        }

        function addToCart(productId) {
            // Implement add to cart functionality
            console.log('Add to cart:', productId);
            // You can add AJAX call to add product to cart
        }

        function addToWishlist(productId) {
            // Implement add to wishlist functionality
            console.log('Add to wishlist:', productId);
            // You can add AJAX call to add product to wishlist
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            renderProducts();
            
            // Debug info
            console.log('Collection data:', collectionData);
            console.log('Products data:', productsData);
            console.log('Products count:', productsData ? productsData.length : 0);
        });
    </script>

    <!-- Include Footer -->
    <?php include __DIR__ . '/../components/footer.html'; ?>
</body>
</html>