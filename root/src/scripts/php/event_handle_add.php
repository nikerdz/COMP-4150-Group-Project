<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Membership.php');
require_once(MODELS_PATH . 'Club.php');

session_start();

// user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to add an event.";
    // Redirect to the correct login page
    header("Location: " . PUBLIC_URL . 'login.php');
    exit;
}

// Check club ID
$clubId = isset($_POST['club_id']) ? (int)$_POST['club_id'] : 0;

$clubModel       = new Club();
$membershipModel = new Membership();
$eventModel      = new Event();

$club = $clubModel->findById($clubId);
if (!$club) {
    $_SESSION['error'] = "Club not found.";
    header("Location: " . PUBLIC_URL . 'clubs.php');
    exit;
}

// Check if user is an executive
$membership = $membershipModel->getMembership($clubId, $_SESSION['user_id']);
if (!$membership || $membership['role'] === 'member') {
    $_SESSION['error'] = "Only club executives can add events.";
    header("Location: " . PUBLIC_URL . 'club/view-club.php?id=' . $clubId);
    exit;
}

// Validate inputs
$eventName        = trim($_POST['event_name'] ?? '');
$eventDescription = trim($_POST['event_description'] ?? '');
$eventLocation    = trim($_POST['event_location'] ?? '');
$eventDate        = $_POST['event_date'] ?? '';
$capacity         = isset($_POST['capacity']) ? (int)$_POST['capacity'] : null;
$eventCondition   = $_POST['event_condition'] ?? 'none';
$eventFee         = isset($_POST['event_fee']) ? (float)$_POST['event_fee'] : 0.00;

if (empty($eventName) || empty($eventDate)) {
    $_SESSION['error'] = "Event name and date are required.";
    // Correct path back to Add Event page
    header("Location: " . PUBLIC_URL . "event/add-event.php?club_id={$clubId}");
    exit;
}

// Prepare data
$data = [
    'club_id'          => $clubId,
    'event_name'       => $eventName,
    'event_description'=> $eventDescription,
    'event_location'   => $eventLocation,
    'event_date'       => $eventDate,
    'capacity'         => $capacity,
    'event_condition'  => $eventCondition,
    'event_fee'        => $eventFee
];

// Insert event
if ($eventModel->createEvent($data)) {
    // Use toast message for view-club.php 
    $_SESSION['toast_message'] = "Event '{$eventName}' created successfully, pending Admin approval!";
    $_SESSION['toast_type']    = 'success';

    header("Location: " . PUBLIC_URL . "club/view-club.php?id={$clubId}");
    exit;
} else {
    $_SESSION['error'] = "Failed to create event. The event name might already exist.";
    // Correct path back to Add Event page on error
    header("Location: " . PUBLIC_URL . "event/add-event.php?club_id={$clubId}");
    exit;
}
