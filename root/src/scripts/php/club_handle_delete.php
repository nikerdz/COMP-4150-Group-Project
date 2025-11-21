<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'Club.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

$clubId = $_POST['club_id'] ?? 0;
$confirmName = trim($_POST['confirm_club_name'] ?? '');

$clubModel = new Club();
$club = $clubModel->findById($clubId);

if (!$club) {
    $_SESSION['toast_message'] = "Club not found.";
    $_SESSION['toast_type']    = "error";

    // send them back to their clubs page under /club/
    header("Location: " . PUBLIC_URL . "club/user-clubs.php");
    exit();
}

// Must match exact name
if ($confirmName !== $club['club_name']) {
    $_SESSION['toast_message'] = "Club name did not match — deletion cancelled.";
    $_SESSION['toast_type']    = "error";

    header("Location: " . PUBLIC_URL . "club/edit-club.php?id=" . $clubId);
    exit();
}

// Delete club
if ($clubModel->deleteClub($clubId)) {
    // ✅ include the club name in the toast
    $_SESSION['toast_message'] = 'Club "' . $club['club_name'] . '" has been deleted.';
    $_SESSION['toast_type']    = "success";

    // ✅ redirect to /club/user-clubs.php
    header("Location: " . PUBLIC_URL . "club/user-clubs.php");
    exit();
}

// On failure
$_SESSION['toast_message'] = "Failed to delete club.";
$_SESSION['toast_type']    = "error";

header("Location: " . PUBLIC_URL . "club/edit-club.php?id=" . $clubId);
exit();
