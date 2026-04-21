<?php
// Email Configuration for Gmail SMTP
// To use: Generate a Gmail App Password at https://myaccount.google.com/apppasswords

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$emailConfig = [
    // SMTP Settings
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'nakanwagiangella61@gmail.com',
    'smtp_password' => 'csax zmlx kecu bmsu', // Replace with Gmail App Password
    'smtp_secure' => 'tls',
    
    // Email addresses
    'from_email' => 'nakanwagiangella61@gmail.com',
    'from_name' => 'Kalungu Quality Feeds',
    'admin_email' => 'nakanwagiangella61@gmail.com',
    'admin_name' => 'Kalungu Quality Feeds',
    
    // Settings
    'charset' => 'UTF-8',
    'use_mail_function' => false, // Use SMTP via PHPMailer when available
    'smtp_debug' => 0
];

/**
 * Send HTML email using PHP mail() function
 * @param string $to_email Recipient email address
 * @param string $to_name Recipient name
 * @param string $subject Email subject
 * @param string $html_body HTML email body
 * @param string $plain_text Plain text alternative (optional)
 * @return bool Success or failure
 */
function sendHTMLEmail($to_email, $to_name, $subject, $html_body, $plain_text = '', $reply_to_email = '', $reply_to_name = '') {
    global $emailConfig;
    
    // Validate email
    if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    if (!empty($emailConfig['use_mail_function'])) {
        // Email headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=" . $emailConfig['charset'] . "\r\n";
        $headers .= "From: " . $emailConfig['from_name'] . " <" . $emailConfig['from_email'] . ">\r\n";
        if ($reply_to_email !== '') {
            $reply_name = $reply_to_name !== '' ? $reply_to_name : $reply_to_email;
            $headers .= "Reply-To: " . $reply_name . " <" . $reply_to_email . ">\r\n";
        } else {
            $headers .= "Reply-To: " . $emailConfig['from_email'] . "\r\n";
        }
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "X-Priority: 3\r\n";

        // Send email using PHP mail() function
        return mail($to_email, $subject, $html_body, $headers);
    }

    return sendSMTPEmail($to_email, $to_name, $subject, $html_body, $plain_text, $reply_to_email, $reply_to_name);
}

/**
 * Send HTML email using SMTP (PHPMailer)
 */
function sendSMTPEmail($to_email, $to_name, $subject, $html_body, $plain_text = '', $reply_to_email = '', $reply_to_name = '') {
    global $emailConfig;

    $autoload_path = __DIR__ . '/vendor/autoload.php';
    if (!file_exists($autoload_path)) {
        error_log('PHPMailer not installed. Run: composer require phpmailer/phpmailer');
        return false;
    }

    require_once $autoload_path;
    if (!class_exists(PHPMailer::class)) {
        error_log('PHPMailer class not found after autoload.');
        return false;
    }

    $smtp_password = $emailConfig['smtp_password'] ?? '';
    $smtp_password = str_replace(' ', '', $smtp_password);

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $emailConfig['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $emailConfig['smtp_username'];
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = ($emailConfig['smtp_secure'] === 'ssl')
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = (int) $emailConfig['smtp_port'];
        $mail->CharSet = $emailConfig['charset'];

        if (!empty($emailConfig['smtp_debug'])) {
            $mail->SMTPDebug = (int) $emailConfig['smtp_debug'];
            $mail->Debugoutput = 'error_log';
        }

        $mail->setFrom($emailConfig['from_email'], $emailConfig['from_name']);
        $mail->addAddress($to_email, $to_name);
        if ($reply_to_email !== '') {
            $reply_name = $reply_to_name !== '' ? $reply_to_name : $reply_to_email;
            $mail->addReplyTo($reply_to_email, $reply_name);
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html_body;
        $mail->AltBody = $plain_text !== '' ? $plain_text : strip_tags($html_body);

        return $mail->send();
    } catch (Exception $e) {
        error_log('SMTP send failed: ' . $e->getMessage());
        return false;
    }
    
    // Email headers
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=" . $emailConfig['charset'] . "\r\n";
    $headers .= "From: " . $emailConfig['from_name'] . " <" . $emailConfig['from_email'] . ">\r\n";
    $headers .= "Reply-To: " . $emailConfig['from_email'] . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "X-Priority: 3\r\n";
    
    // Send email using PHP mail() function
    $result = mail($to_email, $subject, $html_body, $headers);
    
    return $result;
}

/**
 * Send order confirmation email
 */
function sendOrderConfirmationEmail($order_details) {
    $to_email = $order_details['email'];
    $to_name = $order_details['full_name'];
    $order_id = $order_details['id'];
    $product = $order_details['product'];
    $quantity = $order_details['quantity'];
    $delivery_date = date('F d, Y', strtotime($order_details['delivery_date']));
    
    $html_body = "
    <html>
    <head>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2e7d32; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 0 0 5px 5px; }
            .order-info { background: white; padding: 15px; margin: 20px 0; border-left: 4px solid #2e7d32; }
            .info-row { margin: 10px 0; }
            .label { font-weight: bold; color: #2e7d32; display: inline-block; width: 150px; }
            .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Order Confirmation</h1>
                <p>Thank you for your order!</p>
            </div>
            <div class='content'>
                <p>Dear " . htmlspecialchars($to_name) . ",</p>
                
                <p>Your order has been successfully placed. Here are your order details:</p>
                
                <div class='order-info'>
                    <div class='info-row'>
                        <span class='label'>Order ID:</span>
                        <strong>#" . htmlspecialchars($order_id) . "</strong>
                    </div>
                    <div class='info-row'>
                        <span class='label'>Products:</span>
                        " . htmlspecialchars($product) . "
                    </div>
                    <div class='info-row'>
                        <span class='label'>Total Quantity:</span>
                        " . htmlspecialchars($quantity) . "
                    </div>
                    <div class='info-row'>
                        <span class='label'>Delivery Date:</span>
                        " . $delivery_date . "
                    </div>
                </div>
                
                <p>You will receive a call confirmation from our team shortly. If you have any questions, please don't hesitate to contact us.</p>
                
                <p><strong>Contact Information:</strong><br>
                Phone: +256 758 555 562 | +256 758 707 297<br>
                Email: kalungufeeds167@gmail.com<br>
                WhatsApp: <a href='https://wa.me/256758555562'>Click to Chat</a>
                </p>
                
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email. For inquiries, contact us using the information above.</p>
                    <p>&copy; 2026 Kalungu Quality Feeds. All rights reserved.</p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendHTMLEmail($to_email, $to_name, 'Order Confirmation - Order #' . $order_id, $html_body);
}

/**
 * Send welcome email to new users
 */
function sendWelcomeEmail($user_email, $username) {
    global $emailConfig;
    
    $html_body = "
    <html>
    <head>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2e7d32; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 0 0 5px 5px; }
            .button { display: inline-block; background: #2e7d32; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Welcome to Kalungu Quality Feeds</h1>
            </div>
            <div class='content'>
                <p>Dear " . htmlspecialchars($username) . ",</p>
                
                <p>Welcome to Kalungu Quality Feeds! We're excited to have you join our community.</p>
                
                <p>Your account has been successfully created. You can now:</p>
                <ul>
                    <li>Place orders for our premium feed products</li>
                    <li>Track your order status</li>
                    <li>View your order history</li>
                    <li>Contact us for any inquiries</li>
                </ul>
                
                <p><strong>Our Premium Products Include:</strong></p>
                <ul>
                    <li>Soya & Layer Mash</li>
                    <li>Broiler Feed & Grower Mash</li>
                    <li>One-Day-Old Chicks</li>
                    <li>Feed Additives & Concentrates</li>
                    <li>Poultry Equipment (Drinkers, Brooder Heaters)</li>
                </ul>
                
                <p><strong>Quick Links:</strong><br>
                <a href='https://kalungufeeds.com' class='button'>Visit Our Website</a>
                </p>
                
                <p>If you have any questions or need assistance, feel free to reach out:<br>
                Phone: +256 758 555 562 | +256 758 707 297<br>
                Email: kalungufeeds167@gmail.com<br>
                WhatsApp: <a href='https://wa.me/256758555562'>Click to Chat</a>
                </p>
                
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>&copy; 2026 Kalungu Quality Feeds. All rights reserved.</p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendHTMLEmail($user_email, $username, 'Welcome to Kalungu Quality Feeds', $html_body);
}

return $emailConfig;
?>
