<?php
date_default_timezone_set("Asia/Ho_Chi_Minh");
session_start();
ob_start(); //tránh lỗi khi dùng hàm liên quan header, côkie (kiểm soát việc xuất dữ liệu ra trình duyệt)

// Enable error logging for debugging, but don't display errors
error_reporting(E_ALL);
ini_set('display_errors', 0);  // Don't show errors in output
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Create logs directory if it doesn't exist
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0777, true);
}
foreach (glob(__DIR__ . '/configs/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/core/*.php') as $file) {
    require_once $file;
}

// Nạp các file helper
foreach (glob(__DIR__ . '/helpers/*.php') as $file) {
    require_once $file;
}

// Nạp các file model
foreach (glob(__DIR__ . '/app/models/*.php') as $file) {
    require_once $file;
}
// Nạp các file service
foreach (glob(__DIR__ . '/app/services/*.php') as $file) {
    require_once $file;
}

// Nạp các file controller
foreach (glob(__DIR__ . '/app/controllers/*.php') as $file) {
    require_once $file;
}

// Khởi tạo Router để xử lý URL
$router = new Route();
?>