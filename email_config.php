<?php
// Email Configuration
// Update these settings with your email credentials

return [
    // SMTP Settings
    'smtp_host' => 'smtp.gmail.com',  // Gmail SMTP server
    'smtp_port' => 587,                // Port for TLS
    'smtp_username' => 'kalungufeeds167@gmail.com', // Your Gmail address
    'smtp_password' => 'your-app-password-here',    // Gmail App Password (not regular password)
    'smtp_secure' => 'tls',            // TLS encryption
    
    // Email addresses
    'from_email' => 'kalungufeeds167@gmail.com',
    'from_name' => 'Kalungu Feeds Contact Form',
    'to_email' => 'kalungufeeds167@gmail.com',
    'to_name' => 'Kalungu Feeds',
    
    // Other settings
    'charset' => 'UTF-8'
];
?>
