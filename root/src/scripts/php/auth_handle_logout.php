<?php
require_once(__DIR__ . '/../../config/constants.php');

session_start();

// Clear all session data
$_SESSION = [];
session_unset();
session_destroy();

// Floating message text
$successMsg = urlencode('You have been logged out.');

// Redirect to login page with a success message
header("Location: " . PUBLIC_URL . "login.php?success={$successMsg}");
exit();
