<?php
/**
 * Test Script: Kiểm tra route order-details có hoạt động không
 */

// Load configs
require_once __DIR__ . '/../configs/config.php';
require_once __DIR__ . '/../configs/database.php';
require_once __DIR__ . '/../core/BaseModel.php';
require_once __DIR__ . '/../app/models/Order.php';

echo "<h1>🔍 TEST ORDER-DETAILS ROUTE</h1>";
echo "<hr>";

// Test 1: Kiểm tra có orders trong database không
echo "<h2>1. Kiểm tra Orders trong Database:</h2>";
try {
    $db = Database::getInstance();
    $db->query("SELECT order_id, full_name, email, order_status, total_amount, created_at FROM orders ORDER BY created_at DESC LIMIT 5");
    $orders = $db->resultSet();
    
    if ($orders) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
        echo "✅ Tìm thấy " . count($orders) . " đơn hàng<br><br>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><th>Order ID</th><th>Customer</th><th>Email</th><th>Status</th><th>Total</th><th>Test Link</th></tr>";
        
        foreach ($orders as $order) {
            echo "<tr>";
            echo "<td>#{$order->order_id}</td>";
            echo "<td>{$order->full_name}</td>";
            echo "<td>{$order->email}</td>";
            echo "<td>{$order->order_status}</td>";
            echo "<td>" . number_format($order->total_amount, 0, ',', '.') . " VND</td>";
            echo "<td><a href='../admin/index.php?url=order-details&id={$order->order_id}' style='background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>TEST VIEW</a></td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
        echo "❌ KHÔNG có đơn hàng nào trong database!<br>";
        echo "Bạn cần thêm đơn hàng test trước.";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
    echo "❌ Lỗi database: " . $e->getMessage();
    echo "</div>";
}

echo "<hr>";

// Test 2: Test route trực tiếp
echo "<h2>2. Test Route Trực Tiếp:</h2>";
echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; color: #0c5460;'>";
echo "Click link này để test route order-details:<br><br>";
echo "<a href='../admin/index.php?url=order-details&id=1' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px;'>";
echo "🔗 TEST: View Order #1";
echo "</a>";
echo "</div>";

echo "<hr>";

// Test 3: Test JavaScript
echo "<h2>3. Test JavaScript editOrder():</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404;'>";
echo "<p>Nhấn button bên dưới để test function editOrder():</p>";
echo "<button onclick='testEditOrder()' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>";
echo "🧪 TEST editOrder(1)";
echo "</button>";
echo "</div>";

?>

<script>
// Load orders.js để test
document.write('<script src="../app/views/admin/assets/js/orders.js"><\/script>');

function testEditOrder() {
    console.log('Testing editOrder function...');
    
    if (typeof editOrder === 'function') {
        alert('✅ Function editOrder() TỒN TẠI!\n\nBây giờ sẽ chuyển sang order-details page...');
        editOrder(1);
    } else {
        alert('❌ Function editOrder() KHÔNG tồn tại!\n\nFile orders.js chưa được load đúng.');
    }
}
</script>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #f5f5f5;
}
h1, h2 { color: #333; }
kbd {
    background: #eee;
    border: 1px solid #ccc;
    border-radius: 3px;
    padding: 2px 6px;
    font-family: monospace;
}
</style>

<br><br>
<a href="../admin/index.php?url=orders" style="display: inline-block; background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
    ← Quay lại Orders Page
</a>


