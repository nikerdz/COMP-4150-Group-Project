<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'User.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

function settings_delete_error(string $message): void {
    $_SESSION['settings_error'] = $message;
    header("Location: " . USER_URL . "settings.php#delete");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    settings_delete_error("Invalid request.");
}

$password = $_POST['delete_password'] ?? '';

if ($password === '') {
    settings_delete_error("Please enter your password to confirm deletion.");
}

$userId    = (int) $_SESSION['user_id'];
$userModel = new User();
$user      = $userModel->findById($userId);

if (!$user) {
    // Already gone?
    session_unset();
    session_destroy();
    header("Location: " . PUBLIC_URL . "index.php");
    exit();
}

if (!password_verify($password, $user['user_password'])) {
    settings_delete_error("Password is incorrect.");
}

try {
    // Delete user ON DELETE CASCADE will clean up related tables
    $userModel->deleteUser($userId);

    session_unset();
    session_destroy();

    header("Location: " . PUBLIC_URL . "index.php?success=" . urlencode("Your account has been deleted."));
    exit();

} catch (PDOException $e) {
    settings_delete_error("Could not delete account. Please try again.");
}
