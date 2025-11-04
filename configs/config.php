<?php

const _HOST = 'localhost';
const _DB = 'vyueh_db_ecom';

const _USER = 'vyueh_vyueh';      // <--- THAY_BẰNG_TÊN_USER_DB_TRÊN_HOST
const _PASSWORD = '1234@qwE'; // <--- THAY_BẰNG MẬT KHẨU USER DB
const _DRIVER = 'mysql';

// Base URL configuration
// Nếu site chạy trực tiếp tại domain gốc (https://trangsucse0001ueh.id.vn)
define('BASE_URL', ''); 
define('FULL_BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . BASE_URL);

// Define root paths — đảm bảo đường dẫn tồn tại
define('ROOT', __DIR__ . '/../app/views/admin/');
define('ADMIN_PATH', __DIR__ . '/../app/views/admin/');
define('PUBLIC_PATH', __DIR__ . '/../public/');

// Include URL helper functions
require_once __DIR__ . '/../helpers/url_helper.php';

// // Base URL configuration
// // For localhost/Ecom_website
// define('BASE_URL', '/Ecom_website');
// define('FULL_BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . BASE_URL);

// // Define root paths
// define('ROOT', __DIR__ . '/../app/views/admin/');
// define('ADMIN_PATH', __DIR__ . '/../app/views/admin/');
// define('PUBLIC_PATH', __DIR__ . '/../public/');

// // Email verification settings (moved to email.php)

// // Include URL helper functions
// require_once __DIR__ . '/../helpers/url_helper.php';