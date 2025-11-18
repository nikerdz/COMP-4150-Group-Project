<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "index.php");
    exit();
}

function login_error_and_back(string $message): void {
    $_SESSION['login_error'] = $message;
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    login_error_and_back("Invalid request method.");
}

if (empty($_POST['email']) || empty($_POST['password'])) {
    login_error_and_back("Please enter both email and password.");
}

$email    = trim($_POST['email']);
$password = $_POST['password'];

try {
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

    if (!empty($user['user_status']) && $user['user_status'] === 'suspended') {
        login_error_and_back("Your account is suspended. Please contact an administrator.");
    }

    if (!password_verify($password, $user['user_password'])) {
        login_error_and_back("Incorrect password.");
    }

    $_SESSION['user_id']    = (int)$user['user_id'];
    $_SESSION['user_email'] = $user['user_email'];

    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name']  = $user['last_name'];

    $_SESSION['user_name']  = $user['first_name'];

    $_SESSION['is_admin']   = (!empty($user['user_type']) && $user['user_type'] === 'admin');

    unset($_SESSION['login_error']);

    if (!empty($_SESSION['is_admin'])) {
        header("Location: " . ADMIN_URL . "dashboard.php");
    } else {
        header("Location: " . USER_URL . "dashboard.php");
    }
    exit();

} catch (PDOException $e) {
    login_error_and_back("Login failed. Please try again later.");
}
