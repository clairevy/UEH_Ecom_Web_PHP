<!DOCTYPE html>
<html>
<head>
    <title>Simple Route Test</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #007bff; }
        .success { border-left-color: #28a745; background: #d4edda; }
        .error { border-left-color: #dc3545; background: #f8d7da; }
        .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; font-weight: bold; }
        .btn:hover { background: #0056b3; }
        code { background: #f4f4f4; padding: 3px 8px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <h1>🔍 SIMPLE ROUTE TEST</h1>
    
    <div class="box success">
        <h2>✅ Bước 1: Test Orders Page</h2>
        <p>Click vào orders page:</p>
        <a href="../admin/index.php?url=orders" class="btn">📋 Vào Orders Page</a>
        <p><small>Nếu không thấy gì → Có lỗi trong OrdersController::index()</small></p>
    </div>

    <div class="box success">
        <h2>✅ Bước 2: Test Order Details TRỰC TIẾP</h2>
        <p>Bỏ qua button, test route trực tiếp:</p>
        <a href="../admin/index.php?url=order-details&id=1" class="btn">📄 Test Order Details #1</a>
        <p><strong>Kỳ vọng:</strong></p>
        <ul>
            <li>✅ Nếu hiển thị trang chi tiết → Route OK</li>
            <li>❌ Nếu lỗi "Đơn hàng không tồn tại" → Database không có order #1</li>
            <li>❌ Nếu lỗi PHP → order-details.php có bug</li>
            <li>❌ Nếu trắng trang → Controller có exception</li>
        </ul>
    </div>

    <div class="box">
        <h2>🧪 Bước 3: Test JavaScript Function</h2>
        <p>Test function editOrder() trực tiếp:</p>
        <button onclick="testFunction()" class="btn" style="background: #28a745; border: none; cursor: pointer;">
            🎯 Test editOrder(1)
        </button>
        
        <script src="../app/views/admin/assets/js/orders.js"></script>
        <script>
        function testFunction() {
            console.log('Testing editOrder function...');
            
            if (typeof editOrder === 'function') {
                alert('✅ Function editOrder TỒN TẠI!\n\nSẽ chuyển sang order-details...');
                editOrder(1);
            } else {
                alert('❌ Function editOrder KHÔNG TỒN TẠI!\n\nCheck Console (F12) để xem lỗi.');
                console.error('editOrder is not defined');
            }
        }
        </script>
    </div>

    <div class="box error">
        <h2>⚠️ Nếu Lỗi "Đơn hàng không tồn tại"</h2>
        <p>Database chưa có orders. Chạy script này để tạo order test:</p>
        <a href="create_test_order.php" class="btn" style="background: #ffc107; color: #000;">➕ Tạo Order Test</a>
    </div>

    <div class="box">
        <h2>📋 Checklist Debug:</h2>
        <ol>
            <li>✅ orders.js có function editOrder: <code>VERIFIED</code></li>
            <li>✅ orders.php load orders.js: <code>VERIFIED</code></li>
            <li>✅ Router có route order-details: <code>VERIFIED</code></li>
            <li>✅ Controller có showDetails(): <code>VERIFIED</code></li>
            <li>❓ Database có orders: <strong>CẦN KIỂM TRA</strong></li>
            <li>❓ View order-details.php render OK: <strong>CẦN KIỂM TRA</strong></li>
        </ol>
    </div>

    <hr>
    <p><strong>Làm theo thứ tự:</strong></p>
    <ol>
        <li>Click "Test Order Details #1" → Xem có lỗi gì không</li>
        <li>Nếu lỗi "không tồn tại" → Click "Tạo Order Test"</li>
        <li>Sau đó test lại</li>
    </ol>
</body>
</html>


