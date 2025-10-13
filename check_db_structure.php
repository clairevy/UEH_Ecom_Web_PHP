<?php
// Check database structure and data
require_once 'configs/config.php';
require_once 'configs/database.php';

echo "🔍 Database Structure Analysis\n";
echo "===============================\n\n";

try {
    $db = Database::getInstance();
    
    // Check database name
    echo "🗄️  Using database: db_ecom\n\n";
    
    // Check products table structure
    echo "📋 PRODUCTS Table Structure:\n";
    echo "----------------------------\n";
    $db->query("DESCRIBE products");
    $result = $db->resultSet();
    foreach ($result as $row) {
        $null = $row['Null'] == 'YES' ? 'NULL' : 'NOT NULL';
        $default = $row['Default'] ? "DEFAULT '{$row['Default']}'" : '';
        echo sprintf("%-20s %-15s %-10s %s\n", 
            $row['Field'], 
            $row['Type'], 
            $null, 
            $default
        );
    }
    
    echo "\n📋 PRODUCT_VARIANTS Table Structure:\n";
    echo "------------------------------------\n";
    $db->query("DESCRIBE product_variants");
    $result = $db->resultSet();
    foreach ($result as $row) {
        $null = $row['Null'] == 'YES' ? 'NULL' : 'NOT NULL';
        $default = $row['Default'] ? "DEFAULT '{$row['Default']}'" : '';
        echo sprintf("%-20s %-15s %-10s %s\n", 
            $row['Field'], 
            $row['Type'], 
            $null, 
            $default
        );
    }
    
    // Check sample data
    echo "\n📦 Sample Products Data:\n";
    echo "------------------------\n";
    $db->query("SELECT product_id, name, base_price, stock_quantity FROM products LIMIT 5");
    $result = $db->resultSet();
    foreach ($result as $row) {
        echo sprintf("ID: %-3s | Name: %-30s | Price: %-10s | Stock: %s\n", 
            $row['product_id'], 
            substr($row['name'], 0, 30), 
            number_format($row['base_price']), 
            $row['stock_quantity'] ?? 'NULL'
        );
    }
    
    echo "\n🔄 Sample Product Variants Data:\n";
    echo "--------------------------------\n";
    $db->query("SELECT * FROM product_variants LIMIT 5");
    $variants = $db->resultSet();
    
    if (empty($variants)) {
        echo "❌ No data found in product_variants table\n";
    } else {
        foreach ($variants as $variant) {
            echo "Variant ID: {$variant['variant_id']} | Product: {$variant['product_id']} | ";
            echo "Stock: " . ($variant['stock_quantity'] ?? 'NULL') . "\n";
        }
    }
    
    // Count records
    echo "\n📊 Record Counts:\n";
    echo "-----------------\n";
    
    $db->query("SELECT COUNT(*) as count FROM products");
    $result = $db->single();
    echo "Products: {$result->count} records\n";
    
    $db->query("SELECT COUNT(*) as count FROM product_variants");
    $result = $db->single();
    echo "Product Variants: {$result->count} records\n";
    
    // Check which table has stock_quantity
    echo "\n🏪 Stock Quantity Analysis:\n";
    echo "---------------------------\n";
    
    // Check products table for stock
    $db->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity IS NOT NULL AND stock_quantity > 0");
    $result = $db->single();
    echo "Products with stock > 0: {$result->count}\n";
    
    // Check variants table for stock  
    try {
        $db->query("SELECT COUNT(*) as count FROM product_variants WHERE stock_quantity IS NOT NULL AND stock_quantity > 0");
        $result = $db->single();
        echo "Variants with stock > 0: {$result->count}\n";
    } catch (Exception $e) {
        echo "❌ Error checking variant stock: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n✅ Analysis completed!\n";
?>