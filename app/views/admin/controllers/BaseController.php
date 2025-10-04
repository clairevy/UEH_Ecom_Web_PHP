<?php
/**
 * Base Controller
 * Lớp cơ sở cho tất cả các controller
 */

class BaseController {
    protected $data = [];

    /**
     * Render view
     */
    protected function render($view, $data = []) {
        $this->data = array_merge($this->data, $data);
        extract($this->data);
        
        $viewPath = __DIR__ . "/../views/{$view}.php";
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View not found: {$view}";
        }
    }

    /**
     * Redirect
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    /**
     * JSON response
     */
    protected function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
