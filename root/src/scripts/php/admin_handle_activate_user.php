<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'User.php');

session_start();

// --- ADMIN CHECK ---
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    die("Unauthorized.");
}

if (!isset($_POST['user_id'])) {
    die("Invalid request.");
}

$userId = (int)$_POST['user_id'];
$currentAdminId = (int)$_SESSION['user_id'];

if ($userId <= 0) {
    $_SESSION['toast_message'] = 'Invalid user.';
    header("Location: " . PUBLIC_URL . "admin/manage-users.php");
    exit();
}

// Prevent an admin from changing their own status here
if ($userId === $currentAdminId) {
    $_SESSION['toast_message'] = 'You cannot change your own status from this screen.';
    header("Location: " . PUBLIC_URL . "admin/manage-users.php");
    exit();
}

$userModel = new User();
$ok = $userModel->activateUser($userId);

if ($ok) {
    $_SESSION['toast_message'] = 'User activated successfully.';
} else {
    $_SESSION['toast_message'] = 'Failed to activate user.';
}

header("Location: " . USER_URL . "view-user.php?id=" . $userId);
exit();
