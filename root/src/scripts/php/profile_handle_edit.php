<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'User.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

function profile_error_and_back(string $message): void {
    $_SESSION['profile_error'] = $message;
    header("Location: " . USER_URL . "edit-profile.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    profile_error_and_back("Invalid request.");
}

$firstName = trim($_POST['first_name'] ?? '');
$lastName  = trim($_POST['last_name'] ?? '');
$faculty   = trim($_POST['faculty'] ?? '');
$level     = $_POST['level_of_study'] ?? 'undergraduate';
$yearRaw   = trim($_POST['year_of_study'] ?? '');

// Interests come as an array of category_id values
$interests = isset($_POST['interests']) && is_array($_POST['interests'])
    ? $_POST['interests']
    : [];

if ($firstName === '' || $lastName === '') {
    profile_error_and_back("First and last name are required.");
}

if ($faculty === '') {
    profile_error_and_back("Please select your faculty.");
}

if ($yearRaw !== '' && (!ctype_digit($yearRaw) || (int)$yearRaw < 1 || (int)$yearRaw > 20)) {
    profile_error_and_back("Year of study must be a number between 1 and 20.");
}

$userId    = (int) $_SESSION['user_id'];
$userModel = new User();

try {
    // Update the basic profile info
    $userModel->updateProfile($userId, [
        'first_name'      => $firstName,
        'last_name'       => $lastName,
        'faculty'         => $faculty,
        'level_of_study'  => $level,
        'year_of_study'   => $yearRaw === '' ? null : (int)$yearRaw
    ]);

    // Update interests
    $userModel->updateInterests($userId, $interests);

    $_SESSION['first_name'] = $firstName;

    // Clear any old errors
    unset($_SESSION['profile_error']);

    // Flash success
    $_SESSION['profile_success'] = "Profile updated successfully.";

    // Redirect to profile
    header("Location: " . USER_URL . "profile.php");
    exit();

} catch (PDOException $e) {
    profile_error_and_back("Could not update profile. Please try again.");
}
