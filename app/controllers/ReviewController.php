<?php
/**
 * ReviewController - Admin Review Management
 */
class AdminReviewController extends BaseController {
    private $reviewModel;

    public function __construct() {
        $this->reviewModel = new Review();
    }

    public function index() {
        try {
            $reviews = $this->reviewModel->getAll();

            $data = [
                'title' => 'Quản lý đánh giá',
                'reviews' => $reviews
            ];

            $this->view('admin/pages/reviews', $data);

        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function approve($id) {
        try {
            if ($this->reviewModel->updateStatus($id, 'approved')) {
                $_SESSION['success'] = 'Duyệt đánh giá thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi duyệt đánh giá!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        $this->index();
    }

    public function reject($id) {
        try {
            if ($this->reviewModel->updateStatus($id, 'rejected')) {
                $_SESSION['success'] = 'Từ chối đánh giá thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi từ chối đánh giá!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        $this->index();
    }

    public function delete($id) {
        try {
            if ($this->reviewModel->delete($id)) {
                $_SESSION['success'] = 'Xóa đánh giá thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa đánh giá!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        $this->index();
    }
}
