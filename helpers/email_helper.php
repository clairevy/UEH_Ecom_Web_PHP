<?php
require_once __DIR__ . '/../configs/email.php';

// Check if Composer autoload exists (OOP Best Practice: Graceful Degradation)
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
$phpmailerAvailable = false;

if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
    
    // Check if PHPMailer class exists after autoload
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        $phpmailerAvailable = true;
    }
}

// Define constant for PHPMailer availability
define('PHPMAILER_AVAILABLE', $phpmailerAvailable);

// Log warning if PHPMailer not available
if (!PHPMAILER_AVAILABLE) {
    error_log('WARNING: PHPMailer not installed. Email functionality will use fallback PHP mail(). Run: composer install');
}

class EmailHelper {
    
    /**
     * Gửi email sử dụng PHPMailer với SMTP
     */
    public static function sendEmail($to, $subject, $message) {
        // Log email attempt
        $logsDir = __DIR__ . '/../logs';
        if (!is_dir($logsDir)) {
            @mkdir($logsDir, 0755, true);
        }
        
        $emailLog = $logsDir . '/emails.log';
        $logEntry = date('Y-m-d H:i:s') . " - SENDING TO: $to - SUBJECT: $subject\n";
        @file_put_contents($emailLog, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Use PHPMailer
        $result = self::sendWithPHPMailer($to, $subject, $message);
        
        // Log result
        $resultLog = $result ? "SUCCESS" : "FAILED";
        $logEntry = date('Y-m-d H:i:s') . " - RESULT: $resultLog for $to\n\n";
        @file_put_contents($emailLog, $logEntry, FILE_APPEND | LOCK_EX);
        
        return $result;
    }
    
    /**
     * Send email using PHPMailer (OOP Best Practice)
     * With fallback to PHP mail() function
     */
    private static function sendWithPHPMailer($to, $subject, $message) {
        // If PHPMailer is available, use it
        if (PHPMAILER_AVAILABLE && class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            try {
                // Use fully qualified class name (no use statement needed)
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                
                // Server settings
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USERNAME;
                $mail->Password   = SMTP_PASSWORD;
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = SMTP_PORT;
                $mail->CharSet    = 'UTF-8';
                
                // Recipients
                $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
                $mail->addAddress($to);
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;
                
                $mail->send();
                return true;
                
            } catch (\Exception $e) {
                error_log("PHPMailer Error: " . (isset($mail) ? $mail->ErrorInfo : $e->getMessage()));
                // Fallback to PHP mail on error
                return self::sendWithPHPMail($to, $subject, $message);
            }
        } else {
            // Fallback to PHP mail() function
            return self::sendWithPHPMail($to, $subject, $message);
        }
    }
    
    /**
     * Fallback: Send email using PHP mail() function
     * Used when PHPMailer is not available
     * 
     * NOTE: Để sử dụng được, cần cấu hình SMTP trong php.ini:
     * - smtp_port = 587
     * - SMTP = smtp.gmail.com (hoặc mail server khác)
     * - sendmail_from = your-email@gmail.com
     */
    private static function sendWithPHPMail($to, $subject, $message) {
        try {
            // Set headers for HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM . ">" . "\r\n";
            $headers .= "Reply-To: " . SMTP_FROM . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            // Send email
            $result = @mail($to, $subject, $message, $headers);
            
            if (!$result) {
                error_log("PHP mail() function failed for: $to - Make sure SMTP is configured in php.ini");
            } else {
                error_log("Email sent successfully to: $to using PHP mail() fallback");
            }
            
            return $result;
            
        } catch (\Exception $e) {
            error_log("PHP mail() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Gửi email xác thực đăng ký với link verification
     */
    public static function sendVerificationEmail($email, $token) {
        $subject = "Xác thực tài khoản của bạn";
        
        // URL verification link
        $verificationLink = "http://localhost/Ecom_website/auth/verify?token=" . $token;
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
                .content { background: white; padding: 30px; border-radius: 10px; }
                .button { display: inline-block; padding: 15px 30px; background: #d4af37; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { font-size: 12px; color: #666; margin-top: 30px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='content'>
                    <h2 style='color: #d4af37;'>Chào mừng bạn đến với Jewelry Store!</h2>
                    <p>Cảm ơn bạn đã đăng ký tài khoản. Vui lòng nhấn vào nút bên dưới để xác thực email:</p>
                    <a href='$verificationLink' class='button'>Xác thực tài khoản</a>
                    <p>Hoặc copy link sau vào trình duyệt:</p>
                    <p style='word-break: break-all; background: #f5f5f5; padding: 10px;'>$verificationLink</p>
                    <p><strong>Link này sẽ hết hạn sau 24 giờ.</strong></p>
                </div>
                <div class='footer'>
                    <p>Nếu bạn không đăng ký tài khoản này, vui lòng bỏ qua email này.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return self::sendEmail($email, $subject, $message);
    }
    

    
    /**
     * Tạo mã xác thực 6 số
     */
    public static function generateVerificationCode() {
        return sprintf("%06d", mt_rand(0, 999999));
    }
    
    /**
     * Send reset password email
     */
    public static function sendResetPasswordEmail($email, $token) {
        $subject = "Đặt lại mật khẩu - " . SMTP_FROM_NAME;
        
        // Reset password link
        $resetLink = "http://localhost/Ecom_website/reset-password?token=" . $token;
        
        $message = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Đặt Lại Mật Khẩu</h1>
                </div>
                <div class='content'>
                    <p>Xin chào,</p>
                    <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại <strong>" . SMTP_FROM_NAME . "</strong>.</p>
                    <p>Nếu bạn đã yêu cầu đặt lại mật khẩu, vui lòng nhấp vào nút bên dưới để tiến hành:</p>
                    
                    <div style='text-align: center;'>
                        <a href='$resetLink' class='button'>Đặt Lại Mật Khẩu</a>
                    </div>
                    
                    <p><strong>Lưu ý:</strong></p>
                    <ul>
                        <li>Link này sẽ hết hạn sau " . RESET_EXPIRE_MINUTES . " phút</li>
                        <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này</li>
                        <li>Vì lý do bảo mật, không chia sẻ link này với bất kỳ ai</li>
                    </ul>
                    
                    <p>Nếu nút không hoạt động, bạn có thể sao chép và dán link sau vào trình duyệt:</p>
                    <p style='background: #e9e9e9; padding: 10px; word-break: break-all; font-size: 12px;'>$resetLink</p>
                </div>
                <div class='footer'>
                    <p>Email này được gửi tự động, vui lòng không phản hồi.<br>
                    © " . date('Y') . " " . SMTP_FROM_NAME . ". All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return self::sendEmail($email, $subject, $message);
    }
    
    /**
     * Tạo token reset password
     */
    public static function generateResetToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Gửi email xác nhận thanh toán
     * @param object $order Thông tin đơn hàng
     * @return bool
     */
    public static function sendPaymentConfirmationEmail($order) {
        $customerEmail = $order->customer_email ?? $order->email;
        $customerName = $order->customer_name ?? $order->full_name ?? 'Khách hàng';
        $orderId = $order->order_id;
        $totalAmount = number_format($order->total_amount, 0, ',', '.');
        
        $subject = "Xác nhận thanh toán đơn hàng #$orderId - " . SMTP_FROM_NAME;
        
        $message = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0;
                    padding: 0;
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    padding: 20px; 
                    background: #f5f5f5;
                }
                .header { 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white; 
                    padding: 40px 30px; 
                    text-align: center; 
                    border-radius: 10px 10px 0 0;
                }
                .header h1 {
                    margin: 0;
                    font-size: 28px;
                }
                .content { 
                    background: white; 
                    padding: 40px 30px; 
                }
                .success-icon {
                    text-align: center;
                    font-size: 60px;
                    color: #28a745;
                    margin: 20px 0;
                }
                .order-info {
                    background: #f8f9fa;
                    border-left: 4px solid #28a745;
                    padding: 20px;
                    margin: 20px 0;
                }
                .order-info-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 10px 0;
                    border-bottom: 1px solid #e9ecef;
                }
                .order-info-row:last-child {
                    border-bottom: none;
                    font-weight: bold;
                    font-size: 18px;
                    color: #28a745;
                }
                .button { 
                    display: inline-block; 
                    background: #667eea;
                    color: white; 
                    padding: 15px 40px; 
                    text-decoration: none; 
                    border-radius: 5px; 
                    font-weight: bold; 
                    margin: 20px 0;
                    text-align: center;
                }
                .footer { 
                    text-align: center; 
                    margin-top: 30px; 
                    padding: 20px;
                    font-size: 12px; 
                    color: #666;
                    background: #f8f9fa;
                    border-radius: 0 0 10px 10px;
                }
                .highlight {
                    background: #fff3cd;
                    padding: 15px;
                    border-radius: 5px;
                    margin: 15px 0;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>💎 " . SMTP_FROM_NAME . "</h1>
                </div>
                <div class='content'>
                    <div class='success-icon'>✅</div>
                    
                    <h2 style='text-align: center; color: #28a745;'>Thanh Toán Thành Công!</h2>
                    
                    <p>Xin chào <strong>$customerName</strong>,</p>
                    
                    <p>Chúng tôi xác nhận đã nhận được thanh toán cho đơn hàng của bạn. Cảm ơn bạn đã mua sắm tại <strong>" . SMTP_FROM_NAME . "</strong>!</p>
                    
                    <div class='order-info'>
                        <h3 style='margin-top: 0; color: #667eea;'>📦 Thông Tin Đơn Hàng</h3>
                        <div class='order-info-row'>
                            <span>Mã đơn hàng:</span>
                            <span><strong>#$orderId</strong></span>
                        </div>
                        <div class='order-info-row'>
                            <span>Ngày đặt hàng:</span>
                            <span>" . date('d/m/Y H:i', strtotime($order->created_at)) . "</span>
                        </div>
                        <div class='order-info-row'>
                            <span>Trạng thái thanh toán:</span>
                            <span style='color: #28a745;'><strong>✓ Đã thanh toán</strong></span>
                        </div>
                        <div class='order-info-row'>
                            <span>Tổng tiền:</span>
                            <span><strong>$totalAmount VND</strong></span>
                        </div>
                    </div>
                    
                    <div class='highlight'>
                        <p style='margin: 0;'><strong>🚚 Tiếp theo:</strong></p>
                        <p style='margin: 5px 0 0 0;'>Đơn hàng của bạn sẽ được xử lý và giao trong thời gian sớm nhất. Chúng tôi sẽ gửi email thông báo khi đơn hàng được giao.</p>
                    </div>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='http://localhost/Ecom_website/customer/orders' class='button'>Xem Chi Tiết Đơn Hàng</a>
                    </div>
                    
                    <p style='margin-top: 30px;'><strong>Lưu ý quan trọng:</strong></p>
                    <ul style='color: #666;'>
                        <li>Vui lòng giữ email này để theo dõi đơn hàng</li>
                        <li>Kiểm tra thông tin giao hàng và liên hệ ngay nếu có sai sót</li>
                        <li>Thời gian giao hàng dự kiến: 2-5 ngày làm việc</li>
                    </ul>
                    
                    <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi:</p>
                    <p>📧 Email: " . SMTP_FROM . "<br>
                    📞 Hotline: 1900-xxxx</p>
                    
                    <p style='margin-top: 30px;'>Trân trọng,<br>
                    <strong>" . SMTP_FROM_NAME . " Team</strong></p>
                </div>
                <div class='footer'>
                    <p>Email này được gửi tự động, vui lòng không phản hồi.<br>
                    © " . date('Y') . " " . SMTP_FROM_NAME . ". All rights reserved.</p>
                    <p style='margin-top: 10px;'>
                        <a href='#' style='color: #667eea; text-decoration: none; margin: 0 10px;'>Chính sách</a> | 
                        <a href='#' style='color: #667eea; text-decoration: none; margin: 0 10px;'>Hỗ trợ</a> | 
                        <a href='#' style='color: #667eea; text-decoration: none; margin: 0 10px;'>Liên hệ</a>
                    </p>
                </div>
            </div>
        </body>
        </html>";
        
        return self::sendEmail($customerEmail, $subject, $message);
    }
}
?>