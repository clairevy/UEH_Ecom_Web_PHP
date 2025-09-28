<?php
/**
 * URL Helper Functions
 * Đảm bảo tất cả URLs đều có prefix đúng cho XAMPP
 */

/**
 * Generate base URL for the application
 */
function getBaseUrl() {
    // For XAMPP, always include project folder name
    return '/Ecom_website';
}

/**
 * Generate full URL from relative path
 */
function url($path = '') {
    $base = getBaseUrl();
    $path = ltrim($path, '/');
    return $base . ($path ? '/' . $path : '');
}

/**
 * Generate asset URL (CSS, JS, Images)
 */
function asset($path) {
    return url('public/assets/' . ltrim($path, '/'));
}

/**
 * Generate route URL with clean paths
 */
function route($route, $params = []) {
    // Convert old customer routes to clean URLs
    $cleanRoutes = [
        'customer/products' => 'products',
        'customer/productDetail' => 'product',
        'customer/product' => 'product',
        'customer/search' => 'search',
        'customer/api' => 'api',
        'customer' => '', // Home page
    ];
    
    // Check if route needs conversion
    foreach ($cleanRoutes as $old => $new) {
        if (strpos($route, $old) === 0) {
            $route = str_replace($old, $new, $route);
            break;
        }
    }
    
    $url = url($route);
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    return $url;
}
?>