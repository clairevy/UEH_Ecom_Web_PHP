<?php
/**
 * ProductsController - Admin Product Management
 * Quản lý sản phẩm cho admin - Tuân thủ chuẩn MVC và OOP
 */
class ProductsController extends BaseController {
    private $productModel;
    private $categoryModel;
    private $collectionModel;
    
    // Upload configuration
    private $projectRoot;
    private $uploadPath;
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $maxFileSize = 5242880; // 5MB

    public function __construct() {
        // Initialize Models - Dependency Injection pattern
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->collectionModel = $this->model('Collection');
        
        // Set absolute upload path - ensure we point to project root (not /app)
        $this->projectRoot = dirname(__DIR__, 3);
        $this->uploadPath = $this->projectRoot 
            . DIRECTORY_SEPARATOR . 'public'
            . DIRECTORY_SEPARATOR . 'uploads'
            . DIRECTORY_SEPARATOR . 'products'
            . DIRECTORY_SEPARATOR;
    }

    /**
     * Hiển thị danh sách sản phẩm
     */
    public function index() {
        try {
            $products = $this->productModel->getAllWithDetails();
            
            // Load images từ database cho mỗi product (theo đúng schema images + image_usages)
            foreach ($products as $product) {
                $product->images = $this->productModel->getProductImages($product->product_id);
                $product->primary_image = $this->productModel->getProductPrimaryImage($product->product_id);
                
                // Fallback: Nếu không có image từ image_usages, dùng main_image column
                if (!$product->primary_image && !empty($product->main_image)) {
                    $product->primary_image = (object)[
                        'file_path' => $product->main_image,
                        'is_primary' => 1
                    ];
                }
            }
            
            $categories = $this->categoryModel->getAll();
            $collections = $this->collectionModel->getAll();

            $data = [
                'title' => 'Sản Phẩm',
                'products' => $products,
                'categories' => $categories,
                'collections' => $collections
            ];

            // Render admin page with layout
            $this->renderAdminPage('admin/pages/products', $data);

        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Hiển thị form thêm sản phẩm mới
     * Method: GET
     */
    public function showAddForm() {
        try {
            // Lấy dữ liệu từ Models
            $categories = $this->categoryModel->getAll();
            $collections = $this->collectionModel->getAll();
            
            // Lấy danh sách materials từ Product Model
            $materials = $this->productModel->getAvailableMaterials();
            
            // Prepare data cho View
            $data = [
                'title' => 'Thêm Sản Phẩm Mới',
                'categories' => $categories,
                'collections' => $collections,
                'materials' => $materials,
                'pageTitle' => 'Thêm Sản Phẩm',
                'breadcrumb' => 'Home > Sản Phẩm > Thêm Mới'
            ];

            // Render view qua layout
            $this->renderAdminPage('admin/pages/add-product', $data);

        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect('index.php?url=products');
        }
    }

    /**
     * Tạo sản phẩm mới
     * Method: POST
     */
    public function create() {
        // ===== CRITICAL DEBUG START =====
        $debugLog = "=== PRODUCT CREATE METHOD CALLED " . date('Y-m-d H:i:s') . " ===\n";
        $debugLog .= "Request Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
        $debugLog .= "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
        $debugLog .= "POST data: " . json_encode($_POST, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        $debugLog .= "FILES data: " . json_encode($_FILES, JSON_PRETTY_PRINT) . "\n";
        file_put_contents(__DIR__ . '/../../logs/controller_debug.log', $debugLog, FILE_APPEND);
        error_log("🎯 ProductsController::create() METHOD CALLED!");
        // ===== CRITICAL DEBUG END =====
        
        // Chỉ chấp nhận POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("❌ Not POST request: " . $_SERVER['REQUEST_METHOD']);
            $_SESSION['error'] = 'Invalid request method!';
            $this->redirect('index.php?url=products');
            return;
        }

        // ===== DEBUG LOGGING START =====
        error_log("=== PRODUCT CREATE DEBUG START ===");
        error_log("POST data: " . json_encode($_POST, JSON_UNESCAPED_UNICODE));
        error_log("FILES data: " . json_encode($_FILES));
        error_log("Files count: " . (isset($_FILES['product_images']) ? count($_FILES['product_images']['name']) : 'NO FILES'));
        
        if (isset($_FILES['product_images'])) {
            foreach ($_FILES['product_images']['name'] as $index => $name) {
                if (!empty($name)) {
                    error_log("File $index: $name, Size: " . $_FILES['product_images']['size'][$index] . ", Error: " . $_FILES['product_images']['error'][$index]);
                }
            }
        }
        error_log("Upload path: " . $this->uploadPath);
        // ===== DEBUG LOGGING END =====

        try {
            // Validate input data
            $validationErrors = $this->validateProductData($_POST);
            
            // Validate images - BẮT BUỘC phải có ảnh
            if (empty($_FILES['product_images']['name'][0])) {
                $validationErrors[] = 'Phải upload ít nhất 1 ảnh sản phẩm';
            }
            
            if (!empty($validationErrors)) {
                $_SESSION['error'] = 'Lỗi validate: ' . implode('<br>', $validationErrors);
                $_SESSION['old_input'] = $_POST; // Giữ lại dữ liệu cũ
                $this->redirect('index.php?url=add-product');
                return;
            }

            // Prepare data cho Model
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'material' => $_POST['material'] ?? 'gold',
                'base_price' => floatval($_POST['base_price']),
                'sku' => trim($_POST['sku']),
                'collection_id' => !empty($_POST['collection_id']) ? intval($_POST['collection_id']) : null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Prepare variants data
            $variants = [];
            if (isset($_POST['variants']) && is_array($_POST['variants'])) {
                foreach ($_POST['variants'] as $variant) {
                    if (!empty($variant['size']) && !empty($variant['color']) && 
                        isset($variant['price']) && isset($variant['stock'])) {
                        $variants[] = [
                            'size' => trim($variant['size']),
                            'color' => trim($variant['color']),
                            'price' => floatval($variant['price']),
                            'stock' => intval($variant['stock'])
                        ];
                    }
                }
            }

            // Tạo sản phẩm với variants trong database (using transaction)
            error_log("=== CREATING PRODUCT ===");
            error_log("Product data: " . json_encode($data, JSON_UNESCAPED_UNICODE));
            error_log("Variants count: " . count($variants));
            
            $productId = $this->productModel->createWithVariants($data, $variants);
            
            if (!$productId) {
                error_log("❌ CREATE PRODUCT FAILED");
                throw new Exception('Không thể tạo sản phẩm trong database. Kiểm tra error log để biết chi tiết.');
            }
            
            error_log("✅ Product created with ID: " . $productId);

            // Xử lý upload images - BẮT BUỘC
            $uploadResult = $this->handleProductImages($productId, $_FILES['product_images']);
            
            if (!$uploadResult['success']) {
                // Nếu upload fail, XÓA sản phẩm đã tạo
                $this->productModel->delete($productId);
                throw new Exception('Không thể upload ảnh sản phẩm: ' . implode(', ', $uploadResult['errors']));
            }

            // Xử lý categories (many-to-many relationship)
            if (!empty($_POST['category_ids']) && is_array($_POST['category_ids'])) {
                $this->attachCategoriesToProduct($productId, $_POST['category_ids']);
            }

            $_SESSION['success'] = 'Tạo sản phẩm thành công!';
            $this->redirect('index.php?url=products');
            
        } catch (Exception $e) {
            error_log('ProductsController::create Exception: ' . $e->getMessage());
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            $_SESSION['old_input'] = $_POST;
            $this->redirect('index.php?url=add-product');
        }
    }

    /**
     * Cập nhật sản phẩm
     * Method: POST
     */
    public function update() {
        // Chỉ chấp nhận POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method!';
            $this->redirect('index.php?url=products');
            return;
        }

        try {
            $productId = $_GET['id'] ?? null;
            
            if (!$productId) {
                throw new Exception('Không tìm thấy ID sản phẩm');
            }

            // Validate input
            $validationErrors = $this->validateProductData($_POST);
            
            if (!empty($validationErrors)) {
                $_SESSION['error'] = implode('<br>', $validationErrors);
                $this->redirect('index.php?url=edit-product&id=' . $productId);
                return;
            }

            // Prepare data - CHỈ update fields được gửi lên
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'material' => $_POST['material'] ?? 'gold',
                'base_price' => floatval($_POST['base_price']),
                'sku' => trim($_POST['sku']),
                'collection_id' => !empty($_POST['collection_id']) ? intval($_POST['collection_id']) : null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // KHÔNG tự động update slug khi update (giữ nguyên slug cũ)
            // Chỉ update slug nếu tên thay đổi hoặc được gửi lên explicitly
            // Điều này tránh lỗi duplicate slug

            // Update product trong database
            if ($this->productModel->update($productId, $data)) {
                // Xử lý upload images mới (nếu có)
                if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
                    $uploadResult = $this->handleProductImages($productId, $_FILES['product_images']);
                    
                    if (!$uploadResult['success']) {
                        error_log('Warning: Product updated but image upload failed: ' . implode(', ', $uploadResult['errors']));
                    }
                }

                // Xử lý categories (nếu có)
                if (isset($_POST['category_ids']) && is_array($_POST['category_ids'])) {
                    $this->attachCategoriesToProduct($productId, $_POST['category_ids']);
                }

                $_SESSION['success'] = 'Cập nhật sản phẩm thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật sản phẩm!';
            }
            
            error_log("Product #$productId updated successfully");
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            error_log('ProductsController::update Error: ' . $e->getMessage());
        }
        
        $this->redirect('index.php?url=products');
    }

    /**
     * Hiển thị form chỉnh sửa sản phẩm
     * Sử dụng cùng view với product-details (view/edit mode)
     */
    public function showEditForm() {
        try {
            $productId = $_GET['id'] ?? null;
            
            if (!$productId) {
                throw new Exception('Không tìm thấy ID sản phẩm');
            }

            $product = $this->productModel->findById($productId);
            
            if (!$product) {
                throw new Exception('Sản phẩm không tồn tại');
            }

            // Load images từ database (ĐÚNG SCHEMA)
            $product->images = $this->productModel->getProductImages($productId);
            $product->primary_image = $this->productModel->getProductPrimaryImage($productId);

            $categories = $this->categoryModel->getAll();
            $collections = $this->collectionModel->getAll();
            $materials = $this->productModel->getAvailableMaterials();
            
            $data = [
                'title' => 'Chỉnh Sửa Sản Phẩm',
                'product' => $product,
                'categories' => $categories,
                'collections' => $collections,
                'materials' => $materials,
                'pageTitle' => 'Chỉnh Sửa Sản Phẩm',
                'breadcrumb' => 'Home > Sản Phẩm > Chỉnh Sửa',
                'editMode' => true  // Flag để view biết đây là edit mode
            ];

            // Sử dụng product-details.php (không phải edit-product.php)
            $this->renderAdminPage('admin/pages/product-details', $data);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('index.php?url=products');
        }
    }

    /**
     * Hiển thị chi tiết sản phẩm (View mode)
     */
    public function showDetails() {
        try {
            $productId = $_GET['id'] ?? null;
            
            if (!$productId) {
                throw new Exception('Không tìm thấy ID sản phẩm');
            }

            $product = $this->productModel->findById($productId);
            
            if (!$product) {
                throw new Exception('Sản phẩm không tồn tại');
            }

            // Load images từ database (ĐÚNG SCHEMA: images + image_usages)
            $product->images = $this->productModel->getProductImages($productId);
            $product->primary_image = $this->productModel->getProductPrimaryImage($productId);

            // Lấy thêm data cho dropdown (nếu cần chỉnh sửa)
            $categories = $this->categoryModel->getAll();
            $collections = $this->collectionModel->getAll();
            $materials = $this->productModel->getAvailableMaterials();
            
            $data = [
                'title' => 'Chi Tiết Sản Phẩm',
                'product' => $product,
                'categories' => $categories,
                'collections' => $collections,
                'materials' => $materials,
                'pageTitle' => 'Chi Tiết Sản Phẩm',
                'breadcrumb' => 'Home > Sản Phẩm > Chi Tiết',
                'editMode' => false  // View mode only
            ];

            $this->renderAdminPage('admin/pages/product-details', $data);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('index.php?url=products');
        }
    }
    
    /**
     * Xóa sản phẩm (soft delete)
     */
    public function delete($id) {
        try {
            if ($this->productModel->delete($id)) {
                $_SESSION['success'] = 'Xóa sản phẩm thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa sản phẩm!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        $this->redirect('index.php?url=products');
    }

    // =================== PRIVATE HELPER METHODS (OOP Best Practice) ===================
    
    /**
     * Validate dữ liệu sản phẩm
     * @return array Mảng các lỗi validation
     */
    private function validateProductData($data) {
        $errors = [];

        // Validate tên sản phẩm
        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            $errors[] = 'Tên sản phẩm phải có ít nhất 3 ký tự';
        }

        // Validate giá
        if (empty($data['base_price']) || !is_numeric($data['base_price']) || $data['base_price'] <= 0) {
            $errors[] = 'Giá sản phẩm phải là số dương';
        }

        // Validate SKU
        if (empty($data['sku'])) {
            $errors[] = 'SKU không được để trống';
        }

        // Validate categories
        if (empty($data['category_ids']) || !is_array($data['category_ids'])) {
            $errors[] = 'Phải chọn ít nhất 1 danh mục';
        }

        // Validate variants (nếu có)
        if (isset($data['variants']) && is_array($data['variants']) && !empty($data['variants'])) {
            foreach ($data['variants'] as $index => $variant) {
                $variantNum = $index + 1;
                
                // Skip empty variants
                if (empty($variant['size']) && empty($variant['color']) && empty($variant['price']) && empty($variant['stock'])) {
                    continue;
                }
                
                if (empty($variant['size'])) {
                    $errors[] = "Biến thể #{$variantNum}: Size không được để trống";
                }
                
                if (empty($variant['color'])) {
                    $errors[] = "Biến thể #{$variantNum}: Màu sắc không được để trống";
                }
                
                if (empty($variant['price']) || !is_numeric($variant['price']) || $variant['price'] <= 0) {
                    $errors[] = "Biến thể #{$variantNum}: Giá phải là số dương";
                }
                
                if (!isset($variant['stock']) || !is_numeric($variant['stock']) || $variant['stock'] < 0) {
                    $errors[] = "Biến thể #{$variantNum}: Số lượng tồn kho phải là số không âm";
                }
            }
        }

        return $errors;
    }

    /**
     * Xử lý upload ảnh sản phẩm theo đúng database schema
     * Lưu vào tables: images + image_usages
     * @return array ['success' => bool, 'errors' => array]
     */
    private function handleProductImages($productId, $files) {
        error_log("=== HANDLE PRODUCT IMAGES DEBUG ===");
        error_log("Product ID: " . $productId);
        error_log("Files parameter: " . json_encode($files));
        
        $result = ['success' => true, 'errors' => []];
        
        try {
            error_log('Upload base path: ' . $this->uploadPath);
            // Tạo thư mục upload nếu chưa tồn tại
            $productUploadPath = $this->uploadPath . $productId . DIRECTORY_SEPARATOR;
            error_log('Resolved product upload path: ' . $productUploadPath);
            if (!is_dir($productUploadPath)) {
                if (!mkdir($productUploadPath, 0777, true)) {
                    throw new Exception("Không thể tạo thư mục upload: $productUploadPath");
                }
            }
            
            // Kiểm tra quyền ghi
            if (!is_writable($productUploadPath)) {
                throw new Exception("Thư mục upload không có quyền ghi: $productUploadPath");
            }

            $uploadedFiles = [];
            $fileCount = count($files['name']);
            
            if ($fileCount === 0 || empty($files['name'][0])) {
                throw new Exception('Không có file nào được upload');
            }

            for ($i = 0; $i < $fileCount; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $fileName = $files['name'][$i];
                    $fileTmpName = $files['tmp_name'][$i];
                    $fileSize = $files['size'][$i];
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    // Validate file extension
                    if (!in_array($fileExt, $this->allowedExtensions)) {
                        $result['errors'][] = "File {$fileName}: Định dạng không hợp lệ";
                        continue;
                    }

                    // Validate file size
                    if ($fileSize > $this->maxFileSize) {
                        $result['errors'][] = "File {$fileName}: Kích thước quá lớn (max 5MB)";
                        continue;
                    }

                    // Generate unique filename
                    $newFileName = 'product_' . $productId . '_' . uniqid() . '.' . $fileExt;
                    $destination = $productUploadPath . $newFileName;

                    // Move uploaded file
                    if (move_uploaded_file($fileTmpName, $destination)) {
                        // Convert absolute path to relative path for web display
                        $relativePath = 'public/uploads/products/' . $productId . '/' . $newFileName;
                        
                        $uploadedFiles[] = [
                            'path' => $relativePath, // Use relative path for database
                            'is_primary' => ($i === 0) // First image is primary
                        ];
                    } else {
                        $result['errors'][] = "Không thể upload file {$fileName}. Tmp: {$fileTmpName}, Dest: {$destination}";
                        error_log("Upload failed - Source: {$fileTmpName}, Destination: {$destination}");
                        error_log("Source exists: " . (file_exists($fileTmpName) ? 'Yes' : 'No'));
                        error_log("Destination dir exists: " . (is_dir(dirname($destination)) ? 'Yes' : 'No'));
                        error_log("Destination dir writable: " . (is_writable(dirname($destination)) ? 'Yes' : 'No'));
                    }
                }
            }

            // Save images vào database theo ĐÚNG SCHEMA (images + image_usages)
            if (!empty($uploadedFiles)) {
                foreach ($uploadedFiles as $fileData) {
                    // Sử dụng Product Model method để insert vào images + image_usages
                    $usageId = $this->productModel->addProductImage(
                        $productId, 
                        $fileData['path'], 
                        $fileData['is_primary']
                    );
                    
                    if (!$usageId) {
                        $result['errors'][] = "Không thể lưu ảnh vào database: " . $fileData['path'];
                    }
                }
                
                // Update main_image column (backward compatibility)
                $mainImagePath = $uploadedFiles[0]['path']; // Already relative path
                $this->productModel->update($productId, ['main_image' => $mainImagePath]);
            }

            if (!empty($result['errors'])) {
                $result['success'] = false;
            }

        } catch (Exception $e) {
            $result['success'] = false;
            $result['errors'][] = $e->getMessage();
            error_log('handleProductImages Error: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Gắn categories cho product (many-to-many)
     */
    private function attachCategoriesToProduct($productId, $categoryIds) {
        try {
            // Xóa các liên kết cũ
            $sql = "DELETE FROM product_categories WHERE product_id = :product_id";
            $db = Database::getInstance();
            $db->query($sql);
            $db->bind(':product_id', $productId);
            $db->execute();

            // Thêm các liên kết mới
            foreach ($categoryIds as $categoryId) {
                $sql = "INSERT INTO product_categories (product_id, category_id) 
                        VALUES (:product_id, :category_id)";
                $db->query($sql);
                $db->bind(':product_id', $productId);
                $db->bind(':category_id', $categoryId);
                $db->execute();
            }
        } catch (Exception $e) {
            error_log('Error attaching categories: ' . $e->getMessage());
        }
    }

    /**
     * Redirect helper method
     */
    private function redirect($url) {
        if (!headers_sent()) {
            header("Location: $url");
            exit;
        } else {
            // Fallback for testing environment
            echo "<script>window.location.href='$url';</script>";
            exit;
        }
    }
}
