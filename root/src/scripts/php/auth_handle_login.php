<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'User.php');

session_start();

// If user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "index.php");
    exit();
}

/**
 * Helper: store error in session and redirect
 */
function login_error_and_back(string $message): void {
    $_SESSION['login_error'] = $message;
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    login_error_and_back("Invalid request method.");
}

if (empty($_POST['email']) || empty($_POST['password'])) {
    login_error_and_back("Please enter both email and password.");
}

$email    = trim($_POST['email']);
$password = $_POST['password'];

// Create User model instance
$userModel = new User();

try {
    // Attempt to find user by email
    $user = $userModel->findByEmail($email);

    // No user found
    if (!$user) {
        login_error_and_back("No account found with that email.");
    }

    // Block suspended users
    if (!empty($user['user_status']) && $user['user_status'] === 'suspended') {
        login_error_and_back("Your account is suspended. Please contact admin.");
    }

    // Validate password
    if (!password_verify($password, $user['user_password'])) {
        login_error_and_back("Incorrect password.");
    }

    // ✅ Login successful - Set session values
    $_SESSION['user_id']       = $user['user_id'];
    $_SESSION['user_email']    = $user['user_email'];

    // Name pulled directly from the DB row we just fetched
    $_SESSION['first_name']    = $user['first_name'] ?? '';
    $_SESSION['last_name']     = $user['last_name'] ?? '';
    $_SESSION['user_name']     = $user['first_name'] ?? ''; // alias if you want

    $_SESSION['is_admin']      = (!empty($user['user_type']) && $user['user_type'] === 'admin');

    unset($_SESSION['login_error']);

    // Redirect based on user type
    if (!empty($_SESSION['is_admin'])) {
        header("Location: " . ADMIN_URL . "dashboard.php");
    } else {
        header("Location: " . USER_URL . "dashboard.php");
    }
    exit();

} catch (PDOException $e) {
    login_error_and_back("Login failed. Please try again later.");
}
