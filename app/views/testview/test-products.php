<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test ProductController</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .test-links { display: grid; gap: 15px; }
        .test-link { display: block; padding: 15px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; text-align: center; font-weight: bold; }
        .test-link:hover { background: #0056b3; }
        .description { background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 30px; }
        .status { margin: 20px 0; padding: 10px; border-radius: 5px; }
        .status.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status.warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .note { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test ProductController</h1>
        
        <div class="description">
            <h3>Hướng dẫn test</h3>
            <p>Các link bên dưới sẽ kiểm tra tất cả methods trong <code>ProductController</code>:</p>
            <ul>
                <li><strong>index():</strong> Hiển thị tất cả sản phẩm với phân trang</li>
                <li><strong>category():</strong> Hiển thị sản phẩm theo danh mục với bộ lọc</li>
                <li><strong>show():</strong> Hiển thị chi tiết sản phẩm</li>
                <li><strong>search():</strong> Tìm kiếm sản phẩm</li>
            </ul>
        </div>

        <div class="status success">
            ✅ <strong>ProductController đã sẵn sàng!</strong> Các views test đã được tạo trong folder <code>testview/products/</code>
        </div>

        <div class="test-links">
            <a href="/Ecom_website/products" class="test-link">
                📦 Test: Danh sách tất cả sản phẩm (index)
            </a>
            
            <a href="/Ecom_website/products/search" class="test-link">
                🔍 Test: Tìm kiếm sản phẩm (search)
            </a>
            
            <a href="/Ecom_website/products/category/ao-thun" class="test-link">
                📂 Test: Sản phẩm theo danh mục (category)
            </a>
            
            <a href="/Ecom_website/products/show/sample-product" class="test-link">
                👁️ Test: Chi tiết sản phẩm (show)
            </a>
        </div>

        <div class="note">
            <h4>📝 Lưu ý quan trọng:</h4>
            <ul>
                <li>Đảm bảo database có dữ liệu test (products, categories, collections)</li>
                <li>Các link category và show cần có slug thực tế trong database</li>
                <li>Nếu chưa có dữ liệu, tạo một vài records test trong database</li>
                <li>Check Apache đang chạy trên port 8080: <code>http://localhost:8080/Ecom_website/</code></li>
            </ul>
        </div>

        <div class="status warning">
            ⚠️ <strong>Cần kiểm tra:</strong> Đảm bảo file .htaccess đã được cấu hình đúng cho URL rewriting
        </div>

        <!-- Quick Database Check -->
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
            <h4>🗄️ Kiểm tra nhanh database:</h4>
            <p>Mở phpMyAdmin và kiểm tra các bảng sau có dữ liệu:</p>
            <ul>
                <li><code>products</code> - Có ít nhất 1 sản phẩm</li>
                <li><code>categories</code> - Có ít nhất 1 danh mục</li>
                <li><code>collections</code> - Có ít nhất 1 collection (optional)</li>
            </ul>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #dee2e6; color: #666;">
            <p>🚀 <strong>ProductController Test Suite</strong> - Tạo bởi GitHub Copilot</p>
            <a href="/Ecom_website/">← Quay về trang chủ</a>
        </div>
    </div>
</body>
</html>