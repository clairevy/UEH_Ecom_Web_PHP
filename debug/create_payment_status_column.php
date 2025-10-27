<?php
/**
 * Script TẠO COLUMN payment_status
 * Chạy file này 1 lần để thêm column vào database
 */

// Load config
require_once __DIR__ . '/../configs/config.php';
require_once __DIR__ . '/../configs/database.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo Payment Status Column</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .success { background: #d4edda; padding: 20px; border-radius: 5px; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; padding: 20px; border-radius: 5px; color: #721c24; border-left: 4px solid #dc3545; }
        .info { background: #d1ecf1; padding: 20px; border-radius: 5px; color: #0c5460; border-left: 4px solid #17a2b8; }
        h2 { color: #333; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>

<h2>🔧 TẠO COLUMN PAYMENT_STATUS</h2>

<?php
try {
    $db = Database::getInstance();
    
    echo "<div class='info'>";
    echo "<strong>📋 Đang kiểm tra...</strong><br>";
    echo "Database: <code>" . _DB . "</code><br>";
    echo "Table: <code>orders</code><br>";
    echo "Column: <code>payment_status</code>";
    echo "</div>";
    
    // Bước 1: Kiểm tra table orders có tồn tại không
    $checkTable = "SHOW TABLES LIKE 'orders'";
    $db->query($checkTable);
    $tableExists = $db->single();
    
    if (!$tableExists) {
        throw new Exception("Table 'orders' không tồn tại trong database!");
    }
    
    echo "<div class='success'>";
    echo "✅ Table 'orders' tồn tại";
    echo "</div>";
    
    // Bước 2: Kiểm tra column đã tồn tại chưa
    $checkColumn = "SHOW COLUMNS FROM orders LIKE 'payment_status'";
    $db->query($checkColumn);
    $columnExists = $db->single();
    
    if ($columnExists) {
        echo "<div class='info'>";
        echo "ℹ️ <strong>Column 'payment_status' ĐÃ TỒN TẠI!</strong><br>";
        echo "Không cần tạo lại.<br>";
        echo "Type: <code>" . $columnExists->Type . "</code><br>";
        echo "Default: <code>" . ($columnExists->Default ?? 'NULL') . "</code>";
        echo "</div>";
    } else {
        // Bước 3: Tạo column mới
        echo "<div class='info'>";
        echo "⚙️ Đang tạo column 'payment_status'...";
        echo "</div>";
        
        $createColumn = "ALTER TABLE orders 
                        ADD COLUMN payment_status ENUM('paid', 'unpaid') 
                        NOT NULL DEFAULT 'unpaid' 
                        COMMENT 'Trạng thái thanh toán: paid/unpaid'
                        AFTER order_status";
        
        $db->query($createColumn);
        $db->execute();
        
        echo "<div class='success'>";
        echo "✅ <strong>TẠO COLUMN THÀNH CÔNG!</strong><br><br>";
        echo "Column đã được thêm vào table 'orders'<br>";
        echo "Type: <code>ENUM('paid', 'unpaid')</code><br>";
        echo "Default: <code>unpaid</code><br>";
        echo "Position: Sau column 'order_status'";
        echo "</div>";
        
        // Bước 4: Verify
        echo "<div class='info'>";
        echo "🔍 <strong>Verify lại:</strong><br>";
        
        $verify = "SHOW COLUMNS FROM orders LIKE 'payment_status'";
        $db->query($verify);
        $result = $db->single();
        
        if ($result) {
            echo "✅ Column đã được tạo thành công!<br>";
            echo "Field: <code>" . $result->Field . "</code><br>";
            echo "Type: <code>" . $result->Type . "</code><br>";
            echo "Default: <code>" . $result->Default . "</code>";
        }
        echo "</div>";
        
        // Bước 5: Update các order cũ
        echo "<div class='info'>";
        echo "📝 <strong>Cập nhật dữ liệu cũ:</strong><br>";
        
        // Set payment_status = 'paid' cho các order đã delivered/shipped
        $updateSql = "UPDATE orders 
                     SET payment_status = 'paid' 
                     WHERE order_status IN ('delivered', 'shipped', 'paid')";
        $db->query($updateSql);
        $db->execute();
        $updatedRows = $db->rowCount();
        
        echo "✅ Đã cập nhật $updatedRows đơn hàng sang 'paid'";
        echo "</div>";
    }
    
    // Hiển thị cấu trúc table
    echo "<h3>📊 Cấu trúc table 'orders':</h3>";
    $showColumns = "SHOW COLUMNS FROM orders";
    $db->query($showColumns);
    $columns = $db->resultSet();
    
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%; background: white;'>";
    echo "<tr style='background: #007bff; color: white;'>";
    echo "<th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>";
    echo "</tr>";
    
    foreach ($columns as $col) {
        $highlight = $col->Field === 'payment_status' ? "style='background: #d4edda;'" : "";
        echo "<tr $highlight>";
        echo "<td><strong>{$col->Field}</strong></td>";
        echo "<td><code>{$col->Type}</code></td>";
        echo "<td>{$col->Null}</td>";
        echo "<td>{$col->Key}</td>";
        echo "<td><code>" . ($col->Default ?? 'NULL') . "</code></td>";
        echo "<td>{$col->Extra}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "❌ <strong>LỖI:</strong> " . htmlspecialchars($e->getMessage()) . "<br><br>";
    echo "<strong>Giải pháp:</strong><br>";
    echo "1. Kiểm tra XAMPP/MySQL đã chạy chưa<br>";
    echo "2. Kiểm tra database '<code>" . _DB . "</code>' đã tồn tại chưa<br>";
    echo "3. Kiểm tra quyền ALTER TABLE của user MySQL<br>";
    echo "4. Thử chạy SQL trực tiếp trong phpMyAdmin:<br>";
    echo "<code style='display: block; margin-top: 10px; padding: 10px; background: white;'>";
    echo "ALTER TABLE orders ADD COLUMN payment_status ENUM('paid','unpaid') DEFAULT 'unpaid' AFTER order_status;";
    echo "</code>";
    echo "</div>";
}
?>

<hr>

<h3>✅ Hoàn tất!</h3>
<p>Nếu column đã được tạo thành công, bạn có thể:</p>
<ul>
    <li>Chạy lại test script: <a href="test_payment_status.php">test_payment_status.php</a></li>
    <li>Vào trang orders: <a href="../admin/index.php?url=orders" class="btn">Vào Orders Page</a></li>
    <li>Test dropdown payment status</li>
</ul>

</body>
</html>


