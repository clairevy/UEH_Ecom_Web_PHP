<?php
/**
 * Reviews View - Pure MVC View
 * Hiển thị data từ database, không có hardcode
 */

// Biến được truyền từ Controller:
// $reviews - danh sách reviews từ database
// $stats - thống kê reviews từ database
// $flashMessage - thông báo flash
?>
                <!-- Flash Messages -->
                <?php if (!empty($flashMessage)): ?>
                    <?= $flashMessage ?>
                <?php endif; ?>

                <!-- Reviews Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828970.png" alt="Total Reviews" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-primary"><?= $stats['total_reviews'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Tổng Đánh Giá</h6>
                                <p class="text-muted small mb-0">+15 tuần này</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828884.png" alt="Pending Reviews" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-warning"><?= $stats['pending_reviews'] ?? 0 ?></h3>
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
                                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828640.png" alt="Average Rating" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-success"><?= $stats['average_rating'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Đánh Giá Trung Bình</h6>
                                <p class="text-muted small mb-0">⭐⭐⭐⭐⭐</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828843.png" alt="Response Rate" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-info"><?= $stats['response_rate'] ?? 0 ?>%</h3>
                                </div>
                                <h6 class="fw-bold">Tỷ Lệ Phản Hồi</h6>
                                <p class="text-muted small mb-0">Đã trả lời đánh giá</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews Management Table -->
                <div class="table-card">
                    <div class="table-header">
                        <h5 class="table-title">Quản Lý Đánh Giá</h5>
                        <div class="d-flex gap-2">
                            <!-- Filter -->
                            <select class="form-select form-select-sm" style="width: 150px;" id="statusFilter">
                                <option value="all">Tất cả trạng thái</option>
                                <option value="pending">Chờ duyệt</option>
                                <option value="approved">Đã duyệt</option>
                                <option value="rejected">Từ chối</option>
                            </select>
                            
                            <!-- Search -->
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm đánh giá..." style="width: 200px; padding-left: 35px;" id="reviewSearch">
                                <img src="https://cdn-icons-png.flaticon.com/512/751/751463.png" alt="Search" width="16" height="16" class="position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);">
                            </div>
                            
                            <!-- More Actions -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2311/2311524.png" alt="More" width="16" height="16">
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="bulkApprove()">Duyệt hàng loạt</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="bulkReject()">Từ chối hàng loạt</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#">Xuất Excel</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" id="reviewsTable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>ID</th>
                                    <th>Khách Hàng</th>
                                    <th>Sản Phẩm</th>
                                    <th>Đánh Giá</th>
                                    <th>Nội Dung</th>
                                    <th>Trạng Thái</th>
                                    <th>Ngày Tạo</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($reviews)): ?>
                                    <?php foreach ($reviews as $review): ?>
                                        <tr>
                                            <td><input type="checkbox" class="form-check-input row-checkbox"></td>
                                            <td class="fw-bold">#<?= $review->review_id ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://images.unsplash.com/photo-1494790108755-2616b612b804?w=32&h=32&fit=crop&crop=face" alt="Customer" width="32" height="32" class="rounded-circle me-2">
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($review->customer_name ?? 'N/A') ?></div>
                                                        <div class="small text-muted"><?= htmlspecialchars($review->customer_email ?? 'N/A') ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=40&h=40&fit=crop" alt="Product" width="40" height="40" class="rounded me-2">
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($review->product_name ?? 'N/A') ?></div>
                                                        <div class="small text-muted">SKU: <?= htmlspecialchars($review->product_sku ?? 'N/A') ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="review-rating">
                                                    <?php
                                                    $rating = (int)$review->rating;
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($i <= $rating) {
                                                            echo '⭐';
                                                        } else {
                                                            echo '☆';
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <div class="small text-muted"><?= $rating ?>/5 sao</div>
                                            </td>
                                            <td>
                                                <div class="review-text" title="<?= htmlspecialchars($review->comment ?? '') ?>">
                                                    <?= htmlspecialchars(substr($review->comment ?? '', 0, 50)) ?><?= strlen($review->comment ?? '') > 50 ? '...' : '' ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                switch ($review->status) {
                                                    case 'pending':
                                                        $statusClass = 'badge-pending badge-custom';
                                                        $statusText = 'Chờ duyệt';
                                                        break;
                                                    case 'approved':
                                                        $statusClass = 'badge-delivered badge-custom';
                                                        $statusText = 'Đã duyệt';
                                                        break;
                                                    case 'rejected':
                                                        $statusClass = 'badge-canceled badge-custom';
                                                        $statusText = 'Từ chối';
                                                        break;
                                                    case 'hidden':
                                                        $statusClass = 'badge-secondary badge-custom';
                                                        $statusText = 'Ẩn';
                                                        break;
                                                    default:
                                                        $statusClass = 'badge-secondary badge-custom';
                                                        $statusText = 'N/A';
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($review->created_at)) ?></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/2311/2311524.png" alt="Actions" width="16" height="16">
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="viewReview(<?= $review->review_id ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/709/709612.png" alt="View" width="16" height="16" class="me-2">
                                                            Xem chi tiết
                                                        </a></li>
                                                        <?php if ($review->status !== 'approved'): ?>
                                                        <li><a class="dropdown-item text-success" href="#" onclick="approveReview(<?= $review->review_id ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" alt="Approve" width="16" height="16" class="me-2">
                                                            Duyệt
                                                        </a></li>
                                                        <?php endif; ?>
                                                        <?php if ($review->status !== 'rejected'): ?>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="rejectReview(<?= $review->review_id ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828843.png" alt="Reject" width="16" height="16" class="me-2">
                                                            Từ chối
                                                        </a></li>
                                                        <?php endif; ?>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item" href="#" onclick="replyReview(<?= $review->review_id ?>)">
                                                            <img src="https://cdn-icons-png.flaticon.com/512/1946/1946436.png" alt="Reply" width="16" height="16" class="me-2">
                                                            Phản hồi
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="No Data" width="48" height="48" class="mb-3 opacity-50">
                                                <p>Chưa có đánh giá nào</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div id="pagination-container"></div>
            </main>
        </div>
    </div>

    <!-- Component Manager -->
    <script src="app/views/admin/components/component-manager.js"></script>
    
    <!-- Page Configuration -->
    <script>
        window.pageConfig = {
            sidebar: {
                brandName: 'KICKS',
                activePage: 'reviews',
                links: {
                    dashboard: 'index.php?url=dashboard',
                    products: 'index.php?url=products',
                    categories: 'index.php?url=categories',
                    collections: 'index.php?url=collections',
                    orders: 'index.php?url=orders',
                    customers: 'index.php?url=customers',
                    reviews: 'index.php?url=reviews'
                },
                categories: [
                    { name: 'Vòng cổ', count: 12 },
                    { name: 'Vòng tay', count: 10 },
                    { name: 'Bông tai', count: 13 },
                    { name: 'Nhẫn', count: 4 },
                    { name: 'Lắc chân', count: 6 }
                ],
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: 'Đánh Giá',
                breadcrumb: 'Home > Đánh Giá > Quản lý đánh giá'
            }
        };

        // Review management functions
        function viewReview(reviewId) {
            window.location.href = 'index.php?url=review-details&id=' + reviewId;
        }

        function approveReview(reviewId) {
            if (confirm('Bạn có chắc chắn muốn duyệt đánh giá này?')) {
                window.location.href = 'index.php?url=reviews&action=approve&id=' + reviewId;
            }
        }

        function rejectReview(reviewId) {
            if (confirm('Bạn có chắc chắn muốn từ chối đánh giá này?')) {
                window.location.href = 'index.php?url=reviews&action=reject&id=' + reviewId;
            }
        }

        function hideReview(reviewId) {
            if (confirm('Bạn có chắc chắn muốn ẩn đánh giá này?')) {
                window.location.href = 'index.php?url=reviews&action=hide&id=' + reviewId;
            }
        }

        function replyReview(reviewId) {
            // Implementation for replying to review
            console.log('Replying to review:', reviewId);
        }

        function bulkApprove() {
            const selectedReviews = getSelectedReviews();
            if (selectedReviews.length === 0) {
                alert('Vui lòng chọn ít nhất một đánh giá!');
                return;
            }
            if (confirm(`Bạn có chắc chắn muốn duyệt ${selectedReviews.length} đánh giá đã chọn?`)) {
                // Implementation for bulk approve
                console.log('Bulk approving reviews:', selectedReviews);
            }
        }

        function bulkReject() {
            const selectedReviews = getSelectedReviews();
            if (selectedReviews.length === 0) {
                alert('Vui lòng chọn ít nhất một đánh giá!');
                return;
            }
            if (confirm(`Bạn có chắc chắn muốn từ chối ${selectedReviews.length} đánh giá đã chọn?`)) {
                // Implementation for bulk reject
                console.log('Bulk rejecting reviews:', selectedReviews);
            }
        }

        function getSelectedReviews() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.replace('#', ''));
        }

        // Search functionality
        document.getElementById('reviewSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#reviewsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Filter functionality
        document.getElementById('statusFilter').addEventListener('change', function(e) {
            const filterValue = e.target.value;
            const rows = document.querySelectorAll('#reviewsTable tbody tr');
            
            rows.forEach(row => {
                if (filterValue === 'all') {
                    row.style.display = '';
                } else {
                    const statusBadge = row.querySelector('.badge');
                    const statusText = statusBadge ? statusBadge.textContent.toLowerCase() : '';
                    const shouldShow = statusText.includes(filterValue);
                    row.style.display = shouldShow ? '' : 'none';
                }
            });
        });

        // Select all checkbox functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Initialize pagination
        document.addEventListener('DOMContentLoaded', function() {
            const paginationContainer = document.getElementById('pagination-container');
            if (paginationContainer && window.ComponentManager) {
                window.ComponentManager.renderPagination(paginationContainer, {
                    ariaLabel: 'Reviews pagination'
                });
            }
        });
    </script>