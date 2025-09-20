<?php

class HomeController extends BaseController {
    
    public function __construct() {
        // Có thể khởi tạo các model cần thiết ở đây
    }

    /**
     * Trang chủ mặc định
     */
    public function index() {
        echo '<h1>Chào mừng đến với Ecom Website!</h1>';
        echo '<p>Đây là trang chủ của website thương mại điện tử.</p>';
        echo '<a href="/Ecom_website/users/signup">Đăng ký</a> | <a href="/Ecom_website/users/login">Đăng nhập</a>';
    }

    /**
     * Trang giới thiệu
     */
    public function about() {
        echo '<h1>Giới thiệu về chúng tôi</h1>';
        echo '<p>Đây là trang giới thiệu.</p>';
    }

    /**
     * Trang liên hệ
     */
    public function contact() {
        echo '<h1>Liên hệ</h1>';
        echo '<p>Thông tin liên hệ của chúng tôi.</p>';
    }
}