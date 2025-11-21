<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'Membership.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$clubId = isset($_POST['club_id']) ? (int)$_POST['club_id'] : 0;

if (!$clubId) {
    die("Invalid club.");
}

$membershipModel = new Membership();
$success = $membershipModel->join($userId, $clubId);

if ($success) {
    header("Location: " . CLUB_URL . "view-club.php?id=" . $clubId);
    exit;
} else {
    // Check if it's due to restrictions
    $_SESSION['error'] = "You cannot join this club because you do not meet its requirements.";
    header("Location: " . CLUB_URL . "view-club.php?id=" . $clubId);
    exit;
}
