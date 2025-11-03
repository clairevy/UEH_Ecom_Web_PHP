<?php
/**
 * Script kiểm tra và sửa quyền thư mục uploads
 */

echo "<h2>Kiểm tra quyền thư mục uploads</h2>";

$projectRoot = dirname(__DIR__);
$uploadBasePath = $projectRoot . '/public/uploads/products/';

echo "<pre>";
echo "Project Root: " . $projectRoot . "\n";
echo "Upload Base Path: " . $uploadBasePath . "\n\n";

// Kiểm tra thư mục uploads chính
echo "=== KIỂM TRA THƯ MỤC UPLOADS ===\n";

if (!is_dir($uploadBasePath)) {
    echo "❌ Thư mục uploads không tồn tại: $uploadBasePath\n";
    echo "Đang tạo thư mục...\n";
    
    if (mkdir($uploadBasePath, 0777, true)) {
        echo "✅ Đã tạo thư mục thành công\n";
        chmod($uploadBasePath, 0777);
    } else {
        echo "❌ Không thể tạo thư mục\n";
    }
} else {
    echo "✅ Thư mục uploads tồn tại\n";
}

// Kiểm tra quyền ghi
if (is_writable($uploadBasePath)) {
    echo "✅ Thư mục có quyền ghi\n";
} else {
    echo "❌ Thư mục KHÔNG có quyền ghi\n";
    echo "Đang thử cấp quyền...\n";
    
    if (chmod($uploadBasePath, 0777)) {
        echo "✅ Đã cấp quyền 0777 thành công\n";
    } else {
        echo "❌ Không thể cấp quyền (cần chạy script với quyền admin)\n";
    }
}

// Kiểm tra quyền đọc
if (is_readable($uploadBasePath)) {
    echo "✅ Thư mục có quyền đọc\n";
} else {
    echo "❌ Thư mục KHÔNG có quyền đọc\n";
}

// Thông tin chi tiết
echo "\n=== THÔNG TIN CHI TIẾT ===\n";
echo "Owner: " . (function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($uploadBasePath))['name'] : 'N/A') . "\n";
echo "Permissions: " . substr(sprintf('%o', fileperms($uploadBasePath)), -4) . "\n";
echo "Is directory: " . (is_dir($uploadBasePath) ? 'Yes' : 'No') . "\n";
echo "Exists: " . (file_exists($uploadBasePath) ? 'Yes' : 'No') . "\n";

// Test tạo file
echo "\n=== TEST TẠO FILE ===\n";
$testFile = $uploadBasePath . 'test_' . time() . '.txt';
$testContent = "Test upload permissions at " . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $testContent)) {
    echo "✅ Có thể tạo file: " . basename($testFile) . "\n";
    
    if (file_exists($testFile)) {
        echo "✅ File tồn tại sau khi tạo\n";
        
        // Xóa file test
        if (unlink($testFile)) {
            echo "✅ Có thể xóa file\n";
        } else {
            echo "❌ Không thể xóa file\n";
        }
    }
} else {
    echo "❌ KHÔNG thể tạo file trong thư mục uploads\n";
    echo "Lỗi: " . error_get_last()['message'] . "\n";
}

// Kiểm tra các thư mục con mẫu
echo "\n=== KIỂM TRA THƯ MỤC CON MẪU ===\n";
$sampleProductDirs = [1, 12, 14, 18, 23, 30];

foreach ($sampleProductDirs as $productId) {
    $productDir = $uploadBasePath . $productId . '/';
    
    if (is_dir($productDir)) {
        echo "Thư mục sản phẩm #{$productId}: ";
        echo (is_writable($productDir) ? '✅ Writable' : '❌ Not writable');
        echo " | Files: " . count(glob($productDir . '*')) . "\n";
    }
}

echo "\n=== KẾT QUẢ ===\n";
if (is_dir($uploadBasePath) && is_writable($uploadBasePath)) {
    echo "✅ Thư mục uploads đã sẵn sàng!\n";
} else {
    echo "❌ Vẫn còn vấn đề với thư mục uploads\n";
    echo "Hướng dẫn sửa:\n";
    echo "1. Mở Git Bash hoặc Command Prompt với quyền Administrator\n";
    echo "2. Chạy: chmod -R 777 " . $uploadBasePath . "\n";
    echo "3. Hoặc cấp quyền thủ công qua Windows Explorer\n";
}

echo "</pre>";
?>

