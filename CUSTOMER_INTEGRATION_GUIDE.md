# Hướng Dẫn Tích Hợp Customer với Database

## Tổng Quan

Đã hoàn thành việc tích hợp các trang customer với database và controller hiện có. Giữ nguyên layout và thiết kế, chỉ thay thế dữ liệu tĩnh bằng dữ liệu từ database.

## Các Thay Đổi Đã Thực Hiện

### 1. CustomerController (app/controllers/CustomerController.php)

- **Tạo mới**: Controller chuyên xử lý các trang customer
- **Các method chính**:
  - `index()`: Trang chủ customer với new arrivals, popular products
  - `products()`: Danh sách sản phẩm với filters và pagination
  - `productDetail($productId)`: Chi tiết sản phẩm với related products
  - `searchApi()`, `getNewArrivals()`, `getPopularProducts()`: API endpoints

### 2. Product Model (app/models/Product.php)

- **Thêm methods mới**:
  - `getNewArrivals($limit)`: Lấy sản phẩm mới nhất
  - `getPopularProducts($limit)`: Lấy sản phẩm phổ biến
  - `getFeaturedProducts($limit)`: Lấy sản phẩm nổi bật
  - `getPriceRange()`: Lấy khoảng giá tổng thể
  - `searchProductsPaginated()`, `getTotalSearchResults()`: Tìm kiếm có phân trang
  - `getFilteredProducts()`: Lọc sản phẩm theo tiêu chí

### 3. Router (configs/router.php)

- **Cập nhật routing**:
  - Mặc định chuyển về CustomerController thay vì HomeController
  - Xử lý các route customer đặc biệt:
    - `/customer` → CustomerController->index()
    - `/customer/products` → CustomerController->products()
    - `/customer/product-detail/{id}` → CustomerController->productDetail()
    - `/customer/api/new-arrivals` → API new arrivals
    - `/customer/api/popular` → API popular products

### 4. Trang Home (app/views/customer/pages/home.php)

- **Tích hợp database**:
  - New Arrivals và Popular Products lấy từ API
  - Categories hiển thị từ database với banner images
  - Fallback data nếu API fail
  - Search functionality chuyển hướng đến trang products

### 5. Trang List Products (app/views/customer/pages/list-product.php)

- **Tích hợp database**:
  - Sản phẩm render từ PHP với dữ liệu database
  - Categories filter từ database
  - Pagination hoạt động với database
  - Search functionality tích hợp
  - Filter và sort reload trang với parameters

### 6. Trang Product Details (app/views/customer/pages/details-product.php)

- **Tích hợp database**:
  - Thông tin sản phẩm từ database
  - Hình ảnh sản phẩm từ database với fallback
  - Related products từ cùng collection
  - Breadcrumb động từ categories
  - Reviews và ratings (nếu có data)

## Cách Sử Dụng

### 1. Truy Cập Các Trang

- **Trang chủ**: `http://localhost/Ecom_website/` hoặc `http://localhost/Ecom_website/customer`
- **Danh sách sản phẩm**: `http://localhost/Ecom_website/customer/products`
- **Chi tiết sản phẩm**: `http://localhost/Ecom_website/customer/product-detail/{product_id}`

### 2. Chức Năng Search

- Từ trang chủ: Nhập từ khóa và nhấn search → chuyển đến trang products với kết quả
- Từ trang products: Search sẽ reload trang với parameter search

### 3. Filter và Sort (Trang Products)

- **Category Filter**: Checkbox categories từ database
- **Price Range**: Radio buttons hoặc custom min/max
- **Sort**: Dropdown với các tùy chọn (newest, price, popular)
- **Pagination**: Hoạt động với tất cả filters

### 4. API Endpoints

- `GET /customer/api/new-arrivals?limit=8`: Lấy sản phẩm mới
- `GET /customer/api/popular?limit=8`: Lấy sản phẩm phổ biến
- `GET /customer/search?q=keyword`: Tìm kiếm sản phẩm

## Cấu Trúc Database Cần Thiết

Đảm bảo các bảng sau có dữ liệu:

- `products`: Sản phẩm với `is_active = 1`
- `categories`: Danh mục sản phẩm
- `images` + `image_usages`: Hình ảnh sản phẩm và categories
- `product_categories`: Liên kết sản phẩm với danh mục
- `collection`: Bộ sưu tập (optional)

## Lưu Ý Kỹ Thuật

1. **Fallback Data**: Tất cả trang đều có fallback nếu không có data từ database
2. **Image Paths**: Sử dụng đường dẫn từ database với fallback images
3. **Error Handling**: Controller có xử lý khi không tìm thấy sản phẩm
4. **Performance**: Sử dụng pagination và limit để tránh load quá nhiều data
5. **SEO Friendly**: URL có parameter rõ ràng cho search và filter

## Kiểm Tra Hoạt Động

1. Truy cập trang chủ và kiểm tra New Arrivals, Popular Products hiển thị
2. Test search functionality từ trang chủ
3. Vào trang products, test filters và pagination
4. Click vào sản phẩm để xem chi tiết
5. Kiểm tra related products trong trang detail

Tất cả chức năng đã được tích hợp hoàn chỉnh với database trong khi vẫn giữ nguyên giao diện và trải nghiệm người dùng ban đầu.
