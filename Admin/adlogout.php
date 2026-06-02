<?php
include 'connection.php';
session_start();

if (!empty($_SESSION['login_activity_id']) && isset($_SESSION['user_id'])) {
	$activity_id = (int) $_SESSION['login_activity_id'];
	if ($activity_id > 0) {
		$stmt = $conn->prepare("UPDATE login_activity SET logout_at = NOW() WHERE id = ? AND user_id = ?");
		if ($stmt) {
			$user_id = (int) $_SESSION['user_id'];
			$stmt->bind_param('ii', $activity_id, $user_id);
			$stmt->execute();
			$stmt->close();
		}
	}
}

session_destroy();
header('Location: ../login.php');
?>
