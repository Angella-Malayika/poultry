<?php
include './dbcon/connection.php';
session_start();

$message = ''; // Initialize message variable

// Fixed missing opening bracket { and added security (prepared statements)
if (isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $message = '<div class="alert alert-danger">Passwords do not match!</div>';
    } else {
        // Use prepared statements to check if email OR username already exists
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $checkStmt->bind_param("ss", $email, $username);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            $message = '<div class="alert alert-danger">Email or Username already exists!</div>';
        } else {
            // Hash the password securely
            $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user using prepared statements
            $insertStmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $insertStmt->bind_param("sss", $username, $email, $hashedpassword);
            
            if ($insertStmt->execute()) {
                // Registration successful
                $message = '<div class="alert alert-success">Account created successfully! <a href="login.php">Login here</a></div>';
            } else {
                $message = '<div class="alert alert-danger">Error: Something went wrong.</div>';
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        :root {
            --primary-color: #2e7d32;
            --text-color: #333;
            --light-bg: #f9fbe7;
            --white: #f1f8e9;
            --input-border: #2e7d32;
        }

        body {
            background: var(--light-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(46, 125, 50, 0.3);
            max-width: 500px;
            width: 100%;
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 20px;
        }
        .register-header i {
            font-size: 50px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        .register-header h2 {
            color: var(--primary-color);
            font-weight: bold;
        }
        .form-label {
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .form-control {
            background-color: var(--white);
            border: 2px solid var(--input-border);
            color: var(--text-color);
            padding: 0.75rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 10px rgba(46, 125, 50, 0.2);
            background-color: white;
        }
        .btn-register {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1b5e20 100%);
            border: none;
            width: 100%;
            padding: 12px;
            font-weight: bold;
            color: white;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(46, 125, 50, 0.3);
            color: white;
        }
        .text-center { margin-top: 20px; }
        .text-center a { color: var(--primary-color); text-decoration: none; font-weight: bold; }
        .text-center a:hover { color: #1b5e20; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <i class="fas fa-user-plus"></i>
            <h2>Create Account</h2>
            <p class="text-muted">Sign up to get started</p>
        </div>
        
        <?php if (!empty($message)) echo $message; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-user"></i> Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-lock"></i> Password</label>
                <input type="password" class="form-control" name="password" required minlength="6">
            </div>
            
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-lock"></i> Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" required minlength="6">
            </div>
            
            <button type="submit" name="signup" class="btn btn-primary btn-register">
                <i class="fas fa-user-plus"></i> Sign Up
            </button>
        </form>
        
        <div class="text-center mt-3">
            <p>Already have an account? <a href="login.php">Login here</a></p>
            <a href="index.php" class="text-muted"><i class="fas fa-home"></i> Back to Home</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>