<!DOCTYPE html>
<html>
<head>
    <title>FINAL TEST</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #007bff; }
        .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; font-weight: bold; cursor: pointer; border: none; }
        .btn:hover { background: #0056b3; }
        .success { border-left-color: #28a745; background: #d4edda; }
        .error { border-left-color: #dc3545; background: #f8d7da; }
        .info { border-left-color: #17a2b8; background: #d1ecf1; }
    </style>
</head>
<body>
    <h1>🎯 FINAL TEST - Tìm Vấn Đề</h1>
    
    <div class="box info">
        <h2>📋 Bước 1: Test Database</h2>
        <p>Kiểm tra có orders trong database không:</p>
        <button onclick="checkDatabase()" class="btn">🔍 Check Database</button>
        <div id="dbResult"></div>
    </div>

    <div class="box info">
        <h2>📄 Bước 2: Test Route Trực Tiếp</h2>
        <p>Bỏ qua button, test route order-details trực tiếp:</p>
        <button onclick="testRoute()" class="btn" style="background: #28a745;">📄 Test Order Details Route</button>
        <div id="routeResult"></div>
    </div>

    <div class="box info">
        <h2>🎯 Bước 3: Test JavaScript</h2>
        <p>Kiểm tra function editOrder() có load không:</p>
        <button onclick="testJS()" class="btn" style="background: #ffc107; color: black;">🧪 Test JavaScript</button>
        <div id="jsResult"></div>
    </div>

    <div class="box info">
        <h2>📋 Bước 4: Vào Orders Page</h2>
        <p>Test button thật trong orders page:</p>
        <a href="../admin/index.php?url=orders" class="btn" style="background: #6c757d;">📋 Vào Orders Page</a>
    </div>

    <!-- Load orders.js -->
    <script src="../app/views/admin/assets/js/orders.js"></script>
    
    <script>
        function checkDatabase() {
            const result = document.getElementById('dbResult');
            result.innerHTML = '<div style="padding: 10px; background: #fff3cd; border-radius: 5px;">🔄 Đang kiểm tra database...</div>';
            
            // Simulate check
            setTimeout(() => {
                result.innerHTML = `
                    <div style="padding: 10px; background: #d1ecf1; border-radius: 5px; margin: 10px 0;">
                        <strong>Database Check:</strong><br>
                        ✅ Nếu có orders → Click "Test Order Details Route"<br>
                        ❌ Nếu không có → Click "Tạo Order Test" trước
                    </div>
                `;
            }, 1000);
        }
        
        function testRoute() {
            const result = document.getElementById('routeResult');
            result.innerHTML = '<div style="padding: 10px; background: #fff3cd; border-radius: 5px;">🔄 Đang chuyển sang order-details...</div>';
            
            // Direct navigation to order-details
            window.location.href = '../admin/index.php?url=order-details&id=1';
        }
        
        function testJS() {
            const result = document.getElementById('jsResult');
            
            console.log('Testing JavaScript...');
            console.log('editOrder type:', typeof editOrder);
            console.log('editOrder function:', editOrder);
            
            if (typeof editOrder === 'function') {
                result.innerHTML = `
                    <div style="padding: 10px; background: #d4edda; border-radius: 5px; margin: 10px 0;">
                        ✅ Function editOrder() TỒN TẠI!<br>
                        <button onclick="editOrder(1)" style="background: #28a745; color: white; padding: 8px 16px; border: none; border-radius: 4px; margin-top: 10px; cursor: pointer;">
                            🎯 Test editOrder(1)
                        </button>
                    </div>
                `;
            } else {
                result.innerHTML = `
                    <div style="padding: 10px; background: #f8d7da; border-radius: 5px; margin: 10px 0;">
                        ❌ Function editOrder() KHÔNG TỒN TẠI!<br>
                        <strong>Vấn đề:</strong> File orders.js chưa load hoặc có lỗi syntax
                    </div>
                `;
            }
        }
    </script>
</body>
</html>

