<?php
class BaseController {
    /**
     * Load model
     */
    protected function model($model) {
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

    /**
     * Render admin page with layout
     */
    protected function renderAdminPage($contentView, $data = []) {
        // Load content
        ob_start();
        $this->view($contentView, $data);
        $content = ob_get_clean();
        
        // Add content to data
        $data['content'] = $content;
        
        // Render main layout
        $this->view('admin/layouts/main', $data);
    }
}