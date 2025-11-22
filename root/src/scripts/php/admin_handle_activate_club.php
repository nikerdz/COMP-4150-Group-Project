<?php
require_once('../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'Club.php');

session_start();

if (empty($_SESSION['is_admin'])) {
    die("Unauthorized.");
}

if (!isset($_POST['club_id'])) {
    die("Invalid request.");
}

$clubId = (int)$_POST['club_id'];

if ($clubId <= 0) {
    $_SESSION['toast_message'] = 'Invalid club.';
    header("Location: " . PUBLIC_URL . "admin/manage-clubs.php");
    exit();
}

// Activate club
$stmt = $pdo->prepare("UPDATE Club SET club_status = 'active' WHERE club_id = ?");
$ok   = $stmt->execute([$clubId]);

if ($ok) {
    $_SESSION['toast_message'] = 'Club activated successfully.';
} else {
    $_SESSION['toast_message'] = 'Failed to activate club.';
}

header("Location: " . CLUB_URL . "view-club.php?id=" . $clubId);
exit();
