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

$pdo->prepare("UPDATE Club SET club_status = 'active' WHERE club_id = ?")
    ->execute([$clubId]);

header("Location: " . CLUB_URL . "view-club.php?id=" . $clubId);
exit;
