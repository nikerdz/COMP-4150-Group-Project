<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');

session_start();

// If user is already logged in, don't let them log in again
if (isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "index.php");
    exit();
}

/**
 * Helper: store error in session and go back to login page
 */
function login_error_and_back(string $message): void {
    $_SESSION['login_error'] = $message;
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    login_error_and_back("Invalid request method.");
}

// Basic required fields
if (empty($_POST['email']) || empty($_POST['password'])) {
    login_error_and_back("Please enter both email and password.");
}

$email    = trim($_POST['email']);
$password = $_POST['password'];

try {
    // Use the PDO connection from db_config.php
    /** @var PDO $pdo */
    $stmt = $pdo->prepare("
        SELECT 
            user_id,
            first_name,
            last_name,
            user_email,
            user_password,
            user_type,
            user_status
        FROM User
        WHERE user_email = :email
        LIMIT 1
    ");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    // No user with that email
    if (!$user) {
        login_error_and_back("No account found with that email.");
    }

    // Optional: block suspended users
    if (isset($user['user_status']) && $user['user_status'] === 'suspended') {
        login_error_and_back("Your account is suspended. Please contact an administrator.");
    }

    // Check password
    if (!password_verify($password, $user['user_password'])) {
        login_error_and_back("Incorrect password.");
    }

    // ✅ Successful login: store details in session
    $_SESSION['user_id']    = $user['user_id'];
    $_SESSION['user_email'] = $user['user_email'];
    $_SESSION['user_name']  = $user['first_name'] ?? '';
    $_SESSION['is_admin']   = (isset($user['user_type']) && $user['user_type'] === 'admin');

    // Clear old error if any
    unset($_SESSION['login_error']);

    // Redirect based on role (adjust if you want something else)
    if (!empty($_SESSION['is_admin'])) {
        header("Location: " . ADMIN_URL . "dashboard.php");
    } else {
        header("Location: " . USER_URL . "dashboard.php");
    }
    exit();

} catch (PDOException $e) {
    // You can log $e->getMessage() somewhere if you want
    login_error_and_back("Login failed. Please try again later.");
}
