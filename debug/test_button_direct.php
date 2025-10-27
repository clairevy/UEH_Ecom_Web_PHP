<!DOCTYPE html>
<html>
<head>
    <title>Test Button Direct</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .test-box { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #007bff; }
        .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; font-weight: bold; cursor: pointer; border: none; }
        .btn:hover { background: #0056b3; }
        .success { border-left-color: #28a745; background: #d4edda; }
        .error { border-left-color: #dc3545; background: #f8d7da; }
    </style>
</head>
<body>
    <h1>🧪 TEST BUTTON DIRECT</h1>
    
    <div class="test-box">
        <h2>Test 1: JavaScript Function</h2>
        <p>Kiểm tra function editOrder() có tồn tại không:</p>
        <button onclick="testFunction()" class="btn">🎯 Test editOrder()</button>
        <div id="result1"></div>
    </div>

    <div class="test-box">
        <h2>Test 2: Direct Navigation</h2>
        <p>Test chuyển trang trực tiếp:</p>
        <button onclick="directNav()" class="btn" style="background: #28a745;">📄 Direct to Order Details</button>
        <div id="result2"></div>
    </div>

    <div class="test-box">
        <h2>Test 3: Orders Page</h2>
        <p>Vào orders page để test button thật:</p>
        <a href="../admin/index.php?url=orders" class="btn" style="background: #6c757d;">📋 Orders Page</a>
    </div>

    <!-- Load JavaScript -->
    <script src="../app/views/admin/assets/js/orders.js"></script>
    
    <script>
        function testFunction() {
            const result = document.getElementById('result1');
            
            console.log('Testing editOrder function...');
            console.log('editOrder type:', typeof editOrder);
            
            if (typeof editOrder === 'function') {
                result.innerHTML = '<div class="success" style="padding: 10px; margin: 10px 0; border-radius: 5px;">✅ Function editOrder() TỒN TẠI!</div>';
                
                // Test call
                try {
                    editOrder(1);
                } catch (e) {
                    result.innerHTML += '<div class="error" style="padding: 10px; margin: 10px 0; border-radius: 5px;">❌ Lỗi khi gọi: ' + e.message + '</div>';
                }
            } else {
                result.innerHTML = '<div class="error" style="padding: 10px; margin: 10px 0; border-radius: 5px;">❌ Function editOrder() KHÔNG TỒN TẠI!</div>';
            }
        }
        
        function directNav() {
            const result = document.getElementById('result2');
            result.innerHTML = '<div style="padding: 10px; margin: 10px 0; border-radius: 5px; background: #d1ecf1;">🔄 Đang chuyển sang order-details...</div>';
            
            // Direct navigation
            window.location.href = '../admin/index.php?url=order-details&id=1';
        }
    </script>
</body>
</html>

