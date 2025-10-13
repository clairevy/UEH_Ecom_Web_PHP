<?php
// Test cart functionality
echo "🧪 Testing Cart Functionality\n";
echo "============================\n\n";

// Test 1: Check if CartController exists
$cartControllerPath = __DIR__ . '/app/controllers/CartController.php';
echo "1. CartController File: ";
if (file_exists($cartControllerPath)) {
    echo "✅ EXISTS\n";
} else {
    echo "❌ NOT FOUND at $cartControllerPath\n";
}

// Test 2: Check if BaseController exists
$baseControllerPath = __DIR__ . '/core/BaseController.php';
echo "2. BaseController File: ";
if (file_exists($baseControllerPath)) {
    echo "✅ EXISTS\n";
} else {
    echo "❌ NOT FOUND at $baseControllerPath\n";
}

// Test 3: Try to include and instantiate
try {
    echo "3. Including files...\n";
    
    if (file_exists($baseControllerPath)) {
        require_once $baseControllerPath;
        echo "   ✅ BaseController included\n";
    }
    
    if (file_exists($cartControllerPath)) {
        require_once $cartControllerPath;
        echo "   ✅ CartController included\n";
    }
    
    // Test class exists
    if (class_exists('CartController')) {
        echo "   ✅ CartController class exists\n";
        
        // Try to create instance
        $cart = new CartController();
        echo "   ✅ CartController instantiated\n";
        
        // Check if add method exists
        if (method_exists($cart, 'add')) {
            echo "   ✅ add() method exists\n";
        } else {
            echo "   ❌ add() method NOT FOUND\n";
        }
        
    } else {
        echo "   ❌ CartController class NOT EXISTS\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}

// Test 4: Check URL helper
echo "4. URL Helper: ";
try {
    require_once __DIR__ . '/helpers/url_helper.php';
    $testUrl = url('cart/add');
    echo "✅ Working - Test URL: $testUrl\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

// Test 5: Simulate cart add request
echo "5. Simulating Cart Add Request...\n";
$_POST['product_id'] = 1;
$_POST['quantity'] = 1;
$_POST['size'] = '';
$_POST['color'] = '';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

// Set up mock session
session_start();

try {
    // Simulate the cart add process
    ob_start();
    
    // Include necessary files
    require_once __DIR__ . '/core/BaseController.php';
    require_once __DIR__ . '/app/controllers/CartController.php';
    
    $cartController = new CartController();
    $cartController->add();
    
    $output = ob_get_clean();
    echo "   ✅ Cart add executed\n";
    echo "   📤 Output: " . $output . "\n";
    
} catch (Exception $e) {
    ob_end_clean();
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
    echo "   📍 File: " . $e->getFile() . "\n";
    echo "   📍 Line: " . $e->getLine() . "\n";
}

echo "\n🏁 Test completed!\n";
?>