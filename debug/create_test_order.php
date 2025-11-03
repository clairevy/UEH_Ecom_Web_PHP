<?php
/**
 * Tạo Test Order - SIMPLE VERSION
 */

echo "<h1>➕ TẠO TEST ORDER</h1>";
echo "<hr>";

try {
    require_once __DIR__ . '/../configs/config.php';
    require_once __DIR__ . '/../configs/database.php';
    
    $db = Database::getInstance();
    
    // Tạo order test đơn giản (bao gồm payment_method)
    $sql = "INSERT INTO orders (user_id, full_name, email, phone, street, ward, province, country, payment_method, order_status, payment_status, shipping_fee, total_amount, created_at, updated_at) VALUES (NULL, 'Test User', 'test@test.com', '0901234567', '123 Test St', 'Test Ward', 'Test City', 'Vietnam', 'cod', 'pending', 'unpaid', 30000, 500000, NOW(), NOW())";
    
    $db->query($sql);
    $db->execute();
    $orderId = $db->lastInsertId();
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; color: #155724;'>";
    echo "<h2>✅ TẠO ORDER THÀNH CÔNG!</h2>";
    echo "<p><strong>Order ID:</strong> #{$orderId}</p>";
    echo "<p><strong>Customer:</strong> Test User</p>";
    echo "<p><strong>Total:</strong> 500,000 VND</p>";
    echo "</div>";
    
    echo "<br>";
    echo "<a href='../admin/index.php?url=order-details&id={$orderId}' style='display: inline-block; background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>";
    echo "📄 Xem Order #{$orderId}";
    echo "</a>";
    
    echo " ";
    
    echo "<a href='../admin/index.php?url=orders' style='display: inline-block; background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>";
    echo "📋 Vào Orders Page";
    echo "</a>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 5px; color: #721c24;'>";
    echo "<h2>❌ LỖI</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>

<br><br>
<a href="final_test.php" style="display: inline-block; background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
    ← Quay lại Final Test
</a>