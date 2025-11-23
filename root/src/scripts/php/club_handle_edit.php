<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'Club.php');
require_once(MODELS_PATH . 'Membership.php');

session_start();

// --------------------------
// Helpers
// --------------------------
function club_error_and_back(string $message, int $clubId): void {
    $_SESSION['club_edit_error'] = $message;
    header("Location: " . PUBLIC_URL . "club/edit-club.php?id=" . $clubId);
    exit();
}

// --------------------------
// Must be logged in
// --------------------------
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

// --------------------------
// Validate POST
// --------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    club_error_and_back("Invalid request.", (int)($_POST['club_id'] ?? 0));
}

// --------------------------
// Get POST data
// --------------------------
$clubId        = (int)($_POST['club_id'] ?? 0);
$clubName      = trim($_POST['club_name'] ?? '');
$clubEmail     = trim($_POST['club_email'] ?? '');
$clubDesc      = trim($_POST['club_description'] ?? '');
$clubCondition = $_POST['club_condition'] ?? 'none';
$categories    = isset($_POST['categories']) && is_array($_POST['categories'])
    ? array_map('intval', $_POST['categories'])
    : [];

// --------------------------
// Basic validation
// --------------------------
if ($clubId <= 0) {
    die("Invalid club ID.");
}

if ($clubName === '') {
    club_error_and_back("Club name is required.", $clubId);
}

if (!in_array($clubCondition, ['none', 'women_only', 'first_year_only', 'undergrad_only'], true)) {
    club_error_and_back("Invalid access restriction selected.", $clubId);
}

// --------------------------
// Check if user is exec
// --------------------------
$membershipModel = new Membership();
$membership = $membershipModel->getMembership($clubId, $_SESSION['user_id']);
if (!$membership || $membership['role'] === 'member') {
    die("You do not have permission to edit this club.");
}

// --------------------------
// Update club
// --------------------------
$clubModel = new Club();

$updateData = [
    'club_name'        => $clubName,
    'club_email'       => $clubEmail ?: null,
    'club_description' => $clubDesc ?: null,
    'club_condition'   => $clubCondition,
    'tags'             => $categories,
];

if ($clubModel->updateClub($clubId, $updateData)) {
    // Set toast message for next page
    $_SESSION['toast_message'] = "Club updated successfully.";
    $_SESSION['toast_type'] = "success"; 

    // Redirect to view-club.php
    header("Location: " . PUBLIC_URL . "club/view-club.php?id=" . $clubId);
    exit();
} else {
    club_error_and_back("Could not update club. Please try again.", $clubId);
}
