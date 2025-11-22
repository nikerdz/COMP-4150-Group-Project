<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Membership.php');

session_start();

// --------------------------
// Helpers
// --------------------------
function event_error_and_back(string $message, int $eventId): void {
    $_SESSION['event_edit_error'] = $message;
    header("Location: " . PUBLIC_URL . "event/edit-event.php?id=" . $eventId);
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
    $eventId = (int)($_POST['event_id'] ?? 0);
    event_error_and_back("Invalid request.", $eventId);
}

// --------------------------
// Get POST data
// --------------------------
$eventId        = (int)($_POST['event_id'] ?? 0);
$eventName      = trim($_POST['event_name'] ?? '');
$eventLocation  = trim($_POST['event_location'] ?? '');
$eventDesc      = trim($_POST['event_description'] ?? '');
$eventCondition = $_POST['event_condition'] ?? 'none';
$eventDateRaw   = trim($_POST['event_date'] ?? '');
$capacityRaw    = trim($_POST['capacity'] ?? '');
$eventFeeRaw    = trim($_POST['event_fee'] ?? '');

if ($eventId <= 0) {
    die("Invalid event ID.");
}

if ($eventName === '') {
    event_error_and_back("Event name is required.", $eventId);
}

if (!in_array($eventCondition, ['none', 'women_only', 'first_year_only', 'undergrad_only'], true)) {
    event_error_and_back("Invalid access restriction selected.", $eventId);
}

// --------------------------
// Load event & check permission
// --------------------------
$eventModel      = new Event();
$membershipModel = new Membership();

$existingEvent = $eventModel->findById($eventId);
if (!$existingEvent) {
    die("Event not found.");
}

$clubId = (int)$existingEvent['club_id'];

$membership = $membershipModel->getMembership($clubId, $_SESSION['user_id']);
if (!$membership || $membership['role'] === 'member') {
    die("You do not have permission to edit this event.");
}

// --------------------------
// Parse / validate date & time
// --------------------------
$eventDate = null;
if ($eventDateRaw !== '') {
    // Expect format from <input type="datetime-local">
    $dt = DateTime::createFromFormat('Y-m-d\TH:i', $eventDateRaw);
    if (!$dt) {
        event_error_and_back("Invalid date/time format.", $eventId);
    }
    $eventDate = $dt->format('Y-m-d H:i:s');
}

// --------------------------
// Parse / validate capacity
// --------------------------
$capacity = null;
if ($capacityRaw !== '') {
    if (!ctype_digit($capacityRaw) || (int)$capacityRaw <= 0) {
        event_error_and_back("Capacity must be a positive whole number.", $eventId);
    }
    $capacity = (int)$capacityRaw;
}

// --------------------------
// Parse / validate fee
// --------------------------
$eventFee = 0.00;
if ($eventFeeRaw !== '') {
    if (!is_numeric($eventFeeRaw)) {
        event_error_and_back("Event fee must be a valid number.", $eventId);
    }
    $eventFee = (float)$eventFeeRaw;
    if ($eventFee < 0) {
        event_error_and_back("Event fee cannot be negative.", $eventId);
    }
}

// --------------------------
// Update event
// --------------------------
$updateData = [
    'event_name'        => $eventName,
    'event_description' => $eventDesc !== '' ? $eventDesc : null,
    'event_location'    => $eventLocation !== '' ? $eventLocation : null,
    'event_date'        => $eventDate,
    'capacity'          => $capacity,
    'event_condition'   => $eventCondition,
    'event_fee'         => $eventFee,
];

if ($eventModel->updateEvent($eventId, $updateData)) {
    // Toast on view-event page
    $_SESSION['toast_message'] = "Event updated successfully.";
    $_SESSION['toast_type']    = "success";

    header("Location: " . PUBLIC_URL . "event/view-event.php?id=" . $eventId);
    exit();
} else {
    event_error_and_back("Could not update event. Please try again.", $eventId);
}
