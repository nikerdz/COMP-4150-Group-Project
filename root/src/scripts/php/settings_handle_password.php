<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'User.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

function settings_pw_error(string $message): void {
    $_SESSION['settings_error'] = $message;
    header("Location: " . USER_URL . "settings.php#password");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    settings_pw_error("Invalid request.");
}

$current = $_POST['current_password']      ?? '';
$new     = $_POST['new_password']          ?? '';
$confirm = $_POST['confirm_new_password']  ?? '';

if ($current === '' || $new === '' || $confirm === '') {
    settings_pw_error("Please fill in all password fields.");
}

if ($new !== $confirm) {
    settings_pw_error("New passwords do not match.");
}

if (strlen($new) < 8) {
    settings_pw_error("New password should be at least 8 characters.");
}

$userId    = (int) $_SESSION['user_id'];
$userModel = new User();
$user      = $userModel->findById($userId);

if (!$user) {
    session_unset();
    session_destroy();
    header("Location: " . PUBLIC_URL . "login.php?error=Account not found. Please log in again.");
    exit();
}

if (!password_verify($current, $user['user_password'])) {
    settings_pw_error("Current password is incorrect.");
}

$hashed = password_hash($new, PASSWORD_DEFAULT);

try {
    $userModel->updatePassword($userId, $hashed);

    $_SESSION['settings_error']   = null;
    $_SESSION['settings_success'] = "Password updated successfully.";

    header("Location: " . USER_URL . "settings.php#password");
    exit();

} catch (PDOException $e) {
    settings_pw_error("Could not update password. Please try again.");
}
