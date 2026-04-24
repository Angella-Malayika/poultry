<?php
require_once 'auth_required.php';

// Load email configuration
$emailConfig = require_once 'email_config.php';

// Include database connection
include("connection.php");

// Get form data and sanitize
$name = htmlspecialchars(trim($_POST['name'] ?? ''));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(trim($_POST['phone'] ?? 'Not provided'));
if ($phone!== '' && !preg_match('/^\d{10}$/', $phone)){
    $_SESSION['error'] = "phone number must be exactly 10 digits and contain numbers onlys.";
    header("Location: contact.php");
    exit();
}
$subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));

// Validate required fields
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    $_SESSION['error'] = "Please fill in all required fields.";
    header("Location: contact.php");
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format.";
    header("Location: contact.php");
    exit();
}

// Validate Gmail address
if (!preg_match('/@gmail\.com$/', $email)) {
    $_SESSION['error'] = "Please use a Gmail address (@gmail.com).";
    header("Location: contact.php");
    exit();
}

// Build email content
$email_body = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 15px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .field { margin: 10px 0; }
        .field strong { display: inline-block; width: 100px; }
        .message-box { background: white; padding: 15px; margin-top: 15px; border-left: 4px solid #4CAF50; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>New Contact Form Message</h2>
        </div>
        <div class='content'>
            <div class='field'><strong>Name:</strong> $name</div>
            <div class='field'><strong>Email:</strong> <a href='mailto:$email'>$email</a></div>
            <div class='field'><strong>Phone:</strong> $phone</div>
            <div class='field'><strong>Subject:</strong> " . ucfirst($subject) . "</div>
            <div class='message-box'>
                <strong>Message:</strong><br><br>
                " . nl2br($message) . "
            </div>
        </div>
    </div>
</body>
</html>
";

$plain_text = "New Contact Form Message\n\n";
$plain_text .= "Name: $name\n";
$plain_text .= "Email: $email\n";
$plain_text .= "Phone: $phone\n";
$plain_text .= "Subject: " . ucfirst($subject) . "\n\n";
$plain_text .= "Message:\n$message\n";

$email_subject = "Contact Form: " . ucfirst($subject) . " - From: $name";

// Try to send email
try {
    // Save message to database
    $stmt = $conn->prepare("INSERT INTO messages (sender_name, email, phone, subject, message_text) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
    $stmt->execute();
    $stmt->close();

    $sent = sendHTMLEmail(
        $emailConfig['admin_email'],
        $emailConfig['admin_name'],
        $email_subject,
        $email_body,
        $plain_text,
        $email,
        $name
    );

    if ($sent) {
        $_SESSION['success'] = "Thank you for contacting us! Your message has been sent successfully. We'll get back to you soon.";
    } else {
        // Log the message to database or file as backup
        $log_entry = date('Y-m-d H:i:s') . " - Failed to send email from: $name ($email)\n";
        file_put_contents('contact_log.txt', $log_entry, FILE_APPEND);
        
        $_SESSION['error'] = "Sorry, there was an error sending your message. Please try contacting us directly at kalungufeeds167@gmail.com or call us.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "An error occurred. Please try again later or contact us directly.";
}

// Redirect back to contact page
header("Location: contact.php");
exit();
?>
