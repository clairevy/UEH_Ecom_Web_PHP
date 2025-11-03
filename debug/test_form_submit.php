<?php
/**
 * Debug: Test Form Submission Flow
 * Kiểm tra xem form có submit và đến controller không
 */

// Capture all request data
$requestData = [
    'GET' => $_GET,
    'POST' => $_POST,
    'FILES' => $_FILES,
    'SERVER' => [
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'N/A',
        'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? 'N/A',
        'CONTENT_TYPE' => $_SERVER['CONTENT_TYPE'] ?? 'N/A',
    ]
];

// Log to file
$logFile = __DIR__ . '/../logs/form_submit_debug.log';
$logContent = "\n=== FORM SUBMIT DEBUG " . date('Y-m-d H:i:s') . " ===\n";
$logContent .= print_r($requestData, true);
$logContent .= "\n";

file_put_contents($logFile, $logContent, FILE_APPEND);

// Display for browser
echo "<h2>Form Submit Debug</h2>";
echo "<pre>";
echo "Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "Query String: " . ($_SERVER['QUERY_STRING'] ?? 'N/A') . "\n\n";

echo "=== GET Parameters ===\n";
print_r($_GET);

echo "\n=== POST Parameters ===\n";
print_r($_POST);

echo "\n=== FILES ===\n";
if (!empty($_FILES)) {
    foreach ($_FILES as $key => $file) {
        echo "$key:\n";
        if (is_array($file['name'])) {
            echo "  Count: " . count($file['name']) . " files\n";
            for ($i = 0; $i < count($file['name']); $i++) {
                if (!empty($file['name'][$i])) {
                    echo "  File $i: {$file['name'][$i]} ({$file['size'][$i]} bytes)\n";
                    echo "    Error: {$file['error'][$i]}\n";
                }
            }
        } else {
            echo "  Name: {$file['name']}\n";
            echo "  Size: {$file['size']} bytes\n";
            echo "  Error: {$file['error']}\n";
        }
    }
} else {
    echo "No files uploaded\n";
}

echo "\n=== Logged to: logs/form_submit_debug.log ===\n";
echo "</pre>";
?>

