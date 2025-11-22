<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'Event.php');

session_start();

// Must be admin
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: " . PUBLIC_URL . "index.php");
    exit();
}

$eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;

if ($eventId <= 0) {
    $_SESSION['error'] = 'Invalid event.';
    header("Location: " . PUBLIC_URL . "admin/manage-events.php");
    exit();
}

$eventModel = new Event();
$ok = $eventModel->updateStatus($eventId, 'approved');

if ($ok) {
    $_SESSION['toast_message'] = 'Event approved successfully.';
} else {
    $_SESSION['error'] = 'Failed to approve event.';
}

// Go back to the event page
header("Location: " . PUBLIC_URL . "event/view-event.php?id=" . $eventId);
exit();
