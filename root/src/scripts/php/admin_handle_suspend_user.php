<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'User.php');

session_start();

// --- ADMIN CHECK ---
if (empty($_SESSION['is_admin'])) {
    die("Unauthorized.");
}

if (!isset($_POST['user_id'])) {
    die("Invalid request.");
}

$userId = (int)$_POST['user_id'];

if ($userId <= 0) {
    $_SESSION['toast_message'] = 'Invalid user.';
    header("Location: " . PUBLIC_URL . "admin/manage-users.php");
    exit();
}

$userModel = new User();
$ok = $userModel->suspendUser($userId);

if ($ok) {
    $_SESSION['toast_message'] = 'User suspended successfully.';
} else {
    $_SESSION['toast_message'] = 'Failed to suspend user.';
}

header("Location: " . USER_URL . "view-user.php?id=" . $userId);
exit();
