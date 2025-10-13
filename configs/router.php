<?php
class Route {
    protected $controller = 'HomeController'; // Đặt controller mặc định
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();
       
        // Xử lý Controller với Clean URLs
        if (isset($url[0])) {
            // Clean URL routing - direct to CustomerController for main routes
            switch ($url[0]) {
                case 'products':
                    $this->controller = 'CustomerController';
                    $this->method = 'products';
                    unset($url[0]);
                    break;
                    
                case 'product':
                    $this->controller = 'CustomerController';
                    $this->method = 'productDetail';
                    unset($url[0]);
                    break;
                    
                case 'about':
                    $this->controller = 'CustomerController';
                    $this->method = 'about';
                    unset($url[0]);
                    break;
                    
                case 'search':
                    $this->controller = 'CustomerController';
                    $this->method = 'searchApi';
                    unset($url[0]);
                    break;
                    
                case 'api':
                    $this->controller = 'CustomerController';
                    unset($url[0]);
                    if (isset($url[1])) {
                        switch ($url[1]) {
                            case 'new-arrivals':
                                $this->method = 'getNewArrivals';
                                break;
                            case 'popular':
                            case 'popular-products':
                                $this->method = 'getPopularProducts';
                                break;
                            case 'category':
                                $this->method = 'getProductsByCategory';
                                break;
                            case 'categories':
                                $this->method = 'getCategories';
                                break;
                            case 'collections':
                                $this->method = 'getCollections';
                                break;
                        }
                        unset($url[1]);
                    }
                    break;
                    
                case 'customer':
                    // Legacy support for old URLs
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
                            case 'productDetail':
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
                                        case 'popular-products':
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
                    break;
                    
                case 'signin':
                case 'login':
                    $this->controller = 'AuthController';
                    $this->method = 'showSignIn';
                    unset($url[0]);
                    break;
                    
                case 'signup':
                case 'register':
                    $this->controller = 'AuthController';
                    $this->method = 'showSignUp';
                    unset($url[0]);
                    break;
                    
                case 'forgot-password':
                    $this->controller = 'AuthController';
                    $this->method = 'showForgotPassword';
                    unset($url[0]);
                    break;
                    
                case 'reset-password':
                    $this->controller = 'AuthController';
                    $this->method = 'showResetPassword';
                    unset($url[0]);
                    break;
                    
                case 'auth':
                    $this->controller = 'AuthController';
                    unset($url[0]);
                    if (isset($url[1])) {
                        switch ($url[1]) {
                            case 'signin':
                            case 'login':
                                $this->method = 'signIn';
                                break;
                            case 'signup':
                            case 'register':
                                $this->method = 'signUp';
                                break;
                            case 'verify':
                                $this->method = 'verifyEmail';
                                break;
                            case 'logout':
                                $this->method = 'logout';
                                break;
                            case 'resend-verification':
                                $this->method = 'resendVerification';
                                break;
                            case 'forgot-password':
                                $this->method = 'forgotPassword';
                                break;
                            case 'reset-password':
                                $this->method = 'resetPassword';
                                break;
                        }
                        unset($url[1]);
                    }
                    break;

                case 'profile':
                    $this->controller = 'ProfileController';
                    $this->method = 'index';
                    unset($url[0]);
                    if (isset($url[1])) {
                        switch ($url[1]) {
                            case 'update':
                                $this->method = 'update';
                                break;
                            case 'upload-avatar':
                                $this->method = 'uploadAvatar';
                                break;
                            case 'change-password':
                                $this->method = 'changePassword';
                                break;
                        }
                        unset($url[1]);
                    }
                    break;

                case 'cart':
                    $this->controller = 'CartController';
                    $this->method = 'index';
                    unset($url[0]);
                    if (isset($url[1])) {
                        switch ($url[1]) {
                            case 'add':
                                $this->method = 'add';
                                break;
                            case 'update':
                                $this->method = 'update';
                                break;
                            case 'remove':
                                $this->method = 'remove';
                                break;
                            case 'clear':
                                $this->method = 'clear';
                                break;
                            case 'count':
                                $this->method = 'count';
                                break;
                            case 'buynow':
                                $this->method = 'buyNow';
                                break;
                        }
                        unset($url[1]);
                    }
                    break;

                case 'checkout':
                    $this->controller = 'CheckoutController';
                    $this->method = 'index';
                    unset($url[0]);
                    if (isset($url[1])) {
                        switch ($url[1]) {
                            case 'process':
                                $this->method = 'process';
                                break;
                        }
                        unset($url[1]);
                    }
                    break;

                case 'order':
                    $this->controller = 'OrderController';
                    $this->method = 'index';
                    unset($url[0]);
                    if (isset($url[1])) {
                        switch ($url[1]) {
                            case 'buynow':
                                $this->method = 'buyNow';
                                break;
                        }
                        unset($url[1]);
                    }
                    break;

                case 'order-success':
                    $this->controller = 'CheckoutController';
                    $this->method = 'orderSuccess';
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
