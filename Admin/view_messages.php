<?php
session_start();
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include 'connection.php';

$messages = [];
$unread_count = 0;
$feedback_message = '';
$feedback_type = 'success';
$is_async_request = ($_SERVER['REQUEST_METHOD'] === 'POST') && (
    (isset($_POST['async']) && $_POST['async'] === '1') ||
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
);

$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'messages'");
if ($table_check && mysqli_num_rows($table_check) > 0) {
    $is_read_column = mysqli_query($conn, "SHOW COLUMNS FROM messages LIKE 'is_read'");
    if ($is_read_column && mysqli_num_rows($is_read_column) === 0) {
        mysqli_query($conn, "ALTER TABLE messages ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0");
    }

    $read_at_column = mysqli_query($conn, "SHOW COLUMNS FROM messages LIKE 'read_at'");
    if ($read_at_column && mysqli_num_rows($read_at_column) === 0) {
        mysqli_query($conn, "ALTER TABLE messages ADD COLUMN read_at DATETIME DEFAULT NULL");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'], $_POST['message_action'])) {
        $message_id = (int) $_POST['message_id'];
        $message_action = strtolower(trim($_POST['message_action']));
        $update_succeeded = false;
        $updated_is_read = null;
        $updated_read_at = '';

        if ($message_id > 0 && in_array($message_action, ['mark_read', 'mark_unread'], true)) {
            if ($message_action === 'mark_read') {
                $stmt = $conn->prepare("UPDATE messages SET is_read = 1, read_at = IFNULL(read_at, NOW()) WHERE id = ?");
                $feedback_message = 'Message marked as read.';
            } else {
                $stmt = $conn->prepare("UPDATE messages SET is_read = 0, read_at = NULL WHERE id = ?");
                $feedback_message = 'Message marked as unread.';
            }

            if ($stmt) {
                $stmt->bind_param('i', $message_id);
                if (!$stmt->execute()) {
                    $feedback_message = 'Could not update message status. Please try again.';
                    $feedback_type = 'danger';
                } else {
                    $update_succeeded = true;
                }
                $stmt->close();
            } else {
                $feedback_message = 'Could not update message status. Please try again.';
                $feedback_type = 'danger';
            }
        } else {
            $feedback_message = 'Invalid message update request.';
            $feedback_type = 'danger';
        }

        if ($update_succeeded) {
            $state_stmt = $conn->prepare("SELECT is_read, read_at FROM messages WHERE id = ? LIMIT 1");
            if ($state_stmt) {
                $state_stmt->bind_param('i', $message_id);
                $state_stmt->execute();
                $state_stmt->bind_result($db_is_read, $db_read_at);
                if ($state_stmt->fetch()) {
                    $updated_is_read = (int) $db_is_read;
                    $updated_read_at = $db_read_at !== null ? (string) $db_read_at : '';
                }
                $state_stmt->close();
            }
        }

        if ($updated_is_read === null) {
            $updated_is_read = $message_action === 'mark_read' ? 1 : 0;
        }

        if ($is_async_request) {
            header('Content-Type: application/json');
            if ($update_succeeded) {
                echo json_encode([
                    'success' => true,
                    'message' => $feedback_message,
                    'is_read' => $updated_is_read,
                    'read_at' => $updated_read_at
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $feedback_message,
                    'is_read' => $updated_is_read,
                    'read_at' => $updated_read_at
                ]);
            }
            exit();
        }
    }

    $res = mysqli_query($conn, "SELECT * FROM messages ORDER BY id DESC");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $messages[] = [
                'id' => isset($row['id']) ? (int) $row['id'] : (count($messages) + 1),
                'sender' => trim($row['sender_name'] ?? $row['name'] ?? 'Unknown'),
                'email' => trim($row['email'] ?? ''),
                'phone' => trim($row['phone'] ?? ''),
                'subject' => trim($row['subject'] ?? ''),
                'message' => trim($row['message_text'] ?? $row['message'] ?? ''),
                'sent_at' => trim($row['sent_at'] ?? $row['created_at'] ?? ''),
                'is_read' => isset($row['is_read']) ? (int) $row['is_read'] : 0,
                'read_at' => trim($row['read_at'] ?? '')
            ];
        }
    }
}

$unread_count = count(array_filter($messages, static function ($message) {
    return (int) ($message['is_read'] ?? 0) === 0;
}));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messages | Admin Panel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root {
            --leaf-900: #0f3c2a;
            --leaf-700: #1f6b3f;
            --leaf-600: #2f8952;
            --cream: #f6fbf6;
            --sun: #ffd166;
            --ink: #132019;
        }
    body { background: #f0f2f5; }
    .sidebar {
        background: linear-gradient(135deg, #0f3c2a, #1f6b3f);
        min-height: 100vh;
        color: #fff;
        padding-top: 20px;
    }
    .sidebar .nav-link {
        color: rgba(255,255,255,0.8);
        padding: 12px 20px;
        border-radius: 8px;
        margin: 4px 10px;
        transition: all 0.2s;
    }
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
        background: rgba(255,255,255,0.15);
        color: #fff;
    }
    .sidebar .nav-link i { margin-right: 10px; font-size: 1.1rem; }
    .brand { padding: 10px 20px 30px; border-bottom: 1px solid rgba(255,255,255,0.15); margin-bottom: 15px; }
    .brand h4 { margin: 0; font-weight: 700; }
    .brand small { opacity: 0.7; }
    .top-bar { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 15px 25px; }
    .message-card {
        border: none;
        border-radius: 12px;
        border-left: 4px solid #388e3c;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .message-card.unread-message {
        border-left-color: #dc3545;
        background: #fffaf5;
    }
    .message-trigger {
        width: 100%;
        padding: 0;
        text-align: left;
        background: #fff;
        cursor: pointer;
    }
    .message-trigger:focus {
        outline: 2px solid #2f8952;
        outline-offset: 2px;
    }
    .message-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
    .message-preview { color: #4f5d55; }
    .tap-hint { color: #2f8952; font-weight: 600; }
    .status-pill { font-size: 0.75rem; letter-spacing: 0.2px; }
    .message-card.unread-message .tap-hint { color: #b02a37; }
    .empty-state { text-align: center; padding: 60px 20px; color: #888; }
    .empty-state i { font-size: 4rem; display: block; margin-bottom: 15px; color: #ccc; }
</style>
</head>
<body>
<div class="container-fluid">
<div class="row">

    <div class="col-md-3 col-lg-2 sidebar d-none d-md-block">
        <div class="brand">
            <h4><i class="bi bi-egg-fried"></i> Kalungu Quality Feeds</h4>
            <small>Admin Panel</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="admin.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a class="nav-link" href="upload_photo.php"><i class="bi bi-cloud-arrow-up"></i> Upload Photo</a>
            <a class="nav-link" href="view_orders.php"><i class="bi bi-cart3"></i> Orders</a>
            <a class="nav-link active" href="view_messages.php"><i class="bi bi-envelope"></i> Messages</a>
            <a class="nav-link text-danger mt-3" href="adlogout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="col-md-9 col-lg-10 p-0">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-envelope me-2"></i>Messages</h5>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="adlogout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-left"></i> Logout</a>
            </div>
        </div>

        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">All Messages
                    <span class="badge bg-success ms-2"><?php echo count($messages); ?></span>
                    <span id="unreadCounter" data-count="<?php echo $unread_count; ?>" class="badge bg-danger ms-2">Unread: <?php echo $unread_count; ?></span>
                </h4>
            </div>

            <?php if ($feedback_message !== ''): ?>
                <div class="alert alert-<?php echo htmlspecialchars($feedback_type); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($feedback_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (count($messages) > 0): ?>
                <?php foreach ($messages as $index => $msg): ?>
                <?php
                    $modal_id = 'messageModal' . intval($msg['id']) . '_' . $index;
                    $is_unread = ((int)($msg['is_read'] ?? 0) === 0);
                    $status_text = $is_unread ? 'Unread' : 'Read';
                    $status_badge = $is_unread ? 'bg-danger' : 'bg-success';
                    $preview = $msg['message'];
                    if (strlen($preview) > 90) {
                        $preview = substr($preview, 0, 90) . '...';
                    }
                ?>
                <button type="button" class="card message-card message-trigger shadow-sm mb-3 <?php echo $is_unread ? 'unread-message' : ''; ?>" data-bs-toggle="modal" data-bs-target="#<?php echo $modal_id; ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">
                                    <i class="bi bi-person-circle text-success me-1"></i>
                                    <?php echo htmlspecialchars($msg['sender'] ?: 'Unknown'); ?>
                                </h6>
                                <small class="text-muted">
                                    <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($msg['email']); ?>
                                </small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">
                                    <i class="bi bi-clock me-1"></i>
                                    <?php echo htmlspecialchars($msg['sent_at'] ?: 'Unknown time'); ?>
                                </small>
                                <span class="badge rounded-pill status-pill message-status-badge <?php echo $status_badge; ?> mt-1"><?php echo $status_text; ?></span>
                            </div>
                        </div>
                        <hr class="my-2">
                        <p class="mb-1 message-preview"><?php echo htmlspecialchars($preview); ?></p>
                        <small class="tap-hint"><i class="bi bi-hand-index-thumb me-1"></i>Tap to view full message</small>
                    </div>
                </button>

                <div class="modal fade" id="<?php echo $modal_id; ?>" tabindex="-1" aria-hidden="true" data-message-id="<?php echo intval($msg['id']); ?>" data-is-unread="<?php echo $is_unread ? '1' : '0'; ?>" data-is-updating="0">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="bi bi-envelope-open me-2"></i>Message Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2"><strong>From:</strong> <?php echo htmlspecialchars($msg['sender'] ?: 'Unknown'); ?></p>
                                <p class="mb-2"><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>"><?php echo htmlspecialchars($msg['email']); ?></a></p>
                                <?php if (!empty($msg['phone'])): ?>
                                    <p class="mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($msg['phone']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($msg['subject'])): ?>
                                    <p class="mb-2"><strong>Subject:</strong> <?php echo htmlspecialchars($msg['subject']); ?></p>
                                <?php endif; ?>
                                <p class="mb-3"><strong>Sent:</strong> <?php echo htmlspecialchars($msg['sent_at'] ?: 'Unknown time'); ?></p>
                                <p class="mb-3"><strong>Status:</strong> <span class="badge rounded-pill modal-status-badge <?php echo $status_badge; ?>"><?php echo $status_text; ?></span></p>
                                <div class="p-3 rounded border bg-light">
                                    <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <form method="POST" class="m-0 message-action-form">
                                        <input type="hidden" name="message_id" value="<?php echo intval($msg['id']); ?>">
                                        <?php if ($is_unread): ?>
                                            <input class="message-action-input" type="hidden" name="message_action" value="mark_read">
                                            <button type="submit" class="btn btn-success btn-sm message-action-button"><i class="bi bi-check-circle me-1"></i>Mark as read</button>
                                        <?php else: ?>
                                            <input class="message-action-input" type="hidden" name="message_action" value="mark_unread">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm message-action-button"><i class="bi bi-arrow-counterclockwise me-1"></i>Mark as unread</button>
                                        <?php endif; ?>
                                    </form>
                                    <?php if (!empty($msg['read_at'])): ?>
                                        <small class="text-muted read-at-text">Read at: <?php echo htmlspecialchars($msg['read_at']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card shadow-sm empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>No messages yet</h5>
                    <p class="text-muted">When customers send you messages, they'll appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var unreadCounter = document.getElementById('unreadCounter');
    var messageModals = document.querySelectorAll('.modal[data-message-id]');

    messageModals.forEach(function (modalEl) {
        modalEl.addEventListener('shown.bs.modal', function () {
            if (modalEl.getAttribute('data-is-unread') !== '1') {
                return;
            }

            if (modalEl.getAttribute('data-is-updating') === '1') {
                return;
            }

            modalEl.setAttribute('data-is-updating', '1');

            var payload = new FormData();
            payload.append('message_id', modalEl.getAttribute('data-message-id'));
            payload.append('message_action', 'mark_read');
            payload.append('async', '1');

            fetch(window.location.href, {
                method: 'POST',
                body: payload,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (!data || data.success !== true) {
                    return;
                }

                modalEl.setAttribute('data-is-unread', '0');

                var trigger = document.querySelector('[data-bs-target="#' + modalEl.id + '"]');
                if (trigger) {
                    trigger.classList.remove('unread-message');
                    var cardStatusBadge = trigger.querySelector('.message-status-badge');
                    if (cardStatusBadge) {
                        cardStatusBadge.classList.remove('bg-danger');
                        cardStatusBadge.classList.add('bg-success');
                        cardStatusBadge.textContent = 'Read';
                    }
                }

                var modalStatusBadge = modalEl.querySelector('.modal-status-badge');
                if (modalStatusBadge) {
                    modalStatusBadge.classList.remove('bg-danger');
                    modalStatusBadge.classList.add('bg-success');
                    modalStatusBadge.textContent = 'Read';
                }

                var actionInput = modalEl.querySelector('.message-action-input');
                var actionButton = modalEl.querySelector('.message-action-button');
                if (actionInput && actionButton) {
                    actionInput.value = 'mark_unread';
                    actionButton.classList.remove('btn-success');
                    actionButton.classList.add('btn-outline-secondary');
                    actionButton.innerHTML = '<i class="bi bi-arrow-counterclockwise me-1"></i>Mark as unread';
                }

                var readAtText = modalEl.querySelector('.read-at-text');
                if (!readAtText) {
                    readAtText = document.createElement('small');
                    readAtText.className = 'text-muted read-at-text';
                    var footerWrap = modalEl.querySelector('.message-action-form');
                    if (footerWrap && footerWrap.parentElement) {
                        footerWrap.parentElement.appendChild(readAtText);
                    }
                }

                if (readAtText) {
                    var readAtValue = (data.read_at && String(data.read_at).trim() !== '') ? String(data.read_at) : 'Just now';
                    readAtText.textContent = 'Read at: ' + readAtValue;
                }

                if (unreadCounter) {
                    var currentCount = parseInt(unreadCounter.getAttribute('data-count') || '0', 10);
                    if (!isNaN(currentCount) && currentCount > 0) {
                        currentCount -= 1;
                        unreadCounter.setAttribute('data-count', String(currentCount));
                        unreadCounter.textContent = 'Unread: ' + currentCount;
                    }
                }
            })
            .catch(function () {
            })
            .finally(function () {
                modalEl.setAttribute('data-is-updating', '0');
            });
        });
    });
});
</script>
</body>
</html>
