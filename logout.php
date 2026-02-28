<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out | Kalungu Quality Feeds</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #2e7d32;
            --text-color: #333;
            --light-bg: #f9fbe7;
            --white: #f1f8e9;
            --input-border: #2e7d32;
            --black: #333;
        }

        body {
            background: var(--light-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .logout-container {
            background: white;
            padding: 60px 40px;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(46, 125, 50, 0.3);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .logout-icon {
            font-size: 80px;
            color: var(--primary-color);
            margin-bottom: 20px;
            animation: fadeIn 0.6s ease-in;
        }

        .logout-container h1 {
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 2rem;
        }

        .logout-container > p {
            color: var(--text-color);
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .logout-message {
            background-color: var(--white);
            border-left: 4px solid var(--primary-color);
            padding: 15px 20px;
            border-radius: 6px;
            margin: 20px 0;
            color: var(--text-color);
        }

        .logout-message p {
            margin-bottom: 0;
            font-weight: 500;
        }

        .btn-group-logout {
            display: flex;
            gap: 15px;
            margin: 30px 0;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-home, .btn-login {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-home {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-home:hover {
            background-color: #1b5e20;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(46, 125, 50, 0.3);
        }

        .btn-login {
            background-color: var(--white);
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-login:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(46, 125, 50, 0.3);
        }

        .logout-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: var(--text-color);
        }

        .logout-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .logout-footer a:hover {
            color: #1b5e20;
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
    
</head>
<body>
    <div class="logout-container ">
        <div class="logout-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1>You've Been Logged Out</h1>
        <p>Thank you for using Kalungu Quality Feeds</p>
        
        <div class="logout-message">
            <p><i class="fas fa-info-circle me-2"></i>Your session has been securely terminated. Your account is safe.</p>
        </div>

        <p>We hope to see you soon! Feel free to browse our products or sign back in.</p>

        <div class="btn-group-logout">
            <a href="index.php" class="btn-home">
                <i class="fas fa-home"></i> Back to Home
            </a>
            <a href="login.php" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Sign In Again
            </a>
        </div>

        <div class="logout-footer">
            <small>Need help? <a href="contact.php">Contact our support team</a></small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
