<?php
// pages/poultry.php – Fixed paths using BASE_URL
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Fix variable names – assuming form fields are 'firstname', 'secondname', 'password'
    $firstname = trim($_POST['firstname'] ?? '');
    $secondname = trim($_POST['secondname'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if ($firstname === '' || $secondname === '' || $password === '') {
        echo '<script>
            alert("Please fill in all fields.");
            window.location.href = "' . BASE_URL . '/pages/poultry.php";
        </script>';
        exit();
    }

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM login WHERE firstname = ? AND secondname = ? AND password = ?");
    if ($stmt) {
        $stmt->bind_param("sss", $firstname, $secondname, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->num_rows;
        $stmt->close();

        if ($count == 1) {
            header("Location: " . BASE_URL . "/pages/welcome.php");
            exit();
        } else {
            echo '<script>
                alert("Login failed. Invalid firstname, secondname, or password.");
                window.location.href = "' . BASE_URL . '/pages/poultry.php";
            </script>';
            exit();
        }
    } else {
        echo '<script>
            alert("Database error. Please try again later.");
            window.location.href = "' . BASE_URL . '/pages/poultry.php";
        </script>';
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poultry Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3>Poultry Login</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label>First Name</label>
                            <input type="text" name="firstname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Second Name</label>
                            <input type="text" name="secondname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="submit" class="btn btn-success">Login</button>
                        <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-secondary">Back to Home</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>