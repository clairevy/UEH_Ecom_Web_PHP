// Reviews page specific JavaScript
window.pageConfig = window.pageConfig || {};
window.pageConfig.sidebar = window.pageConfig.sidebar || {};
window.pageConfig.sidebar.activePage = 'reviews';

document.addEventListener('DOMContentLoaded', function() {
    console.log('Reviews page JavaScript loaded');
    
    // Initialize reviews page functionality
    initializeReviewsPage();
});

function initializeReviewsPage() {
    // Search functionality
    const searchInput = document.getElementById('reviewSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const productName = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const customerName = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                const content = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';
                
                if (productName.includes(searchTerm) || customerName.includes(searchTerm) || content.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Status filter functionality
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const selectedStatus = this.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const statusBadge = row.querySelector('.badge');
                const statusText = statusBadge?.textContent.toLowerCase() || '';
                
                if (selectedStatus === 'all') {
                    row.style.display = '';
                } else if (statusText.includes(selectedStatus)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Rating filter functionality
    const ratingFilter = document.getElementById('ratingFilter');
    if (ratingFilter) {
        ratingFilter.addEventListener('change', function() {
            const selectedRating = this.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const ratingStars = row.querySelector('td:nth-child(4)');
                const ratingText = ratingStars?.textContent || '';
                
                if (selectedRating === 'all') {
                    row.style.display = '';
                } else if (ratingText.includes(selectedRating)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Bulk actions
    const bulkActions = document.querySelectorAll('.bulk-action');
    bulkActions.forEach(action => {
        action.addEventListener('click', function() {
            const selectedRows = document.querySelectorAll('tbody input[type="checkbox"]:checked');
            
            if (selectedRows.length === 0) {
                alert('Vui lòng chọn ít nhất một đánh giá!');
                return;
            }
            
            const actionType = this.dataset.action;
            if (confirm(`Bạn có chắc chắn muốn ${actionType} ${selectedRows.length} đánh giá?`)) {
                // Implement bulk action logic here
                console.log(`Bulk ${actionType} for ${selectedRows.length} reviews`);
            }
        });
    });
}

// Review management functions
function viewReview(reviewId) {
    window.location.href = `index.php?url=review-details&id=${reviewId}`;
}

function approveReview(reviewId) {
    if (confirm('Bạn có chắc chắn muốn duyệt đánh giá này?')) {
        window.location.href = `index.php?url=reviews&action=approve&id=${reviewId}`;
    }
}

function rejectReview(reviewId) {
    if (confirm('Bạn có chắc chắn muốn từ chối đánh giá này?')) {
        window.location.href = `index.php?url=reviews&action=reject&id=${reviewId}`;
    }
}

function deleteReview(reviewId) {
    if (confirm('Bạn có chắc chắn muốn xóa đánh giá này?')) {
        window.location.href = `index.php?url=reviews&action=delete&id=${reviewId}`;
    }
}

function replyReview(reviewId) {
    const reply = prompt('Nhập phản hồi cho đánh giá:');
    if (reply && reply.trim()) {
        // Implement reply logic here
        console.log(`Replying to review ${reviewId}: ${reply}`);
    }
}

// Bulk actions
function bulkApprove() {
    const checked = document.querySelectorAll('tbody input[type="checkbox"]:checked');
    if (checked.length === 0) {
        alert('Vui lòng chọn ít nhất một đánh giá!');
        return;
    }
    if (confirm(`Duyệt ${checked.length} đánh giá?`)) {
        // Implement bulk approve logic
        console.log(`Bulk approving ${checked.length} reviews`);
    }
}

function bulkReject() {
    const checked = document.querySelectorAll('tbody input[type="checkbox"]:checked');
    if (checked.length === 0) {
        alert('Vui lòng chọn ít nhất một đánh giá!');
        return;
    }
    if (confirm(`Từ chối ${checked.length} đánh giá?`)) {
        // Implement bulk reject logic
        console.log(`Bulk rejecting ${checked.length} reviews`);
    }
}
