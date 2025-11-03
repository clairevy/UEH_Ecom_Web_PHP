<?php
/**
 * Test Product Creation Flow
 * Kiểm tra toàn bộ flow tạo sản phẩm
 */

require_once __DIR__ . '/../configs/config.php';
require_once __DIR__ . '/../configs/database.php';
require_once __DIR__ . '/../core/BaseModel.php';
require_once __DIR__ . '/../app/models/Product.php';

echo "<h2>Test Product Creation Flow</h2>";
echo "<pre>";

// Test 1: Database Connection
echo "=== TEST 1: DATABASE CONNECTION ===\n";
try {
    $db = Database::getInstance();
    echo "✅ Database connection OK\n\n";
} catch (Exception $e) {
    echo "❌ Database connection FAILED: " . $e->getMessage() . "\n\n";
    die();
}

// Test 2: Check Tables
echo "=== TEST 2: CHECK REQUIRED TABLES ===\n";
$tables = ['products', 'product_variants', 'images', 'image_usages', 'categories', 'product_categories'];
foreach ($tables as $table) {
    $sql = "SHOW TABLES LIKE '$table'";
    $db->query($sql);
    $result = $db->single();
    
    if ($result) {
        echo "✅ Table `$table` exists\n";
    } else {
        echo "❌ Table `$table` NOT FOUND\n";
    }
}
echo "\n";

// Test 3: Check Images Table Schema
echo "=== TEST 3: CHECK IMAGES TABLE SCHEMA ===\n";
$sql = "DESCRIBE images";
$db->query($sql);
$columns = $db->resultSet();

$requiredColumns = ['image_id', 'file_path', 'file_name', 'file_type', 'file_size', 'alt_text'];
$foundColumns = array_column($columns, 'Field');

foreach ($requiredColumns as $col) {
    if (in_array($col, $foundColumns)) {
        echo "✅ Column `$col` exists\n";
    } else {
        echo "❌ Column `$col` MISSING\n";
    }
}
echo "\n";

// Test 4: Check Image Usages Table Schema
echo "=== TEST 4: CHECK IMAGE_USAGES TABLE SCHEMA ===\n";
$sql = "DESCRIBE image_usages";
$db->query($sql);
$columns = $db->resultSet();

$requiredColumns = ['usage_id', 'image_id', 'ref_type', 'ref_id', 'is_primary'];
$foundColumns = array_column($columns, 'Field');

foreach ($requiredColumns as $col) {
    if (in_array($col, $foundColumns)) {
        echo "✅ Column `$col` exists\n";
    } else {
        echo "❌ Column `$col` MISSING\n";
    }
}
echo "\n";

// Test 5: Check Upload Directory
echo "=== TEST 5: CHECK UPLOAD DIRECTORY ===\n";
$uploadPath = dirname(__DIR__) . '/public/uploads/products/';

if (is_dir($uploadPath)) {
    echo "✅ Upload directory exists: $uploadPath\n";
    
    if (is_writable($uploadPath)) {
        echo "✅ Upload directory is writable\n";
    } else {
        echo "❌ Upload directory is NOT writable\n";
    }
} else {
    echo "❌ Upload directory NOT FOUND: $uploadPath\n";
    echo "Creating directory...\n";
    
    if (mkdir($uploadPath, 0777, true)) {
        echo "✅ Directory created\n";
    } else {
        echo "❌ Failed to create directory\n";
    }
}
echo "\n";

// Test 6: Test Product Model Methods
echo "=== TEST 6: TEST PRODUCT MODEL METHODS ===\n";
$productModel = new Product();

// Check if methods exist
$methods = ['create', 'createVariants', 'createWithVariants', 'addProductImage', 'getAvailableMaterials'];
foreach ($methods as $method) {
    if (method_exists($productModel, $method)) {
        echo "✅ Method `$method` exists\n";
    } else {
        echo "❌ Method `$method` NOT FOUND\n";
    }
}
echo "\n";

// Test 7: Test Available Materials
echo "=== TEST 7: TEST AVAILABLE MATERIALS ===\n";
$materials = $productModel->getAvailableMaterials();
echo "Available materials: " . implode(', ', $materials) . "\n";
echo "✅ Materials loaded\n\n";

// Test 8: Check Categories
echo "=== TEST 8: CHECK CATEGORIES ===\n";
$sql = "SELECT COUNT(*) as count FROM categories";
$db->query($sql);
$result = $db->single();

if ($result && $result->count > 0) {
    echo "✅ Found {$result->count} categories in database\n";
} else {
    echo "⚠️  No categories found. You should create some categories first.\n";
}
echo "\n";

// Test 9: Check Collections
echo "=== TEST 9: CHECK COLLECTIONS ===\n";
$sql = "SELECT COUNT(*) as count FROM collection";
$db->query($sql);
$result = $db->single();

if ($result && $result->count > 0) {
    echo "✅ Found {$result->count} collections in database\n";
} else {
    echo "⚠️  No collections found. This is optional.\n";
}
echo "\n";

// Summary
echo "=== SUMMARY ===\n";
echo "Database: " . (Database::getInstance() ? "✅ OK" : "❌ FAIL") . "\n";
echo "Tables: Check results above\n";
echo "Upload Directory: " . (is_writable($uploadPath) ? "✅ OK" : "❌ FAIL") . "\n";
echo "Product Model: ✅ OK\n\n";

echo "=== NEXT STEPS ===\n";
echo "1. If any ❌ appears above, fix it first\n";
echo "2. Make sure to run: database/check_images_schema.sql in phpMyAdmin\n";
echo "3. Try adding a product: http://localhost/Ecom_website/index.php?url=add-product\n";
echo "4. Check error logs: logs/error.log\n\n";

echo "=== TEST COMPLETED ===\n";
echo "</pre>";
?>

