<?php
/**
 * Admin Front Controller
 * Tương tự index.php nhưng dành cho admin panel
 */

date_default_timezone_set("Asia/Ho_Chi_Minh");
session_start();
ob_start(); // Tránh lỗi khi dùng hàm liên quan header, cookie

// Include configuration files
foreach (glob(__DIR__ . '/../configs/*.php') as $file) {
    require_once $file;
}

// Include core files
foreach (glob(__DIR__ . '/../core/*.php') as $file) {
    require_once $file;
}

// Nạp các file model
foreach (glob(__DIR__ . '/../app/models/*.php') as $file) {
    require_once $file;
}

// Nạp các file service
foreach (glob(__DIR__ . '/../app/services/*.php') as $file) {
    require_once $file;
}

// Register error handlers
ErrorHandler::register();

// Khởi tạo AdminRouter để xử lý URL admin
$adminRouter = new AdminRouter();
?>
