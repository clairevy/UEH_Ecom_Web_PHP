
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jewelry - Luxury Collection</title>
    
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

<!-- Hero Slider -->
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="4"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-slide">
                    <div class="row h-100 align-items-center">
                        <div class="col-md-6">
                            <div class="hero-content">
                                <h1 class="hero-title">Adorn yourself</h1>
                                <h2 class="hero-subtitle">IN BRILLIANCE</h2>
                                <p class="hero-text">Discover our exquisite collection of handcrafted jewelry designed to shine as bright as you do.</p>
                                <button class="btn btn-gold">DISCOVER MORE</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=600&h=600&fit=crop" alt="Jewelry 1" class="hero-image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide">
                    <div class="row h-100 align-items-center">
                        <div class="col-md-6">
                            <div class="hero-content">
                                <h1 class="hero-title">Elegant</h1>
                                <h2 class="hero-subtitle">GOLD COLLECTION</h2>
                                <p class="hero-text">Timeless pieces crafted with precision and passion for the modern woman.</p>
                                <button class="btn btn-gold">EXPLORE NOW</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <img src="https://images.unsplash.com/photo-1611652022419-a9419f74343d?w=600&h=600&fit=crop" alt="Jewelry 2" class="hero-image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide">
                    <div class="row h-100 align-items-center">
                        <div class="col-md-6">
                            <div class="hero-content">
                                <h1 class="hero-title">Luxury</h1>
                                <h2 class="hero-subtitle">DIAMOND RINGS</h2>
                                <p class="hero-text">Celebrate life's precious moments with our stunning diamond collection.</p>
                                <button class="btn btn-gold">SHOP RINGS</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <img src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=600&h=600&fit=crop" alt="Jewelry 3" class="hero-image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide">
                    <div class="row h-100 align-items-center">
                        <div class="col-md-6">
                            <div class="hero-content">
                                <h1 class="hero-title">Sophisticated</h1>
                                <h2 class="hero-subtitle">NECKLACES</h2>
                                <p class="hero-text">Grace your neckline with our carefully curated necklace collection.</p>
                                <button class="btn btn-gold">VIEW COLLECTION</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=600&h=600&fit=crop" alt="Jewelry 4" class="hero-image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide">
                    <div class="row h-100 align-items-center">
                        <div class="col-md-6">
                            <div class="hero-content">
                                <h1 class="hero-title">Exquisite</h1>
                                <h2 class="hero-subtitle">EARRINGS</h2>
                                <p class="hero-text">Complete your look with our stunning earring designs.</p>
                                <button class="btn btn-gold">DISCOVER EARRINGS</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <img src="https://images.unsplash.com/photo-1617038260897-41a1f14a8ca0?w=600&h=600&fit=crop" alt="Jewelry 5" class="hero-image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>




<!-- CONTAINER -->

<!-- New Arrivals -->
<section class="container shrink my-4">
  <h2 class="section-title text-center">NEW ARRIVALS</h2>
  <div class="title-underline"></div>
  <div class="row justify-content-center" id="newArrivalsContainer">
    <!-- JS sẽ render sản phẩm ở đây -->
  </div>
  <button class="carousel-btn prev-btn" onclick="moveCarousel('newArrivals', -1)">
      <i class="fas fa-chevron-left"></i>
  </button>
  <button class="carousel-btn next-btn" onclick="moveCarousel('newArrivals', 1)">
      <i class="fas fa-chevron-right"></i>
  </button>
  <div class="text-center mt-4">
    <button class="btn btn-outline-dark">View more</button>
  </div>
</section>

<!-- The Most Popular -->
<section class="container shrink my-4">
  <h2 class="section-title text-center">THE MOST POPULAR</h2>
  <div class="title-underline"></div>
  <div class="row justify-content-center" id="popularContainer">
    <!-- JS sẽ render sản phẩm ở đây -->
  </div>
  <button class="carousel-btn prev-btn" onclick="moveCarousel('popular', -1)">
      <i class="fas fa-chevron-left"></i>
  </button>
  <button class="carousel-btn next-btn" onclick="moveCarousel('popular', 1)">
      <i class="fas fa-chevron-right"></i>
  </button>
  <div class="text-center mt-4">
    <button class="btn btn-outline-dark">View more</button>
  </div>
</section>

<!-- Category Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">CATEGORY</h2>
                <div class="title-underline"></div>
            </div>
            <div class="position-relative">
                <div class="d-flex justify-content-center" id="categoryCarousel">
                    <?php if (isset($categories) && !empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <div class="category-item text-center mx-3">
                                <div class="category-circle">
                                    <img src="<?= isset($category->banner_image) ? $category->banner_image : 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=100&h=100&fit=crop' ?>" 
                                         alt="<?= htmlspecialchars($category->category_name ?? $category->name ?? 'Category') ?>">
                                </div>
                                <p class="mt-2"><?= htmlspecialchars($category->category_name ?? $category->name ?? 'Category') ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback static categories if no data from DB -->
                        <div class="category-item text-center mx-3">
                            <div class="category-circle">
                                <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=100&h=100&fit=crop" alt="Necklaces">
                            </div>
                            <p class="mt-2">Necklaces</p>
                        </div>
                        <div class="category-item text-center mx-3">
                            <div class="category-circle">
                                <img src="https://images.unsplash.com/photo-1617038260897-41a1f14a8ca0?w=100&h=100&fit=crop" alt="Earrings">
                            </div>
                            <p class="mt-2">Earrings</p>
                        </div>
                        <div class="category-item text-center mx-3">
                            <div class="category-circle">
                                <img src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=100&h=100&fit=crop" alt="Rings">
                            </div>
                            <p class="mt-2">Rings</p>
                        </div>
                        <div class="category-item text-center mx-3">
                            <div class="category-circle">
                                <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=100&h=100&fit=crop" alt="Bracelets">
                            </div>
                            <p class="mt-2">Bracelets</p>
                        </div>
                        <div class="category-item text-center mx-3">
                            <div class="category-circle">
                                <img src="https://images.unsplash.com/photo-1611652022419-a9419f74343d?w=100&h=100&fit=crop" alt="Anklets">
                            </div>
                            <p class="mt-2">Anklets</p>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </section>

        <!-- Gold/Silver Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="category-card gold-card">
                        <div class="category-overlay">
                            <h3 class="category-title">GOLD</h3>                            
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="category-card silver-card">
                        <div class="category-overlay">
                            <h3 class="category-title">SILVER</h3>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Feedback Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">FEEDBACKS</h2>
                <div class="title-underline"></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="feedback-card">
                        <div class="row">
                            <div class="col-4">
                                <img src="<?= asset('images/placeholder.svg') ?>" alt="Customer" class="feedback-image">
                            </div>
                            <div class="col-8">
                                <h5>SOPHIA</h5>
                                <p class="feedback-date">New York City</p>
                                <p class="feedback-text">"The quality and craftsmanship is absolutely outstanding. I've received so many compliments on my jewelry pieces."</p>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="feedback-card">
                        <div class="row">
                            <div class="col-4">
                                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop" alt="Customer" class="feedback-image">
                            </div>
                            <div class="col-8">
                                <h5>EMMA T</h5>
                                <p class="feedback-date">Los Angeles</p>
                                <p class="feedback-text">"Beautiful jewelry that exceeds expectations. The customer service is exceptional and delivery was fast."</p>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button class="btn btn-outline-dark">VIEW MORE</button>
            </div>
        </div>
    </section>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Home
        document.querySelectorAll('.nav-link[href="#index"]').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = 'index.html';
            });
        });
        // Category
        document.querySelectorAll('.nav-link[href="#category"]').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = 'list-product.html';
            });
        });
        // Sign in
        document.querySelectorAll('a[href="#signin"]').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = '/Ecom_website/signin';
            });
        });
    });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Slider functionality
        const dots = document.querySelectorAll('.slider-dot');
        let currentSlide = 0;
        
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                dots[currentSlide].classList.remove('active');
                currentSlide = index;
                dots[currentSlide].classList.add('active');
            });
        });

        // Auto slide (optional)
        setInterval(() => {
            dots[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % dots.length;
            dots[currentSlide].classList.add('active');
        }, 5000);

        // Search functionality - Check if elements exist
        const searchBtn = document.querySelector('.search-btn');
        const searchInput = document.querySelector('.search-input');
        
        if (searchBtn && searchInput) {
            searchBtn.addEventListener('click', function() {
                const searchTerm = searchInput.value;
                if (searchTerm.trim()) {
                    // Redirect to products page with search parameter - Clean URL
                    window.location.href = `/Ecom_website/products?search=${encodeURIComponent(searchTerm.trim())}`;
                }
            });

            // Handle Enter key in search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchBtn.click();
                }
            });
        }

        // Add parallax effect on scroll
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const decoration = document.querySelector('.hero-decoration');
            if (decoration) {
                decoration.style.transform = `rotate(${scrolled * 0.1}deg)`;
            }
        });

        /* Dữ liệu sản phẩm từ server */
        let newArrivalsData = [];
        let popularProductsData = [];

        // Fetch dữ liệu từ server
        async function fetchProductData() {
            try {
                // Fetch new arrivals - Clean URL
                const newArrivalsResponse = await fetch('/Ecom_website/api/new-arrivals?limit=7');
                const newArrivalsResult = await newArrivalsResponse.json();
                console.log('New Arrivals API Response:', newArrivalsResult);
                if (newArrivalsResult.success) {
                    newArrivalsData = newArrivalsResult.data.map(product => {
                        console.log('Processing product:', product);
                        console.log('Primary image:', product.primary_image);
                        return {
                            id: product.product_id,
                            name: product.name.toUpperCase(),
                            desc: product.description ? product.description.substring(0, 80) + '...' : 'No description',
                            price: parseFloat(product.base_price),
                            img: product.primary_image ? product.primary_image.file_path : "/public/assets/images/placeholder.svg",
                            createdAt: product.created_at,
                            sold: Math.floor(Math.random() * 50) // Mock sold data
                        };
                    });
                    console.log('Processed newArrivalsData:', newArrivalsData);
                    console.log('About to render newArrivals...');
                    renderProducts('newArrivals');
                    console.log('newArrivals rendered!');
                }

                // Fetch popular products - Clean URL
                const popularResponse = await fetch('/Ecom_website/api/popular?limit=7');
                const popularResult = await popularResponse.json();
                console.log('Popular API Response:', popularResult);
                if (popularResult.success) {
                    popularProductsData = popularResult.data.map(product => {
                        console.log('Processing popular product:', product);
                        return {
                            id: product.product_id,
                            name: product.name.toUpperCase(),
                            desc: product.description ? product.description.substring(0, 80) + '...' : 'No description',
                            price: parseFloat(product.base_price),
                            img: product.primary_image ? product.primary_image.file_path : "/public/assets/images/placeholder.svg",
                            createdAt: product.created_at,
                            sold: Math.floor(Math.random() * 50) // Mock sold data
                        };
                    });
                    console.log('Processed popularProductsData:', popularProductsData);
                }

                // Render products after data is loaded
                console.log('About to render both sections...');
                renderProducts('newArrivals');
                renderProducts('popular');
                console.log('Both sections rendered!');
            } catch (error) {
                console.error('Error fetching product data:', error);
                // Fallback to mock data if API fails
                newArrivalsData = getMockProducts();
                popularProductsData = getMockProducts();
                renderProducts('newArrivals');
                renderProducts('popular');
            }
        }

        // Fallback mock data
        function getMockProducts() {
            return [
                {
                    id: 1,
                    name: "DIAMOND SOLITAIRE RING",
                    desc: "Crafted in 18K white gold, this solitaire ring features a brilliant-cut diamond...",
                    price: 50,
                    img: "/public/assets/images/placeholder.svg",
                    createdAt: "2025-09-18",
                    sold: 10
                },
                {
                    id: 2,
                    name: "GOLDEN EARRINGS",
                    desc: "18K gold earrings with elegant design.",
                    price: 120,
                    img: "/public/assets/images/placeholder.svg",
                    createdAt: "2025-09-17",
                    sold: 25
                },
                {
                    id: 3,
                    name: "SILVER BRACELET",
                    desc: "Sterling silver bracelet with diamond accents.",
                    price: 80,
                    img: "/public/assets/images/placeholder.svg",
                    createdAt: "2025-09-16",
                    sold: 18
                }
            ];
        }

/* Cấu hình số card hiển thị mỗi lần */
const CARDS_PER_VIEW = 3;

/* State cho carousel */
let carouselState = {
  newArrivals: 0,
  popular: 0
};

/* Hàm render sản phẩm */
function renderProducts(type, direction = null) {
  let dataSource;
  if (type === 'newArrivals') {
    dataSource = newArrivalsData;
  } else {
    dataSource = popularProductsData;
  }

  if (!dataSource || dataSource.length === 0) {
    return; // Don't render if no data
  }

  let start = carouselState[type];
  let items = [];
  for (let i = 0; i < CARDS_PER_VIEW; i++) {
    let idx = (start + i) % dataSource.length;
    let p = dataSource[idx];
            items.push(`
      <div class="col-md-4 mb-4">
        <div class="card product-card">
          <a href="/Ecom_website/product/${p.slug || p.id}" style="text-decoration: none; color: inherit;">
            <img src="${p.img}" class="card-img-top" alt="${p.name}">
            <div class="card-body text-center">
              <h5 class="card-title">${p.name}</h5>
              <p class="card-text">${p.desc}</p>
              <p class="price">${p.price.toLocaleString('vi-VN')}₫</p>
              <div class="product-actions">
                <i class="bi bi-heart"></i>
                <i class="bi bi-cart"></i>
              </div>
            </div>
          </a>
        </div>
      </div>
    `);
  }
  const container = document.getElementById(type === 'newArrivals' ? 'newArrivalsContainer' : 'popularContainer');
  container.innerHTML = items.join('');
  // Xóa cả hai class trước khi thêm mới
  container.classList.remove('product-carousel-slide-left', 'product-carousel-slide-right');
  if (direction) {
    void container.offsetWidth; // Force reflow
    if (direction === 'left') {
      container.classList.add('product-carousel-slide-left');
    } else if (direction === 'right') {
      container.classList.add('product-carousel-slide-right');
    }
  }
}

function moveCarousel(type, dir) {
  let dataSource = type === 'newArrivals' ? newArrivalsData : popularProductsData;
  if (!dataSource || dataSource.length === 0) return;
  
  let sortedLength = dataSource.length;
  carouselState[type] = (carouselState[type] + dir + sortedLength) % sortedLength;
  renderProducts(type, dir === 1 ? 'right' : 'left');
}

// Khởi tạo - fetch data và render
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM loaded, starting fetchProductData...');
  fetchProductData();
});
    </script>
</body>
</html>