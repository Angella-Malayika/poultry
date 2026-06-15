<?php
// pages/login.php – Fixed paths using BASE_URL from config.php

require_once dirname(__DIR__) . '/config.php';  // loads BASE_URL and starts session
require_once dirname(__DIR__) . '/connection.php';  // database connection

$message = ''; // Initialize message variable

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = '<div class="alert alert-danger">Please fill in all fields.</div>';
    } else {
        // Detect actual column names to avoid "Unknown column" errors across deployments.
        $cols = [];
        $col_res = $conn->query("SHOW COLUMNS FROM users");
        if ($col_res) {
            while ($c = $col_res->fetch_assoc()) {
                $cols[] = $c['Field'];
            }
        }

        $id_col       = in_array('id', $cols)              ? 'id'              : (in_array('user_id', $cols)  ? 'user_id'  : null);
        $pass_col     = in_array('password', $cols)        ? 'password'        : (in_array('password_hashed', $cols) ? 'password_hashed' : null);
        $has_username = in_array('username', $cols);
        $has_role     = in_array('role', $cols);

        if ($id_col === null || $pass_col === null || !$has_username) {
            $message = '<div class="alert alert-danger">An error occurred. Please contact the administrator.</div>';
        } else {
            $sel = "`{$id_col}` AS id, username, email, `{$pass_col}` AS pwd";
            if ($has_role) {
                $sel .= ", role";
            }

            $stmt = $conn->prepare("SELECT {$sel} FROM users WHERE email = ? LIMIT 1");
            if (!$stmt) {
                $message = '<div class="alert alert-danger">Something went wrong. Please try again later.</div>';
            } else {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows === 1) {
                    $row = $result->fetch_assoc();
                    $stored = $row['pwd'] ?? '';

                    // Verify password (hashed or legacy plain-text fallback).
                    $ok = $stored !== '' && (
                        password_verify($password, $stored) || hash_equals($stored, $password)
                    );

                    if ($ok) {
                        session_regenerate_id(true);
                        $_SESSION['logged_in'] = true;
                        $_SESSION['user_id']   = (int) $row['id'];
                        $_SESSION['username']  = $row['username'];
                        $_SESSION['email']     = $row['email'];
                        $role = $has_role ? strtolower(trim((string) ($row['role'] ?? 'user'))) : 'user';
                        if ($role === '') {
                            $role = 'user';
                        }
                        $_SESSION['role'] = $role;
                        $_SESSION['last_activity'] = time();

                        $conn->query(
                            "CREATE TABLE IF NOT EXISTS login_activity (\n"
                            . "id INT AUTO_INCREMENT PRIMARY KEY,\n"
                            . "user_id INT NOT NULL,\n"
                            . "username VARCHAR(50) NOT NULL,\n"
                            . "role VARCHAR(20) NOT NULL,\n"
                            . "ip_address VARCHAR(45) DEFAULT NULL,\n"
                            . "user_agent VARCHAR(255) DEFAULT NULL,\n"
                            . "login_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n"
                            . "logout_at DATETIME DEFAULT NULL\n"
                            . ")"
                        );

                        $activity_stmt = $conn->prepare(
                            "INSERT INTO login_activity (user_id, username, role, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)"
                        );
                        if ($activity_stmt) {
                            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
                            $user_agent = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
                            $activity_stmt->bind_param(
                                'issss',
                                $_SESSION['user_id'],
                                $_SESSION['username'],
                                $_SESSION['role'],
                                $ip_address,
                                $user_agent
                            );
                            if ($activity_stmt->execute()) {
                                $_SESSION['login_activity_id'] = (int) $activity_stmt->insert_id;
                            }
                            $activity_stmt->close();
                        }

                        // Redirect using BASE_URL
                        if ($role === 'admin') {
                            header("Location: " . BASE_URL . "./../Admin/admin.php");
                        } else {
                            header("Location: " . BASE_URL . "./index.php");
                        }
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
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="DxOSWhae3DL7OIIjettiAneNnAyV8CYP49sqXRnojeg" />
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

        html, body {
            height: 100%;
        }

        body {
            background: linear-gradient(135deg, var(--light-bg) 0%, var(--light-bg) 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        .login-wrapper {
            flex: 1;
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
            max-width: 400px;
            width: 100%;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 20px;
        }

        .login-logo {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 12px;
        }

        .login-logo .logo-name {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--primary-color);
            line-height: 1.1;
        }

        .login-logo .logo-subtitle {
            font-weight: 600;
            font-size: 0.72rem;
            color: var(--text-color);
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

        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: var(--primary-color);
            padding: 0;
            cursor: pointer;
            font-size: 1rem;
            line-height: 1;
        }

        .password-toggle:focus-visible {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
            border-radius: 4px;
        }

        .password-input {
            padding-right: 2.5rem;
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

        #footer {
            margin-top: auto;
        }
    </style>
</head>
<body>
    <main class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <div class="login-logo" aria-label="Kalungu Quality Feeds">
                    <span class="logo-name">Kalungu</span>
                    <span class="logo-subtitle">Quality Feeds</span>
                </div>
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
                    <div class="password-group">
                        <input type="password" class="form-control password-input" id="password" name="password" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password" aria-pressed="false">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" name="login" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> SignIn
                </button>
            </form>

            <div class="text-center">
                <p>Don't have an account? <a href="<?php echo BASE_URL; ?>/pages/signup.php">Sign up here</a></p>
                <a href="<?php echo BASE_URL; ?>./../index.php" class="back-link"><i class="fas fa-home"></i> Back to Home</a>
            </div>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        if (passwordInput && togglePassword) {
            togglePassword.addEventListener('click', function () {
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                this.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                this.setAttribute('aria-pressed', String(isHidden));
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            });
        }
    </script>
    <?php include dirname(__DIR__) . '/includes/footer.php'; ?>
</body>
</html>