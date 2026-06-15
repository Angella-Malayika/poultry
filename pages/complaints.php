<?php
// pages/complaints.php – Fixed paths using BASE_URL from config.php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth_required.php';

$name_value = $_SESSION['username'] ?? '';
$email_value = $_SESSION['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="DxOSWhae3DL7OIIjettiAneNnAyV8CYP49sqXRnojeg" />
    <title>Customer Feedback | Kalungu Quality Feeds</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/joy.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .feedback-hero {
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            color: #fff;
            padding: 50px 0;
            text-align: center;
        }
        .feedback-card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 18px 40px rgba(18, 32, 23, 0.08);
        }
        .container p{
            color: #d1e7dd;
        }
    </style>
</head>
<body>
<?php include dirname(__DIR__) . '/includes/header.php'; ?>

<section class="feedback-hero">
    <div class="container">
        <h1><i class="fas fa-comment-dots me-2"></i>Customer Feedback</h1>
        <p class="lead mb-0">Report issues, share complaints, or send appreciation.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php
        if (!empty($_SESSION['feedback_success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['feedback_success']) . '</div>';
            unset($_SESSION['feedback_success']);
        }
        if (!empty($_SESSION['feedback_error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['feedback_error']) . '</div>';
            unset($_SESSION['feedback_error']);
        }
        ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card feedback-card">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="mb-4">Send a complaint or appreciation</h3>
                        <form action="<?php echo BASE_URL; ?>/process_complaint.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name_value); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email_value); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" name="phone" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" title="Phone number must be 10 digits" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Order ID</label>
                                    <input type="text" class="form-control" name="order_id" placeholder="Example: 125" required>
                                    <small class="text-muted d-block mt-1">Enter the order ID from one of your completed orders.</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Type</label>
                                    <select class="form-select" name="category" required>
                                        <option value="">Select</option>
                                        <option value="complaint">Complaint</option>
                                        <option value="appreciation">Appreciation</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Subject</label>
                                    <input type="text" class="form-control" name="subject" placeholder="Short subject" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Message</label>
                                    <textarea class="form-control" name="message" rows="5" placeholder="Describe the issue or share your feedback" required></textarea>
                                </div>
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-paper-plane me-1"></i> Submit Feedback
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

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>