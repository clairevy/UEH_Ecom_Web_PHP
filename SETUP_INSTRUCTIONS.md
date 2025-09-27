# Hướng Dẫn Setup và Chạy Website JEWELRY

## 🚀 Các Bước Thiết Lập

### 1. Chạy Seed Data

Để có dữ liệu mẫu trong database, hãy làm theo các bước sau:

#### Cách 1: Chạy qua Web Browser (Khuyến nghị)

1. Mở trình duyệt và truy cập: `http://localhost/Ecom_website/run_seed.php`
2. Script sẽ tự động tạo dữ liệu mẫu
3. Xem kết quả và các link để test

#### Cách 2: Chạy qua Command Line

```bash
cd C:\xampp\htdocs\Ecom_website
php seed_data.php
```

### 2. Kiểm Tra Website

Sau khi chạy seed data thành công, bạn có thể test các trang:

- **Trang chủ**: `http://localhost/Ecom_website/` hoặc `http://localhost/Ecom_website/customer`
- **Danh sách sản phẩm**: `http://localhost/Ecom_website/customer/products`
- **Chi tiết sản phẩm**: `http://localhost/Ecom_website/customer/product-detail/1`

## 📊 Dữ Liệu Mẫu Được Tạo

### Collections (4)

- Luxury Gold Collection
- Diamond Star Series
- Pearl Beauty Line
- Elegant Silver

### Categories (5)

- Nhẫn (Rings)
- Dây chuyền (Necklaces)
- Bông tai (Earrings)
- Vòng tay (Bracelets)
- Đồng hồ (Watches)

### Products (8)

1. Nhẫn Kim Cương Vàng Trắng 18K - 45,000,000₫
2. Dây Chuyền Ngọc Trai Akoya - 18,500,000₫
3. Bông Tai Kim Cương Thiên Nhiên - 25,000,000₫
4. Vòng Tay Bạc 925 Charm - 2,800,000₫
5. Đồng Hồ Diamond Luxury Swiss - 45,000,000₫
6. Nhẫn Cưới Vàng Trắng 18K - 12,000,000₫
7. Dây Chuyền Vàng 18K Trái Tim - 7,800,000₫
8. Bông Tai Vàng 24K Hoa Hồng - 6,200,000₫

### Images

- Mỗi sản phẩm có 3-4 hình ảnh từ Unsplash
- Mỗi category có banner image
- Tất cả images được lưu dưới dạng URL (không cần upload file)

## 🎯 Tính Năng Hoạt Động

### ✅ Đã Hoàn Thành

- [x] Trang chủ với New Arrivals, Popular Products
- [x] Danh sách sản phẩm với filters và pagination
- [x] Chi tiết sản phẩm với related products
- [x] Search functionality
- [x] Click vào sản phẩm để xem chi tiết
- [x] Responsive design
- [x] Hover effects cho product cards

### 🔧 Chức Năng Test

1. **Navigation**: Click vào logo để về trang chủ
2. **Search**: Nhập từ khóa và search
3. **Product Cards**: Hover để xem hiệu ứng
4. **Product Links**: Click vào sản phẩm để xem chi tiết
5. **Filters**: Test category, price range filters
6. **Pagination**: Test chuyển trang
7. **Related Products**: Xem sản phẩm liên quan

## 🎨 Giao Diện

- **Layout**: Giữ nguyên 100% thiết kế ban đầu
- **Colors**: Màu sắc vàng kim, trắng, đen
- **Typography**: Font Playfair Display cho tiêu đề
- **Hover Effects**: Transform và shadow khi hover
- **Responsive**: Hoạt động tốt trên mobile

## 🔍 Troubleshooting

### Nếu gặp lỗi khi chạy seed:

1. Kiểm tra database connection trong `configs/database.php`
2. Đảm bảo database đã được tạo
3. Kiểm tra quyền truy cập database

### Nếu trang không load:

1. Kiểm tra XAMPP đang chạy
2. Kiểm tra URL có đúng không
3. Xem error log trong XAMPP

### Nếu không có dữ liệu:

1. Chạy lại seed script
2. Kiểm tra database có data không
3. Xóa cache browser

## 📝 Ghi Chú Quan Trọng

- **Images**: Sử dụng URL từ Unsplash, không cần upload file
- **Database**: Tự động tạo structure khi chạy seed
- **Performance**: Optimized với pagination và limits
- **SEO**: URL friendly với slugs
- **Security**: Sanitized inputs và prepared statements

---

