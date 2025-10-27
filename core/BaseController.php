<?php
class BaseController{
    public function model($model){
        require_once "app/models/{$model}.php";
        return new $model;
    }
    
    protected function view($view, $data = []) {
        extract($data);
        
        // Sử dụng đường dẫn tuyệt đối
        $viewPath = __DIR__ . "/../app/views/{$view}.php";
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View không tồn tại: {$viewPath}");
       }
    }
    
    protected function jsonResponse($success = true, $message = '', $data = null) {
        // Clear any previous output
        if (ob_get_length()) {
            ob_clean();
        }
        
        // Set headers if not already sent
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}