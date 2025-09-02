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
    
} catch(Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>
