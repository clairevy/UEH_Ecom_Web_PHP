<?php
// Test file để kiểm tra wishlist API
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
require_once __DIR__ . '/configs/config.php';
require_once __DIR__ . '/core/BaseController.php';
require_once __DIR__ . '/core/BaseModel.php';
require_once __DIR__ . '/configs/database.php';
require_once __DIR__ . '/helpers/session_helper.php';
require_once __DIR__ . '/app/models/Wishlist.php';
require_once __DIR__ . '/app/controllers/WishlistController.php';

// Start session
session_start();

// Test 1: Check if WishlistController can be instantiated
echo "Test 1: Instantiating WishlistController...\n";
try {
    $controller = new WishlistController();
    echo "SUCCESS: WishlistController instantiated successfully\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Test the toggle method
echo "\nTest 2: Testing toggle method...\n";
$_POST['product_id'] = 1; // Set a test product ID

// Capture output
ob_start();
try {
    $controller->toggle();
    $output = ob_get_clean();
    echo "Raw output from toggle(): \n";
    echo $output . "\n";
    
    // Check if it's valid JSON
    $json = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "SUCCESS: Valid JSON returned\n";
        print_r($json);
    } else {
        echo "ERROR: Invalid JSON returned. JSON error: " . json_last_error_msg() . "\n";
        echo "Output was: " . $output . "\n";
    }
} catch (Exception $e) {
    ob_end_clean();
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>