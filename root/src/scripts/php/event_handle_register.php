<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Registration.php');
require_once(MODELS_PATH . 'User.php');

session_start();

function userMeetsEventCondition(array $user, array $event): bool
{
    $condition = $event['event_condition'] ?? 'none';

    // Normalize fields
    $gender = strtolower(trim($user['gender'] ?? ''));
    $level  = $user['level_of_study'] ?? null;
    $year   = isset($user['year_of_study']) ? (int)$user['year_of_study'] : null;

    switch ($condition) {
        case 'women_only':
            return in_array($gender, ['female', 'woman', 'girl', 'f'], true);

        case 'undergrad_only':
            return $level === 'undergraduate';

        case 'first_year_only':
            return $year === 1;

        case 'none':
        default:
            return true;
    }
}

// --------------------------
// Must be logged in
// --------------------------
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: ' . PUBLIC_URL . 'login.php');
    exit;
}

// --------------------------
// Validate event_id
// --------------------------
$eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
if ($eventId <= 0) {
    $_SESSION['error'] = 'Invalid event.';
    header('Location: ' . PUBLIC_URL . 'dashboard.php');
    exit;
}

// --------------------------
// Load models
// --------------------------
$eventModel        = new Event();
$registrationModel = new Registration();
$userModel         = new User();

// --------------------------
// Fetch event
// --------------------------
$event = $eventModel->findById($eventId);
if (!$event) {
    $_SESSION['error'] = 'Event not found.';
    header('Location: ' . PUBLIC_URL . 'dashboard.php');
    exit;
}

// --------------------------
// Prevent registration for past events
// --------------------------
//
if (!empty($event['event_date'])) {
    try {
        $eventDate = new DateTime($event['event_date']);
        $now       = new DateTime('now');

        if ($eventDate < $now) {
            $_SESSION['error'] = 'This event has already passed. You can no longer register.';
            header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
            exit;
        }
    } catch (Exception $e) {
        // If parsing fails treat it as a past/invalid event
        $_SESSION['error'] = 'This event is not available for registration.';
        header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
        exit;
    }
}

// --------------------------
// Fetch user
// --------------------------
$user = $userModel->findById((int)$userId);
if (!$user) {
    $_SESSION['error'] = 'User not found.';
    header('Location: ' . PUBLIC_URL . 'dashboard.php');
    exit;
}

// --------------------------
// Enforce event restrictions (gender/year/undergrad)
// --------------------------
if (!userMeetsEventCondition($user, $event)) {
    $_SESSION['error'] = 'You do not meet the requirements to register for this event.';
    header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
    exit;
}

// --------------------------
// Paid vs free event
// --------------------------
$fee = (float)($event['event_fee'] ?? 0);
if ($fee > 0) {
    // Use pay flow for paid events
    header('Location: ' . PUBLIC_URL . 'event/pay-event.php?id=' . $eventId);
    exit;
}

// --------------------------
// Already registered?
// --------------------------
if ($registrationModel->isRegistered($userId, $eventId)) {
    $_SESSION['toast_message'] = 'You are already registered for this event.';
    $_SESSION['toast_type']    = 'info';
    header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
    exit;
}

// --------------------------
// Capacity check
// --------------------------
$capacity = $event['capacity'] !== null ? (int)$event['capacity'] : null;
if ($capacity !== null) {
    $currentCount = $registrationModel->countRegistrations($eventId);
    if ($currentCount >= $capacity) {
        $_SESSION['error'] = 'This event is full.';
        header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
        exit;
    }
}

// --------------------------
// Register for free event
// --------------------------
$ok = $registrationModel->register($userId, $eventId);

if ($ok) {
    $_SESSION['toast_message'] = 'You have been registered for this event.';
    $_SESSION['toast_type']    = 'success';
} else {
    $_SESSION['error'] = 'There was a problem registering you for this event.';
}

header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
exit;
