<?php
/**
 * Script kiểm tra và debug payment status
 * Chạy file này để kiểm tra xem database có column payment_status chưa
 */

// Load config TRƯỚC (chứa constants: _DRIVER, _HOST, _DB, _USER, _PASSWORD)
require_once __DIR__ . '/../configs/config.php';
// Sau đó mới load database (sử dụng constants từ config)
require_once __DIR__ . '/../configs/database.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Payment Status Column</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h2 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        h3 {
            color: #555;
            margin-top: 30px;
        }
        .success {
            background: #d4edda;
            padding: 15px;
            border-radius: 5px;
            color: #155724;
            border-left: 4px solid #28a745;
            margin: 10px 0;
        }
        .error {
            background: #f8d7da;
            padding: 15px;
            border-radius: 5px;
            color: #721c24;
            border-left: 4px solid #dc3545;
            margin: 10px 0;
        }
        .warning {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            color: #856404;
            border-left: 4px solid #ffc107;
            margin: 10px 0;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            margin: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        th {
            background: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background: #f8f9fa;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
        .info-box {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<h2>🔍 KIỂM TRA PAYMENT STATUS COLUMN</h2>

<?php

try {
    $db = Database::getInstance();
    
    // 1. Kiểm tra column payment_status có tồn tại không
    echo "<h3>1. Kiểm tra column 'payment_status' trong table 'orders':</h3>";
    
    $sql = "SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, IS_NULLABLE 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = 'orders' 
              AND COLUMN_NAME = 'payment_status'";
    
    $db->query($sql);
    $columnInfo = $db->single();
    
    if ($columnInfo) {
        echo "<div class='success'>";
        echo "✅ <strong>Column tồn tại!</strong><br>";
        echo "Type: <code>{$columnInfo->COLUMN_TYPE}</code><br>";
        echo "Default: <code>{$columnInfo->COLUMN_DEFAULT}</code><br>";
        echo "Nullable: <code>{$columnInfo->IS_NULLABLE}</code>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "❌ <strong>Column KHÔNG tồn tại!</strong><br>";
        echo "Bạn cần chạy migration: <code>database/add_payment_status_column.sql</code>";
        echo "</div>";
        
        // Tạo column tự động
        echo "<h4>🔧 Tự động tạo column:</h4>";
        try {
            $createSql = "ALTER TABLE orders 
                         ADD COLUMN payment_status ENUM('paid', 'unpaid') DEFAULT 'unpaid' 
                         COMMENT 'Trạng thái thanh toán' 
                         AFTER order_status";
            $db->query($createSql);
            $db->execute();
            
            echo "<div class='success'>";
            echo "✅ Đã tạo column 'payment_status' thành công!<br>";
            echo "Reload trang để xem kết quả.";
            echo "</div>";
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "❌ Lỗi khi tạo column: " . htmlspecialchars($e->getMessage());
            echo "</div>";
        }
    }
    
    echo "<hr>";
    
    // 2. Kiểm tra dữ liệu orders
    echo "<h3>2. Kiểm tra dữ liệu orders (10 records mới nhất):</h3>";
    
    $sql = "SELECT order_id, order_status, payment_status, total_amount, created_at 
            FROM orders 
            ORDER BY created_at DESC 
            LIMIT 10";
    
    $db->query($sql);
    $orders = $db->resultSet();
    
    if ($orders) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>
                <th>Order ID</th>
                <th>Order Status</th>
                <th>Payment Status</th>
                <th>Total Amount</th>
                <th>Created At</th>
              </tr>";
        
        foreach ($orders as $order) {
            $paymentBadge = $order->payment_status === 'paid' 
                ? "<span style='color: green;'>✓ Đã thanh toán</span>" 
                : "<span style='color: orange;'>⚠️ Chưa thanh toán</span>";
            
            echo "<tr>
                    <td>#{$order->order_id}</td>
                    <td>{$order->order_status}</td>
                    <td>{$paymentBadge}</td>
                    <td>" . number_format($order->total_amount, 0, ',', '.') . " VND</td>
                    <td>{$order->created_at}</td>
                  </tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p><em>Chưa có đơn hàng nào trong database</em></p>";
    }
    
    echo "<hr>";
    
    // 3. Test update payment status
    echo "<h3>3. Test UPDATE payment status:</h3>";
    
    if (!empty($orders)) {
        $testOrder = $orders[0];
        $newStatus = $testOrder->payment_status === 'paid' ? 'unpaid' : 'paid';
        
        echo "<p>Thử update Order #{$testOrder->order_id} từ '{$testOrder->payment_status}' → '{$newStatus}'</p>";
        
        $updateSql = "UPDATE orders 
                     SET payment_status = :new_status 
                     WHERE order_id = :order_id";
        
        $db->query($updateSql);
        $db->bind(':new_status', $newStatus);
        $db->bind(':order_id', $testOrder->order_id);
        
        if ($db->execute()) {
            echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px; color: #155724;'>";
            echo "✅ Update thành công! Reload trang để xem kết quả.";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 10px; border-radius: 5px; color: #721c24;'>";
            echo "❌ Update thất bại!";
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "❌ <strong>Lỗi kết nối database:</strong> " . htmlspecialchars($e->getMessage());
    echo "<br><br><strong>Giải pháp:</strong><br>";
    echo "1. Kiểm tra XAMPP đã bật MySQL chưa<br>";
    echo "2. Kiểm tra database name trong <code>configs/config.php</code><br>";
    echo "3. Kiểm tra username/password MySQL";
    echo "</div>";
}

echo "<hr>";
echo "<h3>📋 Kết luận:</h3>";
echo "<ol>";
echo "<li>Nếu column tồn tại ✅ → Mọi thứ OK, có thể test dropdown</li>";
echo "<li>Nếu column không tồn tại ❌ → Chạy migration hoặc script đã tự động tạo</li>";
echo "<li>Test update thành công ✅ → Dropdown sẽ hoạt động</li>";
echo "</ol>";

?>

<div class="info-box">
    <h3>📚 Tài Liệu Tham Khảo:</h3>
    <ul>
        <li>Migration SQL: <code>database/add_payment_status_column.sql</code></li>
        <li>Hướng dẫn sửa lỗi: <code>docs/FIX_PAYMENT_STATUS_DROPDOWN.md</code></li>
        <li>Controller: <code>app/controllers/admin/OrdersController.php</code></li>
        <li>View: <code>app/views/admin/pages/orders.php</code></li>
    </ul>
</div>

<div style="margin-top: 30px;">
    <a href="../admin/index.php?url=orders" class="btn">← Quay lại Orders Page</a>
    <a href="test_payment_status.php" class="btn" style="background: #28a745; margin-left: 10px;">🔄 Reload Test</a>
</div>

</body>
</html>

