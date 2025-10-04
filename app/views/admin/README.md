# JEWELLERY Admin Panel

Admin panel cho hệ thống quản lý trang sức JEWELLERY được xây dựng theo chuẩn OOP MVC với kết nối database XAMPP.

## 📁 Cấu trúc thư mục (Đã sắp xếp)

```
admin/
├── config/
│   └── database.php          # Cấu hình kết nối database
├── models/
│   ├── BaseModel.php         # Lớp cơ sở cho tất cả models
│   ├── ProductModel.php      # Model quản lý sản phẩm
│   ├── OrderModel.php        # Model quản lý đơn hàng
│   ├── CategoryModel.php     # Model quản lý danh mục
│   └── UserModel.php         # Model quản lý người dùng
├── controllers/
│   ├── BaseController.php    # Lớp cơ sở cho tất cả controllers
│   ├── DashboardController.php # Controller dashboard
│   ├── ProductController.php   # Controller sản phẩm
│   └── OrderController.php      # Controller đơn hàng
├── views/
│   ├── dashboard.php         # Dashboard view
│   ├── products/
│   │   └── index.php         # Danh sách sản phẩm
│   └── orders/
│       └── index.php         # Danh sách đơn hàng
├── includes/
│   ├── header.php            # Header chung
│   └── footer.php            # Footer chung
├── ajax/
│   ├── delete-product.php    # AJAX xóa sản phẩm
│   └── update-order-status.php # AJAX cập nhật trạng thái đơn hàng
├── assets/                   # CSS, JS, images
├── components/               # Các component tái sử dụng
└── index.php                 # Điểm vào chính (routing đơn giản)
```

## 🎯 Tính năng chính

### 1. Dashboard
- Hiển thị thống kê tổng quan từ database
- Biểu đồ doanh thu (placeholder)
- Sản phẩm bán chạy
- Đơn hàng gần đây

### 2. Quản lý sản phẩm
- Danh sách sản phẩm với phân trang
- Tìm kiếm và lọc theo danh mục
- Xóa sản phẩm (AJAX)
- Hiển thị thông tin chi tiết

### 3. Quản lý đơn hàng
- Danh sách đơn hàng với phân trang
- Lọc theo trạng thái
- Cập nhật trạng thái đơn hàng (AJAX)
- Xem chi tiết đơn hàng

## 🚀 Cách sử dụng

### 1. Cài đặt
1. Đảm bảo XAMPP đang chạy (Apache + MySQL)
2. Tạo database `db_ecomphp` trong phpMyAdmin
3. Import dữ liệu mẫu vào database

### 2. Truy cập
- **Dashboard:** `http://localhost/your-project/app/views/admin/`
- **Sản phẩm:** `http://localhost/your-project/app/views/admin/?page=products`
- **Đơn hàng:** `http://localhost/your-project/app/views/admin/?page=orders`

### 3. Cấu hình database
Chỉnh sửa file `config/database.php` nếu cần:
```php
private $host = '127.0.0.1';
private $db_name = 'db_ecomphp';
private $username = 'root';
private $password = '';
```

## 🏗️ Kiến trúc MVC

### Model
- Kế thừa từ `BaseModel`
- Sử dụng PDO để kết nối database
- Có thể sử dụng SQL trực tiếp hoặc stored procedures
- Xử lý business logic

### View
- Template PHP với header/footer chung
- Sử dụng Bootstrap 5 cho UI
- Responsive design
- Component-based architecture

### Controller
- Kế thừa từ `BaseController`
- Xử lý request/response
- Gọi model để lấy dữ liệu
- Render view với dữ liệu

## 🔧 Routing đơn giản

```php
// index.php - Routing cơ bản
$page = $_GET['page'] ?? 'dashboard';

switch ($page) {
    case 'products':
        // Hiển thị trang sản phẩm
        break;
    case 'orders':
        // Hiển thị trang đơn hàng
        break;
    default:
        // Hiển thị dashboard
        break;
}
```

## 📊 Database Integration

### Stored Procedures được sử dụng
- `sp_GetTopSellingProducts` - Sản phẩm bán chạy
- `sp_UpdateOrderStatus` - Cập nhật trạng thái đơn hàng
- `sp_GetOrderDetails` - Chi tiết đơn hàng
- `sp_CreateCategory` - Tạo danh mục

### SQL trực tiếp cho
- Lấy danh sách với JOIN
- Thống kê đơn giản
- Tìm kiếm và lọc

## 🎨 UI/UX Features

- **Header/Footer chung** - Tái sử dụng code
- **Responsive design** - Hoạt động trên mọi thiết bị
- **Bootstrap 5** - UI hiện đại
- **AJAX** - Cập nhật không reload trang
- **Pagination** - Phân trang thông minh
- **Search & Filter** - Tìm kiếm và lọc dữ liệu

## 🔒 Security

- **Prepared Statements** - Tránh SQL injection
- **Input validation** - Kiểm tra dữ liệu đầu vào
- **Error handling** - Xử lý lỗi an toàn
- **XSS protection** - Bảo vệ khỏi XSS

## 📝 Lưu ý

- Tất cả input đều được validate và escape
- Sử dụng prepared statements để tránh SQL injection
- Error handling đầy đủ
- Responsive design cho mobile
- Code được viết theo chuẩn OOP PHP

## 🔄 Mở rộng

Để thêm tính năng mới:
1. Tạo Model mới kế thừa từ `BaseModel`
2. Tạo Controller mới kế thừa từ `BaseController`
3. Tạo View tương ứng trong thư mục `views/`
4. Thêm route vào `index.php`
5. Tạo AJAX endpoint nếu cần