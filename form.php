<?php
// form.php – Fixed paths using BASE_URL
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/connection.php';

// If form submitted, handle login
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Submit'])) {
    $firstname = trim($_POST['Firstname'] ?? '');
    $lastname = trim($_POST['Lastname'] ?? '');
    $email = trim($_POST['Email'] ?? '');
    $residence = trim($_POST['Residence'] ?? '');
    $password = $_POST['Password'] ?? '';

    // Simple validation (this is a basic example – not recommended for production)
    if ($firstname && $lastname && $email && $residence && $password) {
        // Since the original poultry.php expects firstname, secondname, password only,
        // we'll redirect to the proper login page.
        header('Location: ' . BASE_URL . '/pages/login.php');
        exit();
    } else {
        $message = '<div class="alert alert-danger">Please fill in all fields.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form | Kalungu Quality Feeds</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/form.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3>Login to Poultry System</h3>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        <form action="<?php echo BASE_URL; ?>/poultry.php" method="post">
                            <div class="mb-3">
                                <label>First Name</label>
                                <input type="text" name="Firstname" class="form-control" placeholder="Enter Firstname" required>
                            </div>
                            <div class="mb-3">
                                <label>Last Name</label>
                                <input type="text" name="Lastname" class="form-control" placeholder="Enter Lastname" required>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="Email" class="form-control" placeholder="Enter Your Email" required>
                            </div>
                            <div class="mb-3">
                                <label>Residence</label>
                                <input type="text" name="Residence" class="form-control" placeholder="Enter your place of residence" required>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" name="Password" class="form-control" placeholder="Enter Your password" required>
                            </div>
                            <button type="submit" name="Submit" value="Login" class="btn btn-success">Login</button>
                            <a href="<?php echo BASE_URL; ?>/pages/login.php" class="btn btn-link">Use main login</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>