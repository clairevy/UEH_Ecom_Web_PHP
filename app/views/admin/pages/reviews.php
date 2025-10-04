<?php
/**
 * Reviews Page - Quản lý đánh giá với database
 * Chuyển đổi từ reviews.html với OOP MVC
 */

// Thiết lập
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Include models
require_once __DIR__ . '/../models/ReviewModel.php';

try {
    // Khởi tạo model
    $reviewModel = new ReviewModel();
    
    // Lấy dữ liệu từ database
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $keyword = $_GET['search'] ?? '';
    $rating = $_GET['rating'] ?? '';
    $status = $_GET['status'] ?? '';
    
    // Lấy reviews theo điều kiện
    if (!empty($rating) && is_numeric($rating)) {
        $reviews = $reviewModel->getReviewsByRating($rating, $limit, $offset);
    } else if (!empty($status)) {
        $reviews = $reviewModel->getReviewsByStatus($status, $limit, $offset);
    } else if (!empty($keyword)) {
        $reviews = $reviewModel->searchReviews($keyword, $limit, $offset);
    } else {
        $reviews = $reviewModel->getReviewsWithDetails($limit, $offset);
    }
    
    // Lấy thống kê
    $stats = $reviewModel->getReviewStats();
    $totalReviews = $stats['total'];
    $totalPages = ceil($totalReviews / $limit);
    $avgRating = $reviewModel->getAverageRating();
    
    // Thiết lập dữ liệu cho view
    $pageTitle = 'JEWELLERY Admin - Đánh giá';
    $currentPage = 'reviews';
    $baseUrl = '../';
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $reviews = [];
    $totalReviews = 0;
    $totalPages = 0;
    $stats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
    $avgRating = 0;
    $pageTitle = 'JEWELLERY Admin - Đánh giá';
    $currentPage = 'reviews';
    $baseUrl = '../';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/variables.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/reviews.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Component Container -->
        <div id="sidebar-container"></div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header Component Container -->
            <div id="header-container"></div>

            <!-- Content -->
            <main class="content">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Lỗi:</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php else: ?>
                    <!-- Reviews Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828970.png" alt="Total Reviews" width="32" height="32" class="me-2">
                                        <h3 class="mb-0 text-primary"><?= number_format($stats['total']) ?></h3>
                                    </div>
                                    <h6 class="fw-bold">Tổng Đánh Giá</h6>
                                    <p class="text-muted small mb-0">Tất cả reviews</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828884.png" alt="Pending Reviews" width="32" height="32" class="me-2">
                                        <h3 class="mb-0 text-warning"><?= number_format($stats['pending']) ?></h3>
                                    </div>
                                    <h6 class="fw-bold">Chờ Duyệt</h6>
                                    <p class="text-muted small mb-0">Cần xử lý</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828888.png" alt="Approved Reviews" width="32" height="32" class="me-2">
                                        <h3 class="mb-0 text-success"><?= number_format($stats['approved']) ?></h3>
                                    </div>
                                    <h6 class="fw-bold">Đã Duyệt</h6>
                                    <p class="text-muted small mb-0">Đã phê duyệt</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828888.png" alt="Average Rating" width="32" height="32" class="me-2">
                                        <h3 class="mb-0 text-info"><?= $avgRating ?></h3>
                                    </div>
                                    <h6 class="fw-bold">Điểm Trung Bình</h6>
                                    <p class="text-muted small mb-0">Rating trung bình</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex gap-3">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" class="form-control" placeholder="Tìm kiếm reviews..." 
                                       value="<?= htmlspecialchars($keyword) ?>" id="searchInput">
                                <button class="btn btn-outline-secondary" type="button" onclick="searchReviews()">
                                    <img src="https://cdn-icons-png.flaticon.com/512/54/54481.png" alt="Search" width="16" height="16">
                                </button>
                            </div>
                            <select class="form-select" style="width: 150px;" id="ratingFilter" onchange="filterByRating()">
                                <option value="">Tất cả sao</option>
                                <option value="5" <?= $rating == '5' ? 'selected' : '' ?>>5 sao</option>
                                <option value="4" <?= $rating == '4' ? 'selected' : '' ?>>4 sao</option>
                                <option value="3" <?= $rating == '3' ? 'selected' : '' ?>>3 sao</option>
                                <option value="2" <?= $rating == '2' ? 'selected' : '' ?>>2 sao</option>
                                <option value="1" <?= $rating == '1' ? 'selected' : '' ?>>1 sao</option>
                            </select>
                            <select class="form-select" style="width: 150px;" id="statusFilter" onchange="filterByStatus()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                                <option value="approved" <?= $status == 'approved' ? 'selected' : '' ?>>Đã duyệt</option>
                                <option value="rejected" <?= $status == 'rejected' ? 'selected' : '' ?>>Đã từ chối</option>
                            </select>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary" onclick="bulkApprove()">
                                <img src="https://cdn-icons-png.flaticon.com/512/1828/1828888.png" alt="Approve" width="16" height="16" class="me-2">
                                Duyệt hàng loạt
                            </button>
                            <button class="btn btn-outline-danger" onclick="bulkReject()">
                                <img src="https://cdn-icons-png.flaticon.com/512/1828/1828888.png" alt="Reject" width="16" height="16" class="me-2">
                                Từ chối hàng loạt
                            </button>
                        </div>
                    </div>

                    <!-- Reviews Table -->
                    <div class="table-card">
                        <div class="table-header">
                            <h5 class="table-title">Danh sách đánh giá</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted">Tổng: <?= count($reviews) ?> reviews</span>
                                <div class="dropdown">
                                    <button class="btn btn-link" href="#" role="button" data-bs-toggle="dropdown">
                                        <img src="https://cdn-icons-png.flaticon.com/512/2311/2311524.png" alt="More" width="20" height="20">
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Xuất Excel</a></li>
                                        <li><a class="dropdown-item" href="#">Xuất PDF</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table" id="reviewsTable">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input" id="selectAllReviews">
                                        </th>
                                        <th>Khách hàng</th>
                                        <th>Sản phẩm</th>
                                        <th>Đánh giá</th>
                                        <th>Điểm</th>
                                        <th>Ngày tạo</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($reviews)): ?>
                                        <?php foreach ($reviews as $review): ?>
                                            <tr>
                                                <td><input type="checkbox" class="form-check-input review-checkbox"></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User" width="32" height="32" class="rounded-circle me-3">
                                                        <div>
                                                            <div class="fw-bold"><?= htmlspecialchars($review['customer_name']) ?></div>
                                                            <div class="text-muted small">ID: <?= $review['customer_id'] ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?= htmlspecialchars($review['product_name']) ?></div>
                                                    <div class="text-muted small">ID: <?= $review['product_id'] ?></div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?= htmlspecialchars($review['title'] ?? 'Không có tiêu đề') ?></div>
                                                    <div class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($review['comment']) ?>">
                                                        <?= htmlspecialchars($review['comment']) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <?php if ($i <= $review['rating']): ?>
                                                                <span class="text-warning">★</span>
                                                            <?php else: ?>
                                                                <span class="text-muted">★</span>
                                                            <?php endif; ?>
                                                        <?php endfor; ?>
                                                        <span class="ms-2 fw-bold"><?= $review['rating'] ?></span>
                                                    </div>
                                                </td>
                                                <td><?= date('d/m/Y H:i', strtotime($review['created_at'])) ?></td>
                                                <td>
                                                    <?php
                                                        $statusClass = '';
                                                        $statusText = '';
                                                        switch($review['status']) {
                                                            case 'pending':
                                                                $statusClass = 'bg-warning';
                                                                $statusText = 'Chờ duyệt';
                                                                break;
                                                            case 'approved':
                                                                $statusClass = 'bg-success';
                                                                $statusText = 'Đã duyệt';
                                                                break;
                                                            case 'rejected':
                                                                $statusClass = 'bg-danger';
                                                                $statusText = 'Đã từ chối';
                                                                break;
                                                            default:
                                                                $statusClass = 'bg-secondary';
                                                                $statusText = 'Không xác định';
                                                        }
                                                    ?>
                                                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-outline-success" 
                                                                onclick="approveReview(<?= $review['review_id'] ?>)">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                onclick="rejectReview(<?= $review['review_id'] ?>)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                onclick="editReview(<?= $review['review_id'] ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-dark" 
                                                                onclick="deleteReview(<?= $review['review_id'] ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <p class="text-muted">Không tìm thấy đánh giá nào</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="d-flex justify-content-center mt-4">
                                <nav>
                                    <ul class="pagination">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($keyword) ?>&rating=<?= $rating ?>&status=<?= $status ?>">Trước</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($keyword) ?>&rating=<?= $rating ?>&status=<?= $status ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($keyword) ?>&rating=<?= $rating ?>&status=<?= $status ?>">Sau</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Component Manager -->
    <script src="<?= $baseUrl ?>components/component-manager.js"></script>
    
    <!-- Page Configuration -->
    <script>
        window.pageConfig = {
            sidebar: {
                brandName: 'JEWELLERY',
                activePage: '<?= $currentPage ?>',
                links: {
                    dashboard: '../index.php',
                    products: 'products.php',
                    orders: 'orders.php',
                    categories: 'categories.php',
                    customers: 'customers.php',
                    collections: 'collections.php',
                    reviews: 'reviews.php'
                }
            },
            header: {
                title: 'Đánh giá',
                breadcrumb: 'Home > Đánh giá',
                showDateRange: false
            }
        };

        // Functions
        function searchReviews() {
            const keyword = document.getElementById('searchInput').value;
            const rating = document.getElementById('ratingFilter').value;
            const status = document.getElementById('statusFilter').value;
            window.location.href = `?search=${encodeURIComponent(keyword)}&rating=${rating}&status=${status}`;
        }

        function filterByRating() {
            const keyword = document.getElementById('searchInput').value;
            const rating = document.getElementById('ratingFilter').value;
            const status = document.getElementById('statusFilter').value;
            window.location.href = `?search=${encodeURIComponent(keyword)}&rating=${rating}&status=${status}`;
        }

        function filterByStatus() {
            const keyword = document.getElementById('searchInput').value;
            const rating = document.getElementById('ratingFilter').value;
            const status = document.getElementById('statusFilter').value;
            window.location.href = `?search=${encodeURIComponent(keyword)}&rating=${rating}&status=${status}`;
        }

        function approveReview(reviewId) {
            fetch('../ajax/approve-review.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `review_id=${reviewId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Duyệt review thành công!');
                    location.reload();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi duyệt review');
            });
        }

        function rejectReview(reviewId) {
            if (confirm('Bạn có chắc chắn muốn từ chối review này?')) {
                fetch('../ajax/reject-review.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `review_id=${reviewId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Từ chối review thành công!');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi từ chối review');
                });
            }
        }

        function deleteReview(reviewId) {
            if (confirm('Bạn có chắc chắn muốn xóa review này?')) {
                fetch('../ajax/delete-review.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `review_id=${reviewId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Xóa review thành công!');
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa review');
                });
            }
        }

        function bulkApprove() {
            const selectedIds = getSelectedReviewIds();
            if (selectedIds.length === 0) {
                alert('Vui lòng chọn reviews để duyệt');
                return;
            }
            
            if (confirm(`Duyệt ${selectedIds.length} reviews đã chọn?`)) {
                fetch('../ajax/bulk-approve-reviews.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `review_ids=${JSON.stringify(selectedIds)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Duyệt thành công ${selectedIds.length} reviews!`);
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi duyệt reviews');
                });
            }
        }

        function bulkReject() {
            const selectedIds = getSelectedReviewIds();
            if (selectedIds.length === 0) {
                alert('Vui lòng chọn reviews để từ chối');
                return;
            }
            
            if (confirm(`Từ chối ${selectedIds.length} reviews đã chọn?`)) {
                fetch('../ajax/bulk-reject-reviews.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `review_ids=${JSON.stringify(selectedIds)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Từ chối thành công ${selectedIds.length} reviews!`);
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi từ chối reviews');
                });
            }
        }

        function getSelectedReviewIds() {
            const checkboxes = document.querySelectorAll('.review-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.trim());
        }

        // Select all checkbox
        document.getElementById('selectAllReviews').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.review-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $baseUrl ?>assets/js/main.js"></script>
    <script src="<?= $baseUrl ?>assets/js/reviews.js"></script>
</body>
</html>
