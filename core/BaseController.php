<?php
class BaseController{
    public function model($model){
        require_once "app/models/{$model}.php";
        return new $model;
    }
    protected function renderView($view, $data = []) {
        extract($data);
        
        // Sử dụng đường dẫn tuyệt đối
        $viewPath = __DIR__ . "/../app/views/{$view}.php";
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // View không tồn tại - hiển thị thông báo debug
            die("View không tồn tại: {$viewPath}");
        }
    }
}