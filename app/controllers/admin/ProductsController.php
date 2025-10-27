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
    private $uploadPath = 'public/uploads/products/';
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $maxFileSize = 5242880; // 5MB

    public function __construct() {
        // Initialize Models - Dependency Injection pattern
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->collectionModel = $this->model('Collection');
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
        // Chỉ chấp nhận POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method!';
            $this->redirect('index.php?url=products');
            return;
        }

        try {
            // Validate input data
            $validationErrors = $this->validateProductData($_POST);
            
            if (!empty($validationErrors)) {
                $_SESSION['error'] = implode('<br>', $validationErrors);
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

            // Tạo sản phẩm trong database
            $productId = $this->productModel->create($data);
            
            if (!$productId) {
                throw new Exception('Không thể tạo sản phẩm trong database');
            }

            // Xử lý upload images
            if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
                $uploadResult = $this->handleProductImages($productId, $_FILES['product_images']);
                
                if (!$uploadResult['success']) {
                    // Nếu upload fail, log warning nhưng vẫn thành công
                    error_log('Warning: Product created but image upload failed: ' . implode(', ', $uploadResult['errors']));
                }
            }

            // Xử lý categories (many-to-many relationship)
            if (!empty($_POST['category_ids']) && is_array($_POST['category_ids'])) {
                $this->attachCategoriesToProduct($productId, $_POST['category_ids']);
            }

            $_SESSION['success'] = 'Tạo sản phẩm thành công!';
            $this->redirect('index.php?url=products');
            
        } catch (Exception $e) {
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

        // Validate stock (nếu có)
        if (isset($data['stock']) && (!is_numeric($data['stock']) || $data['stock'] < 0)) {
            $errors[] = 'Số lượng tồn kho phải là số không âm';
        }

        return $errors;
    }

    /**
     * Xử lý upload ảnh sản phẩm theo đúng database schema
     * Lưu vào tables: images + image_usages
     * @return array ['success' => bool, 'errors' => array]
     */
    private function handleProductImages($productId, $files) {
        $result = ['success' => true, 'errors' => []];
        
        try {
            // Tạo thư mục upload nếu chưa tồn tại
            $productUploadPath = $this->uploadPath . $productId . '/';
            if (!is_dir($productUploadPath)) {
                mkdir($productUploadPath, 0777, true);
            }

            $uploadedFiles = [];
            $fileCount = count($files['name']);

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
                        $uploadedFiles[] = [
                            'path' => $destination,
                            'is_primary' => ($i === 0) // First image is primary
                        ];
                    } else {
                        $result['errors'][] = "Không thể upload file {$fileName}";
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
                $mainImagePath = $uploadedFiles[0]['path'];
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
                $sql = "INSERT INTO product_categories (product_id, category_id, created_at) 
                        VALUES (:product_id, :category_id, NOW())";
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
        header("Location: $url");
        exit;
    }
}
