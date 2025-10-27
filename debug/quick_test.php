<?php
/**
 * QUICK TEST - Kiểm tra nhanh
 */

echo "<h1>🔍 QUICK TEST</h1>";
echo "<hr>";

// Test 1: Kiểm tra database
echo "<h2>1. Database Test:</h2>";
try {
    require_once __DIR__ . '/../configs/config.php';
    require_once __DIR__ . '/../configs/database.php';
    
    $db = Database::getInstance();
    $db->query("SELECT COUNT(*) as count FROM orders");
    $result = $db->single();
    
    if ($result->count > 0) {
        echo "✅ Database có {$result->count} orders<br>";
        
        // Lấy order đầu tiên
        $db->query("SELECT order_id FROM orders ORDER BY order_id ASC LIMIT 1");
        $order = $db->single();
        echo "✅ Order ID đầu tiên: #{$order->order_id}<br>";
        
        echo "<br><a href='../admin/index.php?url=order-details&id={$order->order_id}' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
        echo "📄 TEST: View Order #{$order->order_id}";
        echo "</a>";
        
    } else {
        echo "❌ Database KHÔNG có orders<br>";
        echo "<a href='create_test_order.php' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
        echo "➕ Tạo Order Test";
        echo "</a>";
    }
    
} catch (Exception $e) {
    echo "❌ Lỗi database: " . $e->getMessage();
}

echo "<hr>";

// Test 2: Kiểm tra file orders.js
echo "<h2>2. JavaScript Test:</h2>";
$jsFile = '../app/views/admin/assets/js/orders.js';
if (file_exists($jsFile)) {
    echo "✅ File orders.js tồn tại<br>";
    
    $content = file_get_contents($jsFile);
    if (strpos($content, 'function editOrder') !== false) {
        echo "✅ Function editOrder() có trong file<br>";
    } else {
        echo "❌ Function editOrder() KHÔNG có trong file<br>";
    }
} else {
    echo "❌ File orders.js KHÔNG tồn tại<br>";
}

echo "<hr>";

// Test 3: Test trực tiếp
echo "<h2>3. Direct Test:</h2>";
echo "<button onclick='testDirect()' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>";
echo "🧪 Test editOrder(1)";
echo "</button>";

echo "<script src='../app/views/admin/assets/js/orders.js'></script>";
echo "<script>";
echo "function testDirect() {";
echo "    console.log('Testing...');";
echo "    if (typeof editOrder === 'function') {";
echo "        alert('✅ Function OK! Chuyển sang order-details...');";
echo "        editOrder(1);";
echo "    } else {";
echo "        alert('❌ Function KHÔNG tồn tại!');";
echo "        console.log('editOrder type:', typeof editOrder);";
echo "    }";
echo "}";
echo "</script>";

echo "<hr>";

// Test 4: Orders page link
echo "<h2>4. Orders Page:</h2>";
echo "<a href='../admin/index.php?url=orders' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
echo "📋 Vào Orders Page";
echo "</a>";

?>

<style>
body { font-family: Arial; max-width: 800px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
h1, h2 { color: #333; }
</style>

