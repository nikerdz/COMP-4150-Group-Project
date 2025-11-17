<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'User.php');

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

if (
    empty($_POST['first_name']) || empty($_POST['last_name']) ||
    empty($_POST['email']) || empty($_POST['password']) ||
    empty($_POST['confirm_password'])
) {
    header("Location: " . PUBLIC_URL . "register.php?error=Please fill in all required fields.");
    exit();
}

$first = trim($_POST['first_name']);
$last = trim($_POST['last_name']);
$email = trim($_POST['email']);
$pass = $_POST['password'];
$confirm = $_POST['confirm_password'];
$faculty = $_POST['faculty'] ?? null;
$year = $_POST['year_of_study'] ?? null;

if ($pass !== $confirm) {
    header("Location: " . PUBLIC_URL . "register.php?error=Passwords do not match.");
    exit();
}

if (!preg_match("/@uwindsor\.ca$/", $email)) {
    header("Location: " . PUBLIC_URL . "register.php?error=Email must be a UWindsor email.");
    exit();
}

$hashed = password_hash($pass, PASSWORD_DEFAULT);

try {
    $userModel = new User();
    $userModel->register([
        'first_name' => $first,
        'last_name' => $last,
        'email' => $email,
        'password' => $hashed,
        'faculty' => $faculty,
        'year_of_study' => $year
    ]);

    header("Location: " . PUBLIC_URL . "login.php?success=Account created! Please log in.");
    exit();

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        header("Location: " . PUBLIC_URL . "register.php?error=Email already registered.");
        exit();
    }

    header("Location: " . PUBLIC_URL . "register.php?error=Registration failed.");
    exit();
}
