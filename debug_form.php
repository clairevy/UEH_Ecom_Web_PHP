<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Form Submission</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .debug { background: #f5f5f5; padding: 10px; margin: 10px 0; border-left: 4px solid #007bff; }
    </style>
</head>
<body>
    <h2>Debug Product Creation Form</h2>
    
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="debug">
            <h3>POST Data Received:</h3>
            <pre><?php print_r($_POST); ?></pre>
        </div>
        
        <div class="debug">
            <h3>FILES Data Received:</h3>
            <pre><?php print_r($_FILES); ?></pre>
        </div>
        
        <div class="debug">
            <h3>Files Analysis:</h3>
            <?php if (isset($_FILES['product_images'])): ?>
                <p>File field exists: ✅</p>
                <p>File count: <?= count($_FILES['product_images']['name']) ?></p>
                
                foreach ($_FILES['product_images']['name'] as $index => $name) {
                    if (!empty($name)) {
                        echo "<p>File $index: $name</p>";
                        echo "<p>Size: " . $_FILES['product_images']['size'][$index] . " bytes</p>";
                        echo "<p>Error: " . $_FILES['product_images']['error'][$index] . "</p>";
                        echo "<p>Tmp name: " . $_FILES['product_images']['tmp_name'][$index] . "</p>";
                        echo "<hr>";
                    }
                }
            <?php else: ?>
                <p>No file field found: ❌</p>
            <?php endif; ?>
        </div>
        
        <div class="debug">
            <h3>Form Action Test:</h3>
            <p>If this page shows, it means the form is NOT submitting to the correct controller.</p>
            <p>Form should submit to: <strong>admin/index.php?url=products&action=create</strong></p>
            <p>But it came to: <strong><?= $_SERVER['REQUEST_URI'] ?></strong></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <h3>Test Form</h3>
        
        <p>
            <label>Tên sản phẩm:</label><br>
            <input type="text" name="name" value="Test Product" required>
        </p>
        
        <p>
            <label>Giá:</label><br>
            <input type="number" name="base_price" value="100000" required>
        </p>
        
        <p>
            <label>SKU:</label><br>
            <input type="text" name="sku" value="TEST-001" required>
        </p>
        
        <p>
            <label>Hình ảnh (multiple):</label><br>
            <input type="file" name="product_images[]" multiple accept="image/*" required>
        </p>
        
        <p>
            <label>Category IDs:</label><br>
            <input type="checkbox" name="category_ids[]" value="1" checked> Category 1
        </p>
        
        <p>
            <label>Variants:</label><br>
            Size: <input type="text" name="variants[0][size]" value="M">
            Color: <input type="text" name="variants[0][color]" value="Đỏ">
            Price: <input type="number" name="variants[0][price]" value="100000">
            Stock: <input type="number" name="variants[0][stock]" value="10">
        </p>
        
        <p>
            <input type="checkbox" name="is_active" value="1" checked> Is Active
        </p>
        
        <p>
            <button type="submit">Test Submit</button>
        </p>
    </form>
    
    <h3>Expected Form Action:</h3>
    <p>The real form should have:</p>
    <code>action="admin/index.php?url=products&amp;action=create"</code>
    
    <h3>Debug Steps:</h3>
    <ol>
        <li>Fill form and submit to see POST/FILES data</li>
        <li>Check if files are being received properly</li>
        <li>Update real form action to point to correct controller</li>
        <li>Add debug logging in ProductsController::create()</li>
    </ol>
</body>
</html>