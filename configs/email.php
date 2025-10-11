<?php
// Cấu hình email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'nhithieu03@gmail.com');
define('SMTP_PASSWORD', 'jfho pjzr euhd lsae'); // App Password của Gmail
define('SMTP_FROM', 'nhithieu03@gmail.com');
define('SMTP_FROM_NAME', 'Jewelry Store');

// Thời gian hết hạn
define('VERIFICATION_EXPIRE_MINUTES', 1440); // 24 giờ
define('RESET_EXPIRE_MINUTES', 30); // 30 phút cho reset password
?>