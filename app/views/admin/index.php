<?php
/**
 * Admin Panel - Trang chủ
 * Điểm vào chính của admin panel
 * Điều hướng đến các trang trong folder pages
 */

// Thiết lập
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Lấy trang từ URL
$page = $_GET['page'] ?? 'dashboard';

// Routing - điều hướng đến các trang trong folder pages
switch ($page) {
    // Trang chính - đã chuyển sang PHP
    case 'products':
        header('Location: pages/products.php');
        exit;
        break;
        
    case 'orders':
        header('Location: pages/orders.php');
        exit;
        break;
        
    case 'categories':
        header('Location: pages/categories.php');
        exit;
        break;
        
    case 'customers':
        header('Location: pages/customers.php');
        exit;
        break;
        
    // Trang PHP - đã chuyển sang PHP
    case 'collections':
        header('Location: pages/collections.php');
        exit;
        break;
        
    case 'reviews':
        header('Location: pages/reviews.php');
        exit;
        break;
        
    case 'media':
        header('Location: pages/media.html');
        exit;
        break;
        
    case 'discounts':
        header('Location: pages/discounts.html');
        exit;
        break;
        
    case 'payments':
        header('Location: pages/payments.html');
        exit;
        break;
        
    case 'customer-roles':
        header('Location: pages/customer-roles.html');
        exit;
        break;
        
    // Trang thêm mới
    case 'add-product':
        header('Location: pages/add-product.html');
        exit;
        break;
        
    case 'add-category':
        header('Location: pages/add-category.html');
        exit;
        break;
        
    case 'add-customer':
        header('Location: pages/add-customer.html');
        exit;
        break;
        
    case 'add-collection':
        header('Location: pages/add-collection.html');
        exit;
        break;
        
    case 'add-discount':
        header('Location: pages/add-discount.html');
        exit;
        break;
        
    // Trang chi tiết
    case 'product-details':
        $id = $_GET['id'] ?? '';
        header("Location: pages/product-details.html?id={$id}");
        exit;
        break;
        
    case 'order-details':
        $id = $_GET['id'] ?? '';
        header("Location: pages/order-details.html?id={$id}");
        exit;
        break;
        
    case 'customer-details':
        $id = $_GET['id'] ?? '';
        header("Location: pages/customer-details.html?id={$id}");
        exit;
        break;
        
    // Dashboard mặc định
    case 'dashboard':
    default:
        // Hiển thị dashboard trực tiếp
        include __DIR__ . '/pages/dashboard.php';
        break;
}
?>