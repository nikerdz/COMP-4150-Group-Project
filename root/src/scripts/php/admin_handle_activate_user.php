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

$userModel = new User();
$userModel->activateUser($userId);

header("Location: " . USER_URL . "view-user.php?id=" . $userId);
exit();
