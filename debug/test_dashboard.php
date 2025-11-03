<?php
// Ensure SERVER vars for CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

require_once __DIR__ . '/../configs/config.php';
require_once __DIR__ . '/../configs/database.php';

// Core base model
require_once __DIR__ . '/../core/BaseModel.php';

// Require models used by AdminService
require_once __DIR__ . '/../app/models/Product.php';
require_once __DIR__ . '/../app/models/Category.php';
require_once __DIR__ . '/../app/models/Order.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Review.php';
require_once __DIR__ . '/../app/services/AdminService.php';

try {
    $service = new AdminService();
    $data = $service->getDashboardData();
    echo "Dashboard data retrieved:\n";
    echo "Stats: \n";
    print_r($data['stats']);
    echo "Recent Orders: \n";
    foreach ($data['recentOrders'] as $o) {
        echo "Order #{$o->order_id} - products: {$o->product_names}\n";
    }
    echo "Best Sellers: \n";
    print_r($data['bestSellers']);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
