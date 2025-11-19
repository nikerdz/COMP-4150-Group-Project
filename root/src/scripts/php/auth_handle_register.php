<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'User.php');

session_start();

// If user is already logged in, don't let them re-register
if (isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "index.php");
    exit();
}

// Helper function for setting an error and redirecting back to register page
function register_error_and_back(string $message): void {
    $_SESSION['register_error'] = $message;
    header("Location: " . PUBLIC_URL . "register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    register_error_and_back("Invalid request.");
}

// Basic required field validation
if (
    empty($_POST['first_name']) || empty($_POST['last_name']) ||
    empty($_POST['email']) || empty($_POST['password']) ||
    empty($_POST['confirm_password']) || empty($_POST['gender'])
) {
    register_error_and_back("Please fill in all required fields.");
}

// Clean inputs
$first    = trim($_POST['first_name']);
$last     = trim($_POST['last_name']);
$email    = trim($_POST['email']);
$pass     = $_POST['password'];
$confirm  = $_POST['confirm_password'];
$gender   = $_POST['gender'];                 // 'M' or 'F'
$faculty  = $_POST['faculty']        ?? null;
$level    = $_POST['level_of_study'] ?? 'undergraduate';
$yearRaw  = $_POST['year_of_study']  ?? null;

// Simple year validation (optional, can be empty)
$year = null;
if ($yearRaw !== null && $yearRaw !== '') {
    if (!ctype_digit((string)$yearRaw)) {
        register_error_and_back("Year of study must be a number.");
    }
    $year = (int)$yearRaw;
}

// Password match check
if ($pass !== $confirm) {
    register_error_and_back("Passwords do not match.");
}

// UWindsor email check
if (!preg_match("/@uwindsor\.ca$/", $email)) {
    register_error_and_back("Email must be a UWindsor (@uwindsor.ca) email.");
}

// Hash the password
$hashed = password_hash($pass, PASSWORD_DEFAULT);

try {
    // Use your User model to insert into DB
    $userModel = new User();
    $userModel->register([
        'first_name'     => $first,
        'last_name'      => $last,
        'email'          => $email,
        'password'       => $hashed,
        'gender'         => $gender,
        'faculty'        => $faculty,
        'level_of_study' => $level,
        'year_of_study'  => $year
    ]);

    // On success: redirect to login with a success message
    header("Location: " . PUBLIC_URL . "login.php?success=" . urlencode("Account created! Please log in."));
    exit();

} catch (PDOException $e) {
    // Duplicate email / unique constraint violation
    if ($e->getCode() == 23000) {
        register_error_and_back("Email already registered.");
    }

    // Generic error
    register_error_and_back("Registration failed. Please try again later.");
}
