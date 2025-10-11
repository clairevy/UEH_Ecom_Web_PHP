<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Ecom_website/helpers/session_helper.php';
$isLoggedIn = SessionHelper::isLoggedIn();
$user = SessionHelper::getUser();
?>

<!-- Navigation -->
<nav th:fragment="header" class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <!-- Logo - Link về trang chủ -->
        <a class="navbar-brand" href="<?= url('/') ?>">
            <i class="fas fa-gem me-2"></i>JEWELRY
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <!-- Home -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/') ?>">Home</a>
                </li>
                
                <!-- About Us -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/about') ?>">About us</a>
                </li>
                
                <!-- Category Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Category
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                        <li><a class="dropdown-item" href="<?= url('/products') ?>">All Products</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <!-- Categories sẽ được load bằng JavaScript -->
                        <div id="categoriesDropdown">
                            <li><span class="dropdown-item text-muted">Loading...</span></li>
                        </div>
                    </ul>
                </li>
                
                <!-- Collection Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="collectionDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Collection
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="collectionDropdown">
                        <!-- Collections sẽ được load bằng JavaScript -->
                        <div id="collectionsDropdown">
                            <li><span class="dropdown-item text-muted">Loading...</span></li>
                        </div>
                    </ul>
                </li>
            </ul>
            
            <!-- Search Form -->
            <form class="d-flex me-3" id="searchForm">
                <div class="input-group">
                    <input class="form-control search-input" type="search" placeholder="Tìm kiếm sản phẩm..." id="searchInput">
                    <button class="btn btn-outline-secondary search-btn" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <!-- Right Side Actions -->
            <ul class="navbar-nav">
                <!-- Shopping Cart -->
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?= url('/cart') ?>">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartBadge">
                            0
                        </span>
                    </a>
                </li>
                
                <?php if ($isLoggedIn): ?>
                    <!-- User Dropdown (Đã đăng nhập) -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= SessionHelper::getAvatarUrl() ?>" class="rounded-circle me-2" width="30" height="30" alt="Avatar">
                            <span class="d-none d-lg-inline"><?= htmlspecialchars($user->name ?: 'User') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <div class="dropdown-header">
                                    <div class="fw-bold"><?= htmlspecialchars($user->name ?: 'User') ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($user->email) ?></small>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('/profile') ?>">
                                <i class="fas fa-user me-2"></i>Thông tin cá nhân
                            </a></li>
                            <li><a class="dropdown-item" href="<?= url('/orders') ?>">
                                <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi
                            </a></li>
                            <li><a class="dropdown-item" href="<?= url('/wishlist') ?>">
                                <i class="fas fa-heart me-2"></i>Danh sách yêu thích
                            </a></li>
                            <li><a class="dropdown-item" href="<?= url('/settings') ?>">
                                <i class="fas fa-cog me-2"></i>Cài đặt
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="logout()">
                                <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Auth Links (Chưa đăng nhập) -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/signin') ?>">
                            <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="<?= url('/signup') ?>">
                            <i class="fas fa-user-plus me-1"></i>Đăng ký
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Additional Styles for User Navigation -->
<style>
.navbar {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.navbar-brand {
    font-weight: bold;
    font-size: 1.5rem;
    color: #667eea !important;
}

.navbar-nav .nav-link {
    font-weight: 500;
    margin: 0 5px;
    transition: color 0.3s ease;
}

.navbar-nav .nav-link:hover {
    color: #667eea !important;
}

.search-input {
    border-radius: 20px 0 0 20px;
    border-right: none;
}

.search-input:focus {
    box-shadow: none;
    border-color: #667eea;
}

.btn-outline-secondary {
    border-radius: 0 20px 20px 0;
    border-left: none;
}

.dropdown-menu {
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.dropdown-item {
    padding: 10px 20px;
    transition: background-color 0.3s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #667eea;
}

.dropdown-header {
    padding: 15px 20px 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin: -8px -16px 0;
    border-radius: 10px 10px 0 0;
}

.badge {
    font-size: 0.6em;
}

@media (max-width: 991px) {
    .navbar-nav .nav-item {
        text-align: center;
        margin: 5px 0;
    }
    
    .search-input {
        border-radius: 20px;
        border: 1px solid #ced4da;
    }
    
    .btn-outline-secondary {
        border-radius: 20px;
        border: 1px solid #ced4da;
        margin-left: 10px;
    }
}
</style>

<!-- JavaScript for User Actions -->
<script>
// Logout function
function logout() {
    if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
        fetch('<?= url('/auth/logout') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '<?= url('/') ?>';
            } else {
                alert('Có lỗi xảy ra khi đăng xuất');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback: redirect to logout page
            window.location.href = '<?= url('/auth/logout') ?>';
        });
    }
}

// Search functionality
document.getElementById('searchForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const searchTerm = document.getElementById('searchInput').value.trim();
    if (searchTerm) {
        // Redirect to products page with search parameter
        window.location.href = '/Ecom_website/products?search=' + encodeURIComponent(searchTerm);
    }
});

// Also handle button click directly
document.querySelector('#searchForm button')?.addEventListener('click', function(e) {
    e.preventDefault();
    const searchTerm = document.getElementById('searchInput').value.trim();
    if (searchTerm) {
        window.location.href = '/Ecom_website/products?search=' + encodeURIComponent(searchTerm);
    }
});

// Update cart badge (sẽ implement sau)
function updateCartBadge() {
    // TODO: Fetch cart count from API
    // document.getElementById('cartBadge').textContent = count;
}

// Load categories và collections dropdown
document.addEventListener('DOMContentLoaded', function() {
    // Load categories
    fetch('<?= url('/api/categories') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.categories) {
                const categoriesDiv = document.getElementById('categoriesDropdown');
                categoriesDiv.innerHTML = '';
                data.categories.forEach(category => {
                    const li = document.createElement('li');
                    li.innerHTML = `<a class="dropdown-item" href="<?= url('/products') ?>?category=${category.id}">${category.name}</a>`;
                    categoriesDiv.appendChild(li);
                });
            }
        })
        .catch(error => {
            document.getElementById('categoriesDropdown').innerHTML = '<li><span class="dropdown-item text-muted">Không thể tải danh mục</span></li>';
        });
    
    // Load collections
    fetch('<?= url('/api/collections') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.collections) {
                const collectionsDiv = document.getElementById('collectionsDropdown');
                collectionsDiv.innerHTML = '';
                data.collections.forEach(collection => {
                    const li = document.createElement('li');
                    li.innerHTML = `<a class="dropdown-item" href="<?= url('/products') ?>?collection=${collection.id}">${collection.name}</a>`;
                    collectionsDiv.appendChild(li);
                });
            }
        })
        .catch(error => {
            document.getElementById('collectionsDropdown').innerHTML = '<li><span class="dropdown-item text-muted">Không thể tải bộ sưu tập</span></li>';
        });
});
</script>