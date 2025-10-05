<?php
class BaseController{
    public function model($model){
        require_once __DIR__ . "/../app/models/{$model}.php";
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
}