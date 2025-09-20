<?php
date_default_timezone_set("Asia/Ho_Chi_Minh");
session_start();
ob_start(); //tránh lỗi khi dùng hàm liên quan header, côkie (kiểm soát việc xuất dữ liệu ra trình duyệt)
foreach (glob(__DIR__ . '/configs/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/core/*.php') as $file) {
    require_once $file;
}

// Nạp các file model
foreach (glob(__DIR__ . '/app/models/*.php') as $file) {
    require_once $file;
}

// Khởi tạo Router để xử lý URL
$router = new Route();
?>