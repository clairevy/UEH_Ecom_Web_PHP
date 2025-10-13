<?php
// Simple test for CartController
echo "Testing CartController...\n";

// Include required files
require_once __DIR__ . '/core/BaseController.php';
require_once __DIR__ . '/app/models/Product.php';
require_once __DIR__ . '/helpers/session_helper.php';
require_once __DIR__ . '/app/controllers/CartController.php';

// Test cart controller instantiation
try {
    $cartController = new CartController();
    echo "✅ CartController instantiated successfully\n";
    
    // Test if session is working
    if (isset($_SESSION)) {
        echo "✅ Session is working\n";
    } else {
        echo "❌ Session not working\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// Test URL helper
if (function_exists('url')) {
    echo "✅ URL helper is working: " . url('cart/add') . "\n";
} else {
    echo "❌ URL helper not found\n";
}

echo "Test completed.\n";
?>