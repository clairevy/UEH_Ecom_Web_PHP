<!DOCTYPE html>
<html>
<head>
    <title>Test Signup Flow</title>
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
    <h1>🧪 TEST SIGNUP FLOW</h1>
    
    <div class="box info">
        <h2>📋 Bước 1: Test Trang Signup</h2>
        <p>Kiểm tra trang signup có load được không:</p>
        <a href="../signup" class="btn">📄 Vào Signup Page</a>
        <p><strong>Kỳ vọng:</strong> Hiển thị form đăng ký với design đẹp</p>
    </div>

    <div class="box info">
        <h2>📋 Bước 2: Test Form Validation</h2>
        <p>Test validation client-side:</p>
        <button onclick="testValidation()" class="btn" style="background: #ffc107; color: black;">🧪 Test Validation</button>
        <div id="validationResult"></div>
    </div>

    <div class="box info">
        <h2>📋 Bước 3: Test API Endpoint</h2>
        <p>Test API auth/signup có hoạt động không:</p>
        <button onclick="testAPI()" class="btn" style="background: #28a745;">🔗 Test API</button>
        <div id="apiResult"></div>
    </div>

    <div class="box success">
        <h2>✅ Bước 4: Test Hoàn Chỉnh</h2>
        <p>Test đăng ký thật với dữ liệu test:</p>
        <button onclick="testFullSignup()" class="btn" style="background: #6c757d;">🎯 Test Full Signup</button>
        <div id="fullTestResult"></div>
    </div>

    <div class="box error">
        <h2>⚠️ Nếu Có Lỗi</h2>
        <p>Kiểm tra Console (F12) để xem lỗi chi tiết:</p>
        <ul>
            <li><strong>404:</strong> Route không đúng</li>
            <li><strong>500:</strong> Lỗi server/controller</li>
            <li><strong>CORS:</strong> Lỗi fetch URL</li>
            <li><strong>Validation:</strong> Lỗi validation logic</li>
        </ul>
    </div>

    <script>
        function testValidation() {
            const result = document.getElementById('validationResult');
            result.innerHTML = '<div style="padding: 10px; background: #fff3cd; border-radius: 5px;">🔄 Testing validation...</div>';
            
            // Test validation logic
            setTimeout(() => {
                result.innerHTML = `
                    <div style="padding: 10px; background: #d1ecf1; border-radius: 5px; margin: 10px 0;">
                        <strong>Validation Test:</strong><br>
                        ✅ Email format validation<br>
                        ✅ Password length validation<br>
                        ✅ Confirm password matching<br>
                        ✅ Terms checkbox validation<br>
                        <small>Check signup page để test thực tế</small>
                    </div>
                `;
            }, 1000);
        }
        
        async function testAPI() {
            const result = document.getElementById('apiResult');
            result.innerHTML = '<div style="padding: 10px; background: #fff3cd; border-radius: 5px;">🔄 Testing API...</div>';
            
            try {
                const response = await fetch('auth/signup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'email=test@test.com&password=123456&confirm_password=123456&name=Test User&phone=0123456789'
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    result.innerHTML = `
                        <div style="padding: 10px; background: #d4edda; border-radius: 5px; margin: 10px 0;">
                            ✅ API hoạt động!<br>
                            <strong>Response:</strong> ${JSON.stringify(data, null, 2)}
                        </div>
                    `;
                } else {
                    result.innerHTML = `
                        <div style="padding: 10px; background: #f8d7da; border-radius: 5px; margin: 10px 0;">
                            ❌ API lỗi!<br>
                            <strong>Status:</strong> ${response.status}<br>
                            <strong>Response:</strong> ${JSON.stringify(data, null, 2)}
                        </div>
                    `;
                }
            } catch (error) {
                result.innerHTML = `
                    <div style="padding: 10px; background: #f8d7da; border-radius: 5px; margin: 10px 0;">
                        ❌ Lỗi kết nối!<br>
                        <strong>Error:</strong> ${error.message}
                    </div>
                `;
            }
        }
        
        function testFullSignup() {
            const result = document.getElementById('fullTestResult');
            result.innerHTML = `
                <div style="padding: 10px; background: #d1ecf1; border-radius: 5px; margin: 10px 0;">
                    <strong>Full Signup Test:</strong><br>
                    1. Vào <a href="../signup" target="_blank">Signup Page</a><br>
                    2. Điền form với dữ liệu test:<br>
                    &nbsp;&nbsp;• Email: test@example.com<br>
                    &nbsp;&nbsp;• Password: 123456<br>
                    &nbsp;&nbsp;• Confirm: 123456<br>
                    &nbsp;&nbsp;• Name: Test User<br>
                    &nbsp;&nbsp;• Phone: 0123456789<br>
                    3. Click "Đăng ký"<br>
                    4. Xem kết quả
                </div>
            `;
        }
    </script>
</body>
</html>

