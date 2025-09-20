<?php
/**
 * Script để chạy seed data
 * Truy cập: http://localhost/Ecom_website/run_seed.php
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Seed Data - JEWELRY</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
    </style>
</head>
<body>
    <h1>Seed Data cho JEWELRY Website</h1>";

try {
    echo "<p class='info'>Đang chạy seed data...</p>";
    
    // Include và chạy seed script
    ob_start();
    include __DIR__ . '/seed_data.php';
    $output = ob_get_clean();
    
    echo "<pre class='success'>" . htmlspecialchars($output) . "</pre>";
    
    echo "<div style='margin-top: 30px; padding: 20px; background: #f0f8ff; border-radius: 5px;'>
        <h3>✅ Hoàn thành!</h3>
        <p>Dữ liệu mẫu đã được tạo thành công. Bây giờ bạn có thể:</p>
        <ul>
            <li><a href='/Ecom_website/customer'>Xem trang chủ</a></li>
            <li><a href='/Ecom_website/customer/products'>Xem danh sách sản phẩm</a></li>
            <li><a href='/Ecom_website/customer/product-detail/1'>Xem chi tiết sản phẩm đầu tiên</a></li>
        </ul>
    </div>";
    
} catch (Exception $e) {
    echo "<p class='error'>Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>
