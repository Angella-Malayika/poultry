<?php
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // include './dbcon/connection.php';
     $con = mysqli_connect('localhost','root', 1234,'project');
    if (isset($_POST['Sub']))
        {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $qery = "INSERT INTO project (name, email, password) VALUES ('$name', '$email', '$password')";
            $execute =mysqli_query($con, $qery);

    }
    
     /*
     MANUAL DATABASE SETUP (SIGN IN)
     1) Include your DB connection file here:
         include './dbcon/connection.php';

     2) After validating input below, add your logic to:
         - Find user by username or email
         - Verify password using password_verify()
         - Set session values
         - Redirect user/admin

     Example structure:
         $safeUsername = mysqli_real_escape_string($conn, $username);
         $sql = "SELECT * FROM users WHERE username='$safeUsername' OR email='$safeUsername'";
         // fetch user, verify password, then redirect
     */

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $message = '<div class="alert alert-danger">Username/Email and password are required.</div>';
    } else {
        $message = '<div class="alert alert-success">Sign-in form submitted. Add your database connection and authentication logic here.</div>';
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
            --secondary-color: #2e7d32;
            --text-color: #333;
            --light-bg: #f9fbe7;
            --white: #f1f8e9;
            --input-border: #2e7d32;
            --black: #333;
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

        .login-header i {
            font-size: 50px;
            color: var(--primary-color);
            margin-bottom: 10px;
            display: block;
        }

        .login-header h2 {
            color: var(--primary-color);
            font-weight: bold;
            margin: 0;
        }

        .login-header p {
            color: var(--text-color);
            margin-bottom: 0;
        }

        .form-label {
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .form-label i {
            margin-right: 0.5rem;
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
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: #999;
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

        .alert {
            border-radius: 6px;
            border-left: 4px solid;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background-color: #ffebee;
            border-color: #c62828;
            color: #2e7d32 ;
        }

        .alert-success {
            background-color: #e8f5e9;
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .text-center {
            margin-top: 20px;
        }

        .text-center p {
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .text-center a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .text-center a:hover {
            color: #1b5e20;
            text-decoration: underline;
        }

        .back-link {
            color: var(--text-color) !important;
            display: inline-block;
            margin-top: 10px;
        }

        .back-link i {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login text-success  margin-bottom: 10px;
            display: block;">
            <!-- <i class="fas fa-sign-in-alt"></i> -->
            <h2>Sign into your account</h2>
            
        </div>
        
        <?php echo $message; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label"><i class="fas fa-user"></i> Username or Email</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username or email" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>
        
        <div class="text-center">
            <p>Don't have an account? <a href="register.php">Sign up here</a></p>
            <a href="index.php" class="back-link"><i class="fas fa-home"></i> Back to Home</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
