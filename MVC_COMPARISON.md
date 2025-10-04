# MVC Comparison: Basic vs Clean Architecture

## 🔴 **TRƯỚC ĐÂY: Basic MVC (Fat Model)**

### Controller (CustomerController.php)

```php
class CustomerController extends BaseController {
    public function products() {
        // ❌ Controller phải biết về business logic
        $categorySlug = $_GET['category'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 12;

        $productModel = $this->model('Product');

        if ($categorySlug) {
            $products = $productModel->getProductsByCategory($categorySlug, $page, $limit);
        } else {
            $products = $productModel->getFilteredProducts($_GET, $page, $limit);
        }

        $this->view('customer/pages/list-product', ['products' => $products]);
    }
}
```

### Model (Product.php - Fat Model)

```php
class Product extends BaseModel {
    // ❌ Model chứa cả CRUD + Business Logic + Complex Queries
    public function getProductsByCategory($categorySlug, $page = 1, $limit = 12) {
        // 50 lines complex SQL với JOINs
    }

    public function getFilteredProducts($filters, $page = 1, $limit = 12) {
        // 80 lines dynamic SQL building
    }

    public function getNewArrivals($limit = 8) {
        // 30 lines business logic
    }

    public function getPopularProducts($limit = 8) {
        // 40 lines với view counts
    }

    public function searchProductsPaginated($search, $page = 1, $limit = 12) {
        // 60 lines search logic
    }

    // + 15 methods khác... = 559 lines!
}
```

### ❌ **Vấn đề của Basic MVC:**

- **Fat Model**: Quá nhiều responsibility
- **Business Logic lộn xộn**: Khó maintain
- **Hard to test**: Logic trộn lẫn với database
- **Code duplication**: Logic lặp ở nhiều nơi

---

## 🟢 **BÂY GIỜ: Clean MVC với Service Layer**

### Controller (CustomerControllerClean.php - Thin Controller)

```php
class CustomerControllerClean extends BaseController {
    private $productService;

    public function __construct() {
        $this->productService = new ProductService();
    }

    public function products() {
        // ✅ Controller chỉ xử lý HTTP, không biết business logic
        try {
            $filters = $_GET;
            $page = (int)($_GET['page'] ?? 1);

            $result = $this->productService->getProductsWithFilters($filters, $page, 12);

            $this->view('customer/pages/list-product', [
                'products' => $result['products'],
                'totalPages' => $result['totalPages'],
                'currentPage' => $page
            ]);
        } catch (Exception $e) {
            $this->view('errors/500', ['message' => $e->getMessage()]);
        }
    }
}
```

### Service (ProductService.php - Business Logic Layer)

```php
class ProductService extends BaseModel {
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
    }

    // ✅ Tất cả business logic ở đây
    public function getProductsWithFilters($filters = [], $page = 1, $limit = 12) {
        // Complex filtering logic
        // Dynamic SQL building
        // Pagination calculation
        // Image attachment
        // Data transformation
        return ['products' => $products, 'totalPages' => $totalPages];
    }

    public function getProductWithFullDetails($slug) {
        // Product + Images + Reviews + Related products
    }

    public function getNewArrivals($limit = 8) {
        // Business logic for new arrivals
    }
}
```

### Model (Product.php - Thin Model)

```php
class Product extends BaseModel {
    protected $table = 'products';

    // ✅ Chỉ CRUD operations, không có business logic
    public function create($data) {
        // Simple INSERT
    }

    public function findById($id) {
        // Simple SELECT by ID
    }

    public function findBySlug($slug) {
        // Simple SELECT by slug
    }

    public function update($id, $data) {
        // Simple UPDATE
    }

    public function delete($id) {
        // Simple DELETE
    }

    public function getAll() {
        // Simple SELECT *
    }

    // Chỉ 6 methods CRUD = 130 lines sạch sẽ!
}
```

---

## ✅ **Ưu điểm của Clean MVC:**

### 🎯 **1. Separation of Concerns**

- **Controller**: Chỉ HTTP handling
- **Service**: Chỉ business logic
- **Model**: Chỉ database operations
- **View**: Chỉ presentation logic

### 🧪 **2. Testability**

```php
// Dễ unit test Service layer
class ProductServiceTest {
    public function testGetProductsWithFilters() {
        $service = new ProductService();
        $result = $service->getProductsWithFilters(['category' => 'electronics']);
        $this->assertNotEmpty($result['products']);
    }
}
```

### 🔄 **3. Reusability**

```php
// Service có thể dùng ở nhiều nơi
class AdminController {
    public function dashboard() {
        $productService = new ProductService();
        $newProducts = $productService->getNewArrivals(5);
    }
}

class ApiController {
    public function getProducts() {
        $productService = new ProductService();
        return $productService->getProductsWithFilters($_GET);
    }
}
```

### 🛠️ **4. Maintainability**

- Sửa business logic → chỉ sửa Service
- Thêm field database → chỉ sửa Model
- Thay đổi UI → chỉ sửa View
- Thêm API endpoint → chỉ thêm Controller

---

## 📊 **Kết luận:**

| Aspect              | Basic MVC       | Clean MVC + Service Layer |
| ------------------- | --------------- | ------------------------- |
| **Vẫn là MVC?**     | ✅ Có           | ✅ Có (nâng cao)          |
| **Model**           | Fat (559 lines) | Thin (130 lines)          |
| **Business Logic**  | ❌ Trong Model  | ✅ Trong Service          |
| **Controller**      | ❌ Thick        | ✅ Thin                   |
| **Testability**     | ❌ Khó          | ✅ Dễ                     |
| **Maintainability** | ❌ Khó          | ✅ Dễ                     |
| **Code Reuse**      | ❌ Ít           | ✅ Nhiều                  |

**Kết quả: Vẫn là MVC nhưng clean, maintainable và professional hơn!** 🚀
