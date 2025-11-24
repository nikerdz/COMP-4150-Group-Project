<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Membership.php');

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . PUBLIC_URL . "index.php");
    exit();
}

$eventId = (int)($_POST['event_id'] ?? 0);

if ($eventId <= 0) {
    $_SESSION['toast_message'] = "Invalid event.";
    $_SESSION['toast_type']    = "error";
    header("Location: " . PUBLIC_URL . "index.php");
    exit();
}

$eventModel      = new Event();
$membershipModel = new Membership();

// Fetch event to determine club
$event = $eventModel->findById($eventId);
if (!$event) {
    $_SESSION['toast_message'] = "Event not found.";
    $_SESSION['toast_type']    = "error";
    header("Location: " . PUBLIC_URL . "index.php");
    exit();
}

$clubId = (int)$event['club_id'];

// Permission check: must be exec
$membership = $membershipModel->getMembership($clubId, $_SESSION['user_id']);
if (!$membership || $membership['role'] === 'member') {
    $_SESSION['toast_message'] = "You do not have permission to delete this event.";
    $_SESSION['toast_type']    = "error";
    header("Location: " . PUBLIC_URL . "club/view-club.php?id=" . $clubId);
    exit();
}

// Perform delete
if ($eventModel->deleteEvent($eventId)) {
    $_SESSION['toast_message'] = "Event deleted successfully.";
    $_SESSION['toast_type']    = "success";

} else {
    $_SESSION['toast_message'] = "Failed to delete event.";
    $_SESSION['toast_type']    = "error";
}

header("Location: " . PUBLIC_URL . "club/view-club.php?id=" . $clubId);
exit();
