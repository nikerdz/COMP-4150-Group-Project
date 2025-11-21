<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Registration.php');

session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: ' . PUBLIC_URL . 'login.php');
    exit;
}

$eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
if ($eventId <= 0) {
    $_SESSION['error'] = 'Invalid event.';
    header('Location: ' . PUBLIC_URL . 'dashboard.php');
    exit;
}

$eventModel        = new Event();
$registrationModel = new Registration();

$event = $eventModel->findById($eventId);
if (!$event) {
    $_SESSION['error'] = 'Event not found.';
    header('Location: ' . PUBLIC_URL . 'dashboard.php');
    exit;
}

$fee = (float)($event['event_fee'] ?? 0);
if ($fee > 0) {
    // Guard: use pay flow for paid events
    header('Location: ' . PUBLIC_URL . 'event/pay-event.php?id=' . $eventId);
    exit;
}

// Already registered?
if ($registrationModel->isRegistered($userId, $eventId)) {
    $_SESSION['toast_message'] = 'You are already registered for this event.';
    $_SESSION['toast_type']    = 'info';
    header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
    exit;
}

// Capacity check
$capacity = $event['capacity'] !== null ? (int)$event['capacity'] : null;
if ($capacity !== null) {
    $currentCount = $registrationModel->countRegistrations($eventId);
    if ($currentCount >= $capacity) {
        $_SESSION['error'] = 'This event is full.';
        header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
        exit;
    }
}

// Register for free event
$ok = $registrationModel->register($userId, $eventId);

if ($ok) {
    $_SESSION['toast_message'] = 'You have been registered for this event.';
    $_SESSION['toast_type']    = 'success';
} else {
    $_SESSION['error'] = 'There was a problem registering you for this event.';
}

header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
exit;
