<!DOCTYPE html>
<html>
<head>
    <title>Test Signup Page</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #007bff; }
        .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; font-weight: bold; }
        .btn:hover { background: #0056b3; }
        .success { border-left-color: #28a745; background: #d4edda; }
        .error { border-left-color: #dc3545; background: #f8d7da; }
    </style>
</head>
<body>
    <h1>🧪 TEST SIGNUP PAGE</h1>
    
    <div class="box success">
        <h2>✅ Test 1: Signup Page</h2>
        <p>Kiểm tra trang signup có load được không:</p>
        <a href="../signup" class="btn">📄 Vào Signup Page</a>
        <p><small>Nếu hiển thị trang đăng ký → OK</small></p>
    </div>

    <div class="box success">
        <h2>✅ Test 2: Signin Page</h2>
        <p>Kiểm tra trang signin có load được không:</p>
        <a href="../signin" class="btn" style="background: #28a745;">📄 Vào Signin Page</a>
        <p><small>Nếu hiển thị trang đăng nhập → OK</small></p>
    </div>

    <div class="box success">
        <h2>✅ Test 3: Home Page</h2>
        <p>Kiểm tra trang chủ có load được không:</p>
        <a href="../" class="btn" style="background: #6c757d;">🏠 Vào Home Page</a>
        <p><small>Nếu hiển thị trang chủ → OK</small></p>
    </div>

    <div class="box error">
        <h2>⚠️ Nếu Có Lỗi</h2>
        <p>Kiểm tra các vấn đề sau:</p>
        <ul>
            <li><strong>Parse error:</strong> Có lỗi syntax trong PHP</li>
            <li><strong>Fatal error:</strong> Thiếu class hoặc method</li>
            <li><strong>404 Not Found:</strong> Routing không đúng</li>
            <li><strong>500 Internal Server Error:</strong> Lỗi database hoặc logic</li>
        </ul>
    </div>

    <div class="box">
        <h2>🔍 Debug Info</h2>
        <p><strong>PHP Version:</strong> <?= phpversion() ?></p>
        <p><strong>Current Time:</strong> <?= date('Y-m-d H:i:s') ?></p>
        <p><strong>Error Reporting:</strong> <?= error_reporting() ?></p>
    </div>
</body>
</html>

