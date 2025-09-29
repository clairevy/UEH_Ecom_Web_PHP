# Component-based Jewelry Admin Dashboard

## Tổng quan

Dự án này đã được refactor để sử dụng hệ thống component giúp tái sử dụng code và dễ dàng bảo trì. Tất cả các phần tử giao diện lặp lại đã được chuyển thành các component riêng biệt.

## Cấu trúc thư mục

```
components/
├── component-manager.js     # Quản lý và load components
├── sidebar.html            # Component thanh sidebar
├── header.html             # Component header với breadcrumb
├── user-dropdown.html      # Component dropdown user
├── pagination.html         # Component phân trang
├── stats-card.html         # Component thẻ thống kê
├── product-card.html       # Component thẻ sản phẩm
├── table-card.html         # Component bảng dữ liệu
└── status-badge.html       # Component badge trạng thái
```

## Components đã tạo

### 1. Sidebar Component (`sidebar.html`)

- Thanh điều hướng bên trái
- Logo và menu chính
- Phần danh mục động
- Hỗ trợ highlight trang hiện tại

### 2. Header Component (`header.html`)

- Tiêu đề trang và breadcrumb
- Khu vực hiển thị ngày tháng (tùy chọn)
- Container cho các action buttons
- Dropdown user

### 3. User Dropdown Component (`user-dropdown.html`)

- Menu dropdown người dùng
- Profile, Settings, Logout links

### 4. Pagination Component (`pagination.html`)

- Điều khiển phân trang
- Nút Previous/Next
- Số trang

### 5. Stats Card Component (`stats-card.html`)

- Thẻ hiển thị thống kê
- Giá trị, nhãn, phần trăm thay đổi
- Hỗ trợ trend tích cực/tiêu cực

### 6. Product Card Component (`product-card.html`)

- Thẻ hiển thị sản phẩm
- Hình ảnh, tên, giá
- Thống kê bán hàng và tồn kho
- Menu dropdown actions

### 7. Table Card Component (`table-card.html`)

- Container cho bảng dữ liệu
- Header với tiêu đề và actions
- Cấu trúc table responsive

## Cách sử dụng

### 1. Thiết lập cơ bản

Trong mỗi file HTML, thêm containers và script:

```html
<!-- Sidebar Container -->
<div id="sidebar-container"></div>

<!-- Header Container -->
<div id="header-container"></div>

<!-- Component Manager -->
<script src="components/component-manager.js"></script>
<!-- hoặc ../components/component-manager.js cho files trong thư mục pages -->
```

### 2. Cấu hình trang

Thêm cấu hình cho từng trang:

```javascript
window.pageConfig = {
  sidebar: {
    brandName: "JEWELLERY",
    activePage: "dashboard", // 'dashboard', 'products', 'orders'
    links: {
      dashboard: "index.html",
      products: "pages/products.html",
      orders: "pages/orders.html",
    },
    categories: [
      { name: "Vòng cổ", count: 12 },
      { name: "Vòng tay", count: 10 },
      // ...
    ],
    categoriesTitle: "DANH MỤC",
  },
  header: {
    title: "Dashboard",
    breadcrumb: "Home > Dashboard",
    showDateRange: true, // tùy chọn
    dateRange: "Feb 16 2022 - Feb 20 2022", // tùy chọn
    additionalActions: "<button>Add Product</button>", // HTML cho buttons thêm
  },
};
```

### 3. Sử dụng Component Manager

```javascript
// Load và render sidebar
await ComponentManager.renderSidebar({
  brandName: "JEWELLERY",
  activePage: "products",
});

// Load và render header
await ComponentManager.renderHeader({
  title: "Products",
  breadcrumb: "Home > Products",
});

// Render stats card
const container = document.getElementById("stats-container");
await ComponentManager.renderStatsCard(container, {
  value: "$126.560",
  label: "Total Orders",
  percentage: "36.7%",
  comparison: "Compared to Jan 2022",
  trend: "positive",
});

// Render product card
const productContainer = document.getElementById("products-grid");
await ComponentManager.renderProductCard(productContainer, {
  name: "Adidas Ultra boost",
  category: "Sneaker",
  price: "$110.40",
  summary: "Long distance running...",
  salesCount: 1269,
  stockCount: 1269,
  image: "path/to/image.jpg",
  editLink: "product-details.html",
});
```

## Lợi ích của hệ thống Component

### 1. Tái sử dụng code

- Không cần copy-paste HTML lặp lại
- Một lần thay đổi component áp dụng cho tất cả

### 2. Dễ bảo trì

- Component tập trung tại một nơi
- Dễ dàng cập nhật giao diện

### 3. Tính nhất quán

- Đảm bảo UI/UX thống nhất
- Giảm thiểu lỗi do copy nhầm

### 4. Hiệu suất tốt hơn

- Load component theo nhu cầu
- Cache component đã load

### 5. Linh hoạt

- Cấu hình component theo từng trang
- Dễ dàng thêm tính năng mới

## Ví dụ sử dụng thực tế

### Trang Products

```javascript
window.pageConfig = {
  sidebar: {
    brandName: "JEWELLERY",
    activePage: "products",
    // ... cấu hình khác
  },
  header: {
    title: "All Products",
    breadcrumb: "Home > All Products",
    additionalActions: `
            <button class="btn btn-success-custom btn-custom">
                <img src="icon.png" alt="Add" width="16" height="16">
                ADD NEW PRODUCT
            </button>
        `,
  },
};
```

### Trang Orders với Date Range

```javascript
window.pageConfig = {
  sidebar: {
    brandName: "JEWELLERY",
    activePage: "orders",
  },
  header: {
    title: "Orders List",
    breadcrumb: "Home > Order List",
    showDateRange: true,
    dateRange: "Feb 16 2022 - Feb 20 2022",
    additionalActions: `
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle">
                    Change Status
                </button>
                <!-- dropdown menu -->
            </div>
        `,
  },
};
```

## Mở rộng trong tương lai

1. **Thêm components mới**: Tạo file HTML trong thư mục components và thêm method render tương ứng trong component-manager.js

2. **Cải thiện ComponentManager**: Thêm tính năng cache, lazy loading, hoặc template engine

3. **Tích hợp framework**: Có thể migrate sang React, Vue, hoặc Angular trong tương lai

4. **API Integration**: Kết nối với backend để load dữ liệu động cho components

## Lưu ý khi phát triển

- Luôn sử dụng `data-*` attributes để đánh dấu các phần tử cần cấu hình
- Tách biệt logic component khỏi business logic
- Đảm bảo component hoạt động độc lập
- Test component trên các trình duyệt khác nhau
- Cập nhật README khi thêm component mới
