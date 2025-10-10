<?php
/**
 * Check PHP Syntax - Kiểm tra syntax của tất cả file PHP
 */

echo "<h1>🔍 PHP Syntax Checker</h1>";

// Function to check PHP syntax
function checkPHPSyntax($file) {
    $output = shell_exec("php -l \"$file\" 2>&1");
    return $output;
}

// Check admin controllers
echo "<h2>📁 Admin Controllers:</h2>";
$controllerPath = __DIR__ . '/../app/controllers/admin/';
$controllerFiles = glob($controllerPath . '*.php');

echo "<ul>";
foreach ($controllerFiles as $file) {
    $filename = basename($file);
    $output = checkPHPSyntax($file);
    
    if (strpos($output, 'No syntax errors') !== false) {
        echo "<li>✅ $filename - OK</li>";
    } else {
        echo "<li>❌ $filename - ERROR</li>";
        echo "<pre style='color: red; font-size: 12px;'>$output</pre>";
    }
}
echo "</ul>";

// Check admin views
echo "<h2>📁 Admin Views:</h2>";
$viewPath = __DIR__ . '/../app/views/admin/pages/';
$viewFiles = glob($viewPath . '*.php');

echo "<ul>";
foreach ($viewFiles as $file) {
    $filename = basename($file);
    $output = checkPHPSyntax($file);
    
    if (strpos($output, 'No syntax errors') !== false) {
        echo "<li>✅ $filename - OK</li>";
    } else {
        echo "<li>❌ $filename - ERROR</li>";
        echo "<pre style='color: red; font-size: 12px;'>$output</pre>";
    }
}
echo "</ul>";

// Check services
echo "<h2>📁 Services:</h2>";
$servicePath = __DIR__ . '/../app/services/';
$serviceFiles = glob($servicePath . '*.php');

echo "<ul>";
foreach ($serviceFiles as $file) {
    $filename = basename($file);
    $output = checkPHPSyntax($file);
    
    if (strpos($output, 'No syntax errors') !== false) {
        echo "<li>✅ $filename - OK</li>";
    } else {
        echo "<li>❌ $filename - ERROR</li>";
        echo "<pre style='color: red; font-size: 12px;'>$output</pre>";
    }
}
echo "</ul>";

echo "<h2>🎯 Kết quả:</h2>";
echo "<p>Nếu tất cả đều ✅ thì syntax đã đúng!</p>";
?>
