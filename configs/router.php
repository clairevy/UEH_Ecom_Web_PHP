<?php
class Route {
    protected $controller = 'HomeController'; // Đặt controller mặc định
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();
       
        // Xử lý Controller
        if (isset($url[0])) {
            // Special handling for customer routes
            if ($url[0] === 'customer') {
                $this->controller = 'CustomerController';
                unset($url[0]);
                
                // Check for specific customer actions
                if (isset($url[1])) {
                    switch ($url[1]) {
                        case 'products':
                            $this->method = 'products';
                            unset($url[1]);
                            break;
                        case 'product-detail':
                        case 'product':
                            $this->method = 'productDetail';
                            unset($url[1]);
                            break;
                        case 'search':
                            $this->method = 'searchApi';
                            unset($url[1]);
                            break;
                        case 'api':
                            unset($url[1]);
                            if (isset($url[2])) {
                                switch ($url[2]) {
                                    case 'new-arrivals':
                                        $this->method = 'getNewArrivals';
                                        break;
                                    case 'popular':
                                        $this->method = 'getPopularProducts';
                                        break;
                                    case 'category':
                                        $this->method = 'getProductsByCategory';
                                        break;
                                }
                                unset($url[2]);
                            }
                            break;
                    }
                }
            } else {
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
            // Default to customer home page instead of HomeController
            $this->controller = 'CustomerController';
            $this->method = 'index';
        }

        // Nạp controller
        $controllerFilePath = __DIR__ . '/../app/controllers/' . $this->controller . '.php';
        if (file_exists($controllerFilePath)) {
            require_once $controllerFilePath;
            $this->controller = new $this->controller;
        } else {
            // Xử lý lỗi nếu không tìm thấy file controller
            die("Controller '{$this->controller}' not found.");
        }

        // Xử lý Method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Xử lý Params
        $this->params = $url ? array_values($url) : [];

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
