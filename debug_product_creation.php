<?php
/**
 * Test file upload và product creation
 */

// Include required files
require_once 'configs/config.php';
require_once 'configs/database.php';
require_once 'core/BaseModel.php';
require_once 'core/BaseController.php';
require_once 'app/models/Product.php';
require_once 'app/models/Category.php';
require_once 'app/models/Collection.php';
require_once 'app/controllers/admin/ProductsController.php';

echo "<h2>Debug Product Creation Flow</h2>";

// 1. Test database connection
try {
    $db = new Database();
    $conn = $db->getConnection();
    echo "✅ Database connection: OK<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// 2. Test Product model
try {
    $productModel = new Product();
    echo "✅ Product model instantiated: OK<br>";
} catch (Exception $e) {
    echo "❌ Product model failed: " . $e->getMessage() . "<br>";
    exit;
}

// 3. Test ProductsController
try {
    $controller = new ProductsController();
    echo "✅ ProductsController instantiated: OK<br>";
} catch (Exception $e) {
    echo "❌ ProductsController failed: " . $e->getMessage() . "<br>";
    exit;
}

// 4. Check upload directory
$projectRoot = realpath(__DIR__);
$uploadPath = $projectRoot . '/public/uploads/products/';
echo "<br><strong>Upload Path Check:</strong><br>";
echo "Project Root: " . $projectRoot . "<br>";
echo "Upload Path: " . $uploadPath . "<br>";

if (!is_dir($uploadPath)) {
    echo "⚠️  Upload directory doesn't exist, trying to create...<br>";
    if (mkdir($uploadPath, 0777, true)) {
        echo "✅ Upload directory created successfully<br>";
    } else {
        echo "❌ Failed to create upload directory<br>";
    }
} else {
    echo "✅ Upload directory exists<br>";
}

if (is_writable($uploadPath)) {
    echo "✅ Upload directory is writable<br>";
} else {
    echo "❌ Upload directory is NOT writable<br>";
}

// 5. Test fake form submission data
echo "<br><strong>Testing Form Data Processing:</strong><br>";

// Simulate POST data
$_POST = [
    'name' => 'Test Product Debug',
    'description' => 'Test product for debugging',
    'material' => 'gold',
    'base_price' => 100000,
    'sku' => 'TEST-DEBUG-' . time(),
    'collection_id' => null,
    'is_active' => 1,
    'category_ids' => [1], // Assume category 1 exists
    'variants' => [
        [
            'size' => 'M',
            'color' => 'Đỏ',
            'price' => 100000,
            'stock' => 10
        ]
    ]
];

// Simulate file upload (empty for now)
$_FILES = [
    'product_images' => [
        'name' => [''],
        'tmp_name' => [''],
        'size' => [0],
        'error' => [UPLOAD_ERR_NO_FILE]
    ]
];

echo "POST data prepared: " . json_encode($_POST, JSON_UNESCAPED_UNICODE) . "<br>";
echo "FILES data prepared: " . json_encode($_FILES) . "<br>";

// 6. Test validation method
echo "<br><strong>Testing Validation:</strong><br>";
$reflection = new ReflectionClass('ProductsController');
$validateMethod = $reflection->getMethod('validateProductData');
$validateMethod->setAccessible(true);

try {
    $validationErrors = $validateMethod->invoke($controller, $_POST);
    if (empty($validationErrors)) {
        echo "✅ Validation passed<br>";
    } else {
        echo "⚠️  Validation errors:<br>";
        foreach ($validationErrors as $error) {
            echo "- " . $error . "<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Validation test failed: " . $e->getMessage() . "<br>";
}

// 7. Test image validation
echo "<br><strong>Testing Image Validation:</strong><br>";
if (empty($_FILES['product_images']['name'][0])) {
    echo "⚠️  No images uploaded (as expected for this test)<br>";
} else {
    echo "✅ Images detected<br>";
}

// 8. Check database tables exist
echo "<br><strong>Checking Database Tables:</strong><br>";
$tables = ['products', 'product_variants', 'categories', 'images', 'image_usages', 'product_categories'];

foreach ($tables as $table) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM $table");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        echo "✅ Table '$table' exists with $count records<br>";
    } catch (Exception $e) {
        echo "❌ Table '$table' error: " . $e->getMessage() . "<br>";
    }
}

echo "<br><strong>Debug Complete!</strong><br>";
echo "<br>Next step: Try creating a product with actual images via the web form and check error logs.";
?>