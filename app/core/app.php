<?php

Class App
 {
    protected $controller ="home";
    protected $method ="index";

    function __construct( )
    {
        $url = $this->parseURL();
        show($url);
    }
    
    private function parseURL()
    {
       $url = isset($_GET['url']) ? $_GET['url'] : 'home';
      return explode("/", filter_var(trim($url,"/"), FILTER_SANITIZE_URL));
    }
}
