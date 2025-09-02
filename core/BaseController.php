<?php
class BaseController{
    public function model($model){
        require_once "app/models/{$model}.php";
        return new $model;
    }
    protected function renderView($view, $data = []) {
        extract($data);
        if (file_exists("../app/views/{$view}.php")) {
            require_once "../app/views/{$view}.php";
        } else {
            // View không tồn tại
            die('View does not exist');
        }
    }
}