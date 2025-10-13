<?php
// Test cart add via HTTP request simulation
echo "🧪 Testing Cart Add Simulation\n";
echo "==============================\n";

// Simulate POST data
$_POST = [
    'product_id' => 1,
    'quantity' => 1,
    'size' => '',
    'color' => ''
];

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

// Capture output
ob_start();

try {
    // Include the main index.php to load all dependencies
    include 'index.php';
    
} catch (Exception $e) {
    $output = ob_get_clean();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📤 Output captured: " . $output . "\n";
    exit;
}

$output = ob_get_clean();
echo "✅ Request processed\n";
echo "📤 Output: " . $output . "\n";

// Check if it's valid JSON
$decoded = json_decode($output, true);
if ($decoded !== null) {
    echo "✅ Valid JSON response\n";
    echo "📋 Response data: " . print_r($decoded, true) . "\n";
} else {
    echo "❌ Invalid JSON response\n";
    echo "🔍 JSON error: " . json_last_error_msg() . "\n";
}
?>