<?php
/**
 * Test Simple Controllers
 * Test đơn giản các controller
 */

echo "<h1>Test Simple Controllers</h1>";

// Simulate web environment
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/Ecom_website/admin/index.php';

try {
    // Include configs
    require_once __DIR__ . '/../configs/config.php';
    require_once __DIR__ . '/../configs/database.php';
    require_once __DIR__ . '/../core/BaseController.php';
    require_once __DIR__ . '/../core/BaseModel.php';
    require_once __DIR__ . '/../app/models/Review.php';
    require_once __DIR__ . '/../app/models/Product.php';
    require_once __DIR__ . '/../app/services/ProductService.php';
    
    echo "<p style='color: green;'>✓ Core files loaded</p>";
    
    echo "<h2>1. Test ReviewsController</h2>";
    require_once __DIR__ . '/../app/controllers/admin/ReviewsController.php';
    echo "<p style='color: green;'>✓ ReviewsController loaded</p>";
    
    $_GET['url'] = 'reviews';
    $reviewsController = new ReviewsController();
    echo "<p style='color: green;'>✓ ReviewsController created</p>";
    
    ob_start();
    $reviewsController->index();
    $reviewsOutput = ob_get_clean();
    
    echo "<p style='color: green;'>✓ ReviewsController executed</p>";
    echo "<p>Reviews output length: " . strlen($reviewsOutput) . " characters</p>";
    
    echo "<h2>2. Test ProductsController</h2>";
    require_once __DIR__ . '/../app/controllers/admin/ProductController.php';
    echo "<p style='color: green;'>✓ ProductsController loaded</p>";
    
    $_GET['url'] = 'products';
    $productController = new ProductsController();
    echo "<p style='color: green;'>✓ ProductsController created</p>";
    
    ob_start();
    $productController->index();
    $productsOutput = ob_get_clean();
    
    echo "<p style='color: green;'>✓ ProductsController executed</p>";
    echo "<p>Products output length: " . strlen($productsOutput) . " characters</p>";
    
    echo "<h2>3. Kết luận</h2>";
    if (strlen($reviewsOutput) > 10000 && strlen($productsOutput) > 10000) {
        echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✅ CẢ HAI TRANG ĐỀU HOẠT ĐỘNG ĐÚNG!</p>";
    } else {
        echo "<p style='color: red; font-size: 18px; font-weight: bold;'>❌ VẪN CÓ VẤN ĐỀ!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Lỗi: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
