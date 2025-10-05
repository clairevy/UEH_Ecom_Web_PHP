<?php
/**
 * AdminRouter - Routing cho Admin Panel
 * Tương tự Route class nhưng dành cho admin
 */
class AdminRouter {
    protected $controller = 'DashboardController'; // Controller mặc định cho admin
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();
       
        // Xử lý Controller với Clean URLs cho admin
        if (isset($url[0])) {
            // Clean URL routing cho admin
            switch ($url[0]) {
                case 'dashboard':
                    $this->controller = 'DashboardController';
                    $this->method = 'index';
                    unset($url[0]);
                    break;
                    
                case 'products':
                    $this->controller = 'AdminProductController';
                    $this->method = 'index';
                    unset($url[0]);
                    break;
                    
                case 'categories':
                    $this->controller = 'AdminCategoryController';
                    $this->method = 'index';
                    unset($url[0]);
                    break;
                    
                case 'collections':
                    $this->controller = 'AdminCollectionController';
                    $this->method = 'index';
                    unset($url[0]);
                    break;
                    
                case 'orders':
                    $this->controller = 'AdminOrderController';
                    $this->method = 'index';
                    unset($url[0]);
                    break;
                    
                case 'customers':
                    $this->controller = 'AdminCustomerController';
                    $this->method = 'index';
                    unset($url[0]);
                    break;
                    
                case 'reviews':
                    $this->controller = 'AdminReviewController';
                    $this->method = 'index';
                    unset($url[0]);
                    break;
                    
                case 'media':
                    $this->controller = 'MediaController';
                    $this->method = 'index';
                    unset($url[0]);
                    break;
                    
                default:
                    // Chuyển đổi tên từ URL (vd: 'users') thành tên Class (vd: 'UsersController')
                    $controllerName = ucfirst($url[0]) . 'Controller';
                    // Tạo đường dẫn tuyệt đối đến file controller
                    $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

                    if (file_exists($controllerFile)) {
                        $this->controller = $controllerName;
                        unset($url[0]);
                    }
            }
        } else {
            // Default to dashboard
            $this->controller = 'DashboardController';
            $this->method = 'index';
        }

        // Nạp controller
        $controllerFilePath = __DIR__ . '/../app/controllers/' . $this->controller . '.php';
        if (file_exists($controllerFilePath)) {
            require_once $controllerFilePath;
            $this->controller = new $this->controller;
        } else {
            // Xử lý lỗi nếu không tìm thấy file controller
            die("Admin Controller '{$this->controller}' not found.");
        }

        // Xử lý Method từ URL hoặc action parameter
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        } elseif (isset($_GET['action'])) {
            $action = $_GET['action'];
            if (method_exists($this->controller, $action)) {
                $this->method = $action;
            }
        }

        // Xử lý Params từ URL hoặc id parameter
        $this->params = $url ? array_values($url) : [];
        if (isset($_GET['id']) && empty($this->params)) {
            $this->params = [$_GET['id']];
        }

        // Gọi controller, method với các tham số
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function getUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
