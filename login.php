<?php
include './dbcon/connection.php';
session_start();

$message = ''; // Initialize message variable to prevent undefined warnings

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = '<div class="alert alert-danger">Please fill in all fields.</div>';
    } else {
        // Prepare the SQL statement to find the user
        $stmt = $conn->prepare("SELECT email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Verify the password against the hashed version in DB
            if (password_verify($password, $row['password'])) {
                // Success! Set session and redirect
                $_SESSION['email'] = $row['email'];
                header("Location: index.php");
                exit();
            } else {
                $message = '<div class="alert alert-danger">Invalid email or password.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Invalid email or password.</div>';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Kalungu Quality Feeds</title>
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
            background: linear-gradient(135deg, var(--light-bg) 0%, var(--light-bg) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(46, 125, 50, 0.3);
            max-width: 450px;
            width: 100%;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 20px;
        }

        .login-header h2 {
            color: var(--primary-color);
            font-weight: bold;
            margin: 0;
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

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1b5e20 100%);
            border: none;
            width: 100%;
            padding: 12px;
            font-weight: bold;
            color: white;
            border-radius: 6px;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(46, 125, 50, 0.3);
            color: white;
        }

        .text-center { margin-top: 20px; }
        .text-center a { color: var(--primary-color); text-decoration: none; font-weight: bold; }
        .text-center a:hover { color: #1b5e20; text-decoration: underline; }
        .back-link { color: var(--text-color) !important; display: inline-block; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Sign into your account</h2>
        </div>
        
        <?php if (!empty($message)) echo $message; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" name="login" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i> SignIn
            </button>
        </form>
        
        <div class="text-center">
            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
            <a href="index.php" class="back-link"><i class="fas fa-home"></i> Back to Home</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>