<?php
/**
 * Review Model
 * Quản lý đánh giá sản phẩm theo OOP MVC
 */

require_once __DIR__ . '/BaseModel.php';

class ReviewModel extends BaseModel {
    protected $table = 'product_reviews';
    protected $primaryKey = 'review_id';

    /**
     * Lấy reviews với thông tin chi tiết
     */
    public function getReviewsWithDetails($limit = 10, $offset = 0) {
        // Đơn giản hóa - chỉ lấy từ reviews table với thông tin cơ bản
        $sql = "SELECT r.*, 'Unknown Product' as product_name, 'Customer' as customer_name
                FROM product_reviews r
                WHERE r.status != 'deleted'
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tìm kiếm reviews
     */
    public function searchReviews($keyword, $limit = 10, $offset = 0) {
        $sql = "SELECT r.*, 'Unknown Product' as product_name, 'Customer' as customer_name
                FROM product_reviews r
                WHERE r.status != 'deleted' 
                AND (r.comment LIKE :keyword OR r.title LIKE :keyword)
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        $keyword = "%{$keyword}%";
        $stmt->bindParam(':keyword', $keyword);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy reviews theo rating
     */
    public function getReviewsByRating($rating, $limit = 10, $offset = 0) {
        $sql = "SELECT r.*, 'Unknown Product' as product_name, 'Customer' as customer_name
                FROM product_reviews r
                WHERE r.status != 'deleted' AND r.rating = :rating
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy reviews theo trạng thái
     */
    public function getReviewsByStatus($status, $limit = 10, $offset = 0) {
        $sql = "SELECT r.*, 'Unknown Product' as product_name, 'Customer' as customer_name
                FROM product_reviews r
                WHERE r.status = :status
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Cập nhật trạng thái review
     */
    public function updateReviewStatus($id, $status) {
        $sql = "UPDATE product_reviews SET status = :status, updated_at = NOW() WHERE review_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    /**
     * Xóa review (soft delete)
     */
    public function deleteReview($id) {
        $sql = "UPDATE product_reviews SET status = 'deleted', updated_at = NOW() WHERE review_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Approve review
     */
    public function approveReview($id) {
        $sql = "UPDATE product_reviews SET status = 'approved', updated_at = NOW() WHERE review_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Reject review
     */
    public function rejectReview($id) {
        $sql = "UPDATE product_reviews SET status = 'rejected', updated_at = NOW() WHERE review_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Lấy thống kê reviews
     */
    public function getReviewStats() {
        $stats = [];
        
        // Tổng reviews
        $sql = "SELECT COUNT(*) as total FROM product_reviews WHERE status != 'deleted'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stats['total'] = $stmt->fetch()['total'];
        
        // Reviews pending
        $sql = "SELECT COUNT(*) as pending FROM product_reviews WHERE status = 'pending'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stats['pending'] = $stmt->fetch()['pending'];
        
        // Reviews approved
        $sql = "SELECT COUNT(*) as approved FROM product_reviews WHERE status = 'approved'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stats['approved'] = $stmt->fetch()['approved'];
        
        // Reviews rejected
        $sql = "SELECT COUNT(*) as rejected FROM product_reviews WHERE status = 'rejected'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stats['rejected'] = $stmt->fetch()['rejected'];
        
        return $stats;
    }

    /**
     * Lấy đánh giá trung bình
     */
    public function getAverageRating() {
        $sql = "SELECT AVG(rating) as avg_rating FROM product_reviews WHERE status = 'approved'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return round($result['avg_rating'], 1);
    }

    /**
     * Tạo review mới (cho admin test)
     */
    public function createReview($data) {
        $sql = "INSERT INTO product_reviews (title, comment, rating, product_id, customer_id, status, created_at) 
                VALUES (:title, :comment, :rating, :product_id, :customer_id, :status, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':comment', $data['comment']);
        $stmt->bindParam(':rating', $data['rating'], PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $data['product_id'], PDO::PARAM_INT);
        $stmt->bindParam(':customer_id', $data['customer_id'], PDO::PARAM_INT);
        $stmt->bindParam(':status', $data['status']);
        // Removed is_active parameter
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
}
?>
