<?php
require_once __DIR__ . '/../auth_required.php';
include '../connection.php';

$user_id = (int) ($_SESSION['user_id'] ?? 0);
$profile = [
    'username' => (string) ($_SESSION['username'] ?? ''),
    'email' => (string) ($_SESSION['email'] ?? ''),
];

$account_password = '';
$message = '';

$stmt = $conn->prepare('SELECT username, email, password FROM users WHERE user_id = ? LIMIT 1');
if ($stmt) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $profile['username'] = (string) ($row['username'] ?? $profile['username']);
        $profile['email'] = (string) ($row['email'] ?? $profile['email']);
        $account_password = (string) ($row['password'] ?? '');
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $current_password = (string) ($_POST['current_password'] ?? '');
    $new_password = (string) ($_POST['new_password'] ?? '');
    $confirm_password = (string) ($_POST['confirm_password'] ?? '');

    if ($username === '' || $email === '') {
        $message = '<div class="alert alert-danger">Username and email are required.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-danger">Please enter a valid email address.</div>';
    } else {
        $needs_password_change = $new_password !== '' || $confirm_password !== '' || $current_password !== '';
        if ($needs_password_change) {
            if ($current_password === '' || $new_password === '' || $confirm_password === '') {
                $message = '<div class="alert alert-danger">Fill in your current password and the new password fields.</div>';
            } elseif (!password_verify($current_password, $account_password) && !hash_equals($account_password, $current_password)) {
                $message = '<div class="alert alert-danger">Current password is incorrect.</div>';
            } elseif ($new_password !== $confirm_password) {
                $message = '<div class="alert alert-danger">New passwords do not match.</div>';
            } elseif (strlen($new_password) < 6) {
                $message = '<div class="alert alert-danger">New password must be at least 6 characters long.</div>';
            }
        }

        if ($message === '') {
            $duplicate_stmt = $conn->prepare('SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id <> ? LIMIT 1');
            if ($duplicate_stmt) {
                $duplicate_stmt->bind_param('ssi', $username, $email, $user_id);
                $duplicate_stmt->execute();
                $duplicate_result = $duplicate_stmt->get_result();

                if ($duplicate_result && $duplicate_result->num_rows > 0) {
                    $message = '<div class="alert alert-danger">Username or email already exists.</div>';
                }

                $duplicate_stmt->close();
            }

            if ($message === '') {
                $new_password_hash = $new_password !== '' ? password_hash($new_password, PASSWORD_DEFAULT) : '';

                if ($new_password_hash !== '') {
                    $update_stmt = $conn->prepare('UPDATE users SET username = ?, email = ?, password = ? WHERE user_id = ?');
                    if ($update_stmt) {
                        $update_stmt->bind_param('sssi', $username, $email, $new_password_hash, $user_id);
                    }
                } else {
                    $update_stmt = $conn->prepare('UPDATE users SET username = ?, email = ? WHERE user_id = ?');
                    if ($update_stmt) {
                        $update_stmt->bind_param('ssi', $username, $email, $user_id);
                    }
                }

                if (isset($update_stmt) && $update_stmt) {
                    if ($update_stmt->execute()) {
                        $_SESSION['username'] = $username;
                        $_SESSION['email'] = $email;
                        $profile['username'] = $username;
                        $profile['email'] = $email;
                        $message = '<div class="alert alert-success">Your profile has been updated successfully.</div>';
                    } else {
                        $message = '<div class="alert alert-danger">Unable to update your profile right now.</div>';
                    }
                    $update_stmt->close();
                } else {
                    $message = '<div class="alert alert-danger">Unable to update your profile right now.</div>';
                }
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
    <title>Profile | Kalungu Quality Feeds</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        body {
            background: linear-gradient(180deg, #f7faf4 0%, #ffffff 100%);
        }

        .profile-hero {
            background: linear-gradient(135deg, #1b5e20, #2e7d32 100%);
            color: #fff;
            padding: 4rem 0 2.5rem;
        }

        .profile-card {
            border: 0;
            border-radius: 22px;
            box-shadow: 0 18px 45px rgba(18, 32, 23, 0.08);
        }

        /* Password eye toggle styles */
        .password-wrap {
            position: relative;
        }

        .password-wrap .form-control {
            padding-right: 2.8rem;
            transition: padding 0.15s ease;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 0.75rem;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            padding: 0;
            line-height: 1;
            cursor: pointer;
            color: #198754;
            z-index: 2;
        }

        .password-toggle i {
            font-size: 1rem;
            pointer-events: none;
        }

        .password-toggle:focus {
            outline: none;
            box-shadow: none;
        }

        .password-toggle:hover {
            color: #146c43;
        }

        @media (max-width: 576px) {
            .password-toggle {
                right: 0.5rem;
            }

            .col-md-4 .form-control {
                padding-right: 2.1rem;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <section class="profile-hero">
        <div class="container">
            <h1 class="mb-2"><i class="fa-solid fa-user-pen me-2"></i>Your Profile</h1>
            <p class="mb-0">Update your account details whenever you need to.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <div class="card profile-card">
                        <div class="card-body p-4 p-md-5">
                            <?php echo $message; ?>

                            <form method="POST" action="" id="edit-profile">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Username</label>
                                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($profile['username']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($profile['email']); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <hr>
                                    </div>
                                    <div class="col-12">
                                        <h2 class="h5 mb-1">Change Password</h2>
                                        <p class="text-muted small mb-0">Leave this blank if you do not want to change your password.</p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold" for="current_password">Current Password</label>
                                        <div class="password-wrap">
                                            <input id="current_password" type="password" name="current_password" class="form-control" autocomplete="current-password">
                                            <button type="button" class="password-toggle btn btn-link p-0 text-success" data-target="current_password" aria-label="Show current password">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold" for="new_password">New Password</label>
                                        <div class="password-wrap">
                                            <input id="new_password" type="password" name="new_password" class="form-control" autocomplete="new-password">
                                            <button type="button" class="password-toggle btn btn-link p-0 text-success" data-target="new_password" aria-label="Show new password">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold" for="confirm_password">Confirm Password</label>
                                        <div class="password-wrap">
                                            <input id="confirm_password" type="password" name="confirm_password" class="form-control" autocomplete="new-password">
                                            <button type="button" class="password-toggle btn btn-link p-0 text-success" data-target="confirm_password" aria-label="Show confirm password">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex flex-wrap gap-2 justify-content-end mt-2">
                                        <a href="../index.php" class="btn btn-outline-secondary">Back Home</a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa-solid fa-floppy-disk me-2"></i>Save Changes
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.password-toggle').forEach(function(button) {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    if (!input) return;

                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');

                    clearTimeout(input._hideTimer);
                    input._hideTimer = setTimeout(function() {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }, 5000);
                });
            });
        });
    </script>
</body>

</html>