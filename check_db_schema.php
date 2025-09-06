<?php
// File tạm thời để kiểm tra cấu trúc database
require_once 'configs/config.php';
require_once 'configs/database.php';

try {
    $db = Database::getInstance();
    
    echo "=== DANH SÁCH CÁC BẢNG ===\n";
    $db->query("SHOW TABLES");
    $tables = $db->resultSet();
    
    foreach($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- $tableName\n";
    }
    
    echo "\n=== CẤU TRÚC BẢNG USERS ===\n";
    $db->query("DESCRIBE users");
    $userColumns = $db->resultSet();
    
    foreach($userColumns as $column) {
        echo "- {$column->Field} ({$column->Type}) - {$column->Null} - {$column->Key}\n";
    }
    
    echo "\n=== CẤU TRÚC BẢNG PRODUCTS ===\n";
    $db->query("DESCRIBE products");
    $productColumns = $db->resultSet();
    
    foreach($productColumns as $column) {
        echo "- {$column->Field} ({$column->Type}) - {$column->Null} - {$column->Key}\n";
    }
    
    echo "\n=== CẤU TRÚC BẢNG ORDERS ===\n";
    $db->query("DESCRIBE orders");
    $orderColumns = $db->resultSet();
    
    foreach($orderColumns as $column) {
        echo "- {$column->Field} ({$column->Type}) - {$column->Null} - {$column->Key}\n";
    }
    
    echo "\n=== CẤU TRÚC BẢNG CARTS ===\n";
    $db->query("DESCRIBE carts");
    $cartColumns = $db->resultSet();
    
    foreach($cartColumns as $column) {
        echo "- {$column->Field} ({$column->Type}) - {$column->Null} - {$column->Key}\n";
    }

    echo "\n=== CẤU TRÚC BẢNG CART_ITEMS ===\n";
    $db->query("DESCRIBE cart_items");
    $cartItemColumns = $db->resultSet();
    
    foreach($cartItemColumns as $column) {
        echo "- {$column->Field} ({$column->Type}) - {$column->Null} - {$column->Key}\n";
    }

    echo "\n=== CẤU TRÚC BẢNG PAYMENTS ===\n";
    $db->query("DESCRIBE payments");
    $paymentColumns = $db->resultSet();
    
    foreach($paymentColumns as $column) {
        echo "- {$column->Field} ({$column->Type}) - {$column->Null} - {$column->Key}\n";
    }

    echo "\n=== CẤU TRÚC BẢNG WISHLISTS ===\n";
    $db->query("DESCRIBE wishlists");
    $wishlistColumns = $db->resultSet();
    
    foreach($wishlistColumns as $column) {
        echo "- {$column->Field} ({$column->Type}) - {$column->Null} - {$column->Key}\n";
    }

    echo "\n=== CẤU TRÚC BẢNG WISHLIST_ITEMS ===\n";
    $db->query("DESCRIBE wishlist_items");
    $wishlistItemColumns = $db->resultSet();
    
    foreach($wishlistItemColumns as $column) {
        echo "- {$column->Field} ({$column->Type}) - {$column->Null} - {$column->Key}\n";
    }

    echo "\n=== CẤU TRÚC BẢNG PRODUCT_REVIEWS ===\n";
    $db->query("DESCRIBE product_reviews");
    $reviewColumns = $db->resultSet();
    
    foreach($reviewColumns as $column) {
        echo "- {$column->Field} ({$column->Type}) - {$column->Null} - {$column->Key}\n";
    }
    $db->query("DESCRIBE discounts");
    print_r($db->resultSet());

    echo "\n=== CẤU TRÚC BẢNG CATEGORIES ===\n";
    $db->query("DESCRIBE categories");
    print_r($db->resultSet());

    echo "\n=== CẤU TRÚC BẢNG COLLECTION ===\n";
    $db->query("DESCRIBE collection");
    print_r($db->resultSet());
    
} catch(Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>
