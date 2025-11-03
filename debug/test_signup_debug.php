<?php
/**
 * Debug Signup System
 */

echo "<h1>🔍 DEBUG SIGNUP SYSTEM</h1>";
echo "<hr>";

// Test 1: Check if all required files exist
echo "<h2>1. Kiểm tra Files:</h2>";
$files = [
    'app/controllers/AuthController.php',
    'app/models/User.php', 
    'app/views/auth/signup.php',
    'configs/config.php',
    'configs/database.php',
    'configs/email.php',
    'helpers/email_helper.php',
    'core/BaseController.php',
    'core/BaseModel.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file<br>";
    } else {
        echo "❌ $file - MISSING<br>";
    }
}

echo "<hr>";

// Test 2: Check constants
echo "<h2>2. Kiểm tra Constants:</h2>";
if (defined('VERIFICATION_EXPIRE_MINUTES')) {
    echo "✅ VERIFICATION_EXPIRE_MINUTES = " . VERIFICATION_EXPIRE_MINUTES . "<br>";
} else {
    echo "❌ VERIFICATION_EXPIRE_MINUTES not defined<br>";
}

if (defined('RESET_EXPIRE_MINUTES')) {
    echo "✅ RESET_EXPIRE_MINUTES = " . RESET_EXPIRE_MINUTES . "<br>";
} else {
    echo "❌ RESET_EXPIRE_MINUTES not defined<br>";
}

echo "<hr>";

// Test 3: Check database connection
echo "<h2>3. Kiểm tra Database:</h2>";
try {
    require_once 'configs/config.php';
    require_once 'configs/database.php';
    
    $db = Database::getInstance();
    echo "✅ Database connection OK<br>";
    
    // Check if users table exists
    $db->query("SHOW TABLES LIKE 'users'");
    $result = $db->single();
    if ($result) {
        echo "✅ Users table exists<br>";
    } else {
        echo "❌ Users table missing<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 4: Check AuthController
echo "<h2>4. Kiểm tra AuthController:</h2>";
try {
    require_once 'app/controllers/AuthController.php';
    echo "✅ AuthController loaded<br>";
    
    $auth = new AuthController();
    echo "✅ AuthController instantiated<br>";
    
    // Check if methods exist
    if (method_exists($auth, 'signUp')) {
        echo "✅ signUp method exists<br>";
    } else {
        echo "❌ signUp method missing<br>";
    }
    
} catch (Exception $e) {
    echo "❌ AuthController error: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 5: Check User Model
echo "<h2>5. Kiểm tra User Model:</h2>";
try {
    require_once 'app/models/User.php';
    echo "✅ User model loaded<br>";
    
    $user = new User();
    echo "✅ User model instantiated<br>";
    
    // Check if methods exist
    $methods = ['create', 'findByEmail', 'updateVerificationToken'];
    foreach ($methods as $method) {
        if (method_exists($user, $method)) {
            echo "✅ $method method exists<br>";
        } else {
            echo "❌ $method method missing<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ User model error: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 6: Test signup with sample data
echo "<h2>6. Test Signup với dữ liệu mẫu:</h2>";
try {
    $auth = new AuthController();
    
    // Simulate POST data
    $_POST = [
        'email' => 'test@example.com',
        'password' => '123456',
        'confirm_password' => '123456',
        'name' => 'Test User',
        'phone' => '0123456789'
    ];
    
    echo "Testing with data: " . json_encode($_POST) . "<br>";
    
    // Capture output
    ob_start();
    $auth->signUp();
    $output = ob_get_clean();
    
    echo "Response: " . $output . "<br>";
    
} catch (Exception $e) {
    echo "❌ Signup test error: " . $e->getMessage() . "<br>";
}

?>

<style>
body { font-family: Arial; max-width: 1000px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
h1, h2 { color: #333; }
</style>

