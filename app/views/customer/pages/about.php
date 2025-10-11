<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JEWELRY - Giới thiệu</title>
    <link href="<?= asset('css/css.css?v=' . time()) ?>" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Main Content -->
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="text-center mb-5">
                    <h1 class="display-4 mb-4">Về chúng tôi</h1>
                    <p class="lead">Chào mừng đến với JEWELRY - nơi tôn vinh vẻ đẹp và sự tinh tế</p>
                </div>

                <div class="row mb-5">
                    <div class="col-md-6">
                        <img src="<?= asset('images/about-us.jpg') ?>" class="img-fluid rounded" alt="About Us">
                    </div>
                    <div class="col-md-6">
                        <h3>Câu chuyện của chúng tôi</h3>
                        <p>
                            JEWELRY được thành lập với sứ mệnh mang đến những tác phẩm trang sức độc đáo, 
                            tinh tế và chất lượng cao. Chúng tôi tin rằng mỗi món trang sức không chỉ là 
                            một phụ kiện, mà còn là cách thể hiện cá tính và phong cách riêng của bạn.
                        </p>
                        <p>
                            Với đội ngũ thợ kim hoàn tài năng và kinh nghiệm nhiều năm trong ngành, 
                            chúng tôi cam kết tạo ra những sản phẩm hoàn hảo nhất cho khách hàng.
                        </p>
                    </div>
                </div>

                <div class="row text-center mb-5">
                    <div class="col-md-4">
                        <div class="card h-100 border-0">
                            <div class="card-body">
                                <i class="fas fa-gem fa-3x text-warning mb-3"></i>
                                <h5>Chất lượng cao</h5>
                                <p>Sử dụng chỉ những chất liệu tốt nhất, được kiểm định nghiêm ngặt</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0">
                            <div class="card-body">
                                <i class="fas fa-hammer fa-3x text-warning mb-3"></i>
                                <h5>Thủ công tinh xảo</h5>
                                <p>Mỗi sản phẩm được chế tác thủ công bởi những nghệ nhân tài ba</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0">
                            <div class="card-body">
                                <i class="fas fa-heart fa-3x text-warning mb-3"></i>
                                <h5>Tận tâm phục vụ</h5>
                                <p>Đội ngũ tư vấn chuyên nghiệp, luôn sẵn sàng hỗ trợ khách hàng</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <h4>Liên hệ với chúng tôi</h4>
                    <p>Địa chỉ: 123 Đường ABC, Quận XYZ, TP.HCM</p>
                    <p>Điện thoại: 0123 456 789</p>
                    <p>Email: info@jewelry.com</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
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
</body>
</html>