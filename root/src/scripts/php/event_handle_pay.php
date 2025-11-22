<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Registration.php');
require_once(MODELS_PATH . 'Payment.php');
require_once(MODELS_PATH . 'User.php');

session_start();

/**
 * Check if a user meets the restrictions for an event.
 *
 * Event conditions (from your DB):
 *  - 'none'
 *  - 'women_only'
 *  - 'undergrad_only'
 *  - 'first_year_only'
 *
 * User fields (from User table):
 *  - gender (VARCHAR(20))
 *  - level_of_study ENUM('undergraduate', 'graduate')
 *  - year_of_study INT
 */
function userMeetsEventCondition(array $user, array $event): bool
{
    $condition = $event['event_condition'] ?? 'none';

    // Normalize fields
    $gender = strtolower(trim($user['gender'] ?? ''));
    $level  = $user['level_of_study'] ?? null;
    $year   = isset($user['year_of_study']) ? (int)$user['year_of_study'] : null;

    switch ($condition) {
        case 'women_only':
            // Accept common variants like "female", "woman", etc.
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

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: ' . PUBLIC_URL . 'login.php');
    exit;
}

$eventId       = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
$methodRaw     = $_POST['payment_method'] ?? '';
$cardNumberRaw = $_POST['card_number']     ?? '';
$expiryRaw     = $_POST['expiry']          ?? '';
$cvvRaw        = $_POST['cvv']             ?? '';

if ($eventId <= 0) {
    $_SESSION['error'] = 'Invalid event.';
    header('Location: ' . PUBLIC_URL . 'dashboard.php');
    exit;
}

// Clean up card fields (remove spaces)
$cardNumber = preg_replace('/\s+/', '', $cardNumberRaw);
$expiry     = preg_replace('/\s+/', '', $expiryRaw);
$cvv        = preg_replace('/\s+/', '', $cvvRaw);

/* -----------------------------
   Basic numeric validation
----------------------------- */

// Card number: 13–19 digits
if (!ctype_digit($cardNumber) || strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
    $_SESSION['error'] = 'Please enter a valid card number.';
    header('Location: ' . PUBLIC_URL . 'event/pay-event.php?id=' . $eventId);
    exit;
}

// Expiry: MMYY (4 digits)
if (!ctype_digit($expiry) || strlen($expiry) !== 4) {
    $_SESSION['error'] = 'Please enter expiry in MMYY format.';
    header('Location: ' . PUBLIC_URL . 'event/pay-event.php?id=' . $eventId);
    exit;
}

$expMonth = (int)substr($expiry, 0, 2);
$expYear2 = (int)substr($expiry, 2, 2);

// Month must be 1–12
if ($expMonth < 1 || $expMonth > 12) {
    $_SESSION['error'] = 'Invalid expiry month.';
    header('Location: ' . PUBLIC_URL . 'event/pay-event.php?id=' . $eventId);
    exit;
}

// Convert YY -> 20YY (simple assumption)
$expYear = 2000 + $expYear2;

// Build expiry DateTime as last second of that month
$expiryDate = DateTime::createFromFormat('Y-m-d H:i:s', sprintf('%04d-%02d-01 23:59:59', $expYear, $expMonth));
if ($expiryDate !== false) {
    $expiryDate->modify('last day of this month 23:59:59');
    $now = new DateTime('now');
    if ($expiryDate < $now) {
        $_SESSION['error'] = 'This card is expired. Please use a different card.';
        header('Location: ' . PUBLIC_URL . 'event/pay-event.php?id=' . $eventId);
        exit;
    }
} else {
    $_SESSION['error'] = 'Invalid expiry date.';
    header('Location: ' . PUBLIC_URL . 'event/pay-event.php?id=' . $eventId);
    exit;
}

// CVV: 3–4 digits
if (!ctype_digit($cvv) || strlen($cvv) < 3 || strlen($cvv) > 4) {
    $_SESSION['error'] = 'Please enter a valid CVV.';
    header('Location: ' . PUBLIC_URL . 'event/pay-event.php?id=' . $eventId);
    exit;
}

/* -----------------------------
   Payment method validation
----------------------------- */

$validMethods = ['credit_card', 'debit'];
if (!in_array($methodRaw, $validMethods, true)) {
    $paymentMethod = 'credit_card'; // default
} else {
    $paymentMethod = $methodRaw;
}

$eventModel        = new Event();
$registrationModel = new Registration();
$paymentModel      = new Payment();
$userModel         = new User();

$event = $eventModel->findById($eventId);
if (!$event) {
    $_SESSION['error'] = 'Event not found.';
    header('Location: ' . PUBLIC_URL . 'dashboard.php');
    exit;
}

/* -----------------------------
   Prevent registration for past events
----------------------------- */

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
        // If parsing fails, block registration
        $_SESSION['error'] = 'This event is not available for registration.';
        header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
        exit;
    }
}

/* -----------------------------
   Fetch user & enforce restrictions
----------------------------- */

$user = $userModel->findById((int)$userId);
if (!$user) {
    $_SESSION['error'] = 'User not found.';
    header('Location: ' . PUBLIC_URL . 'dashboard.php');
    exit;
}

if (!userMeetsEventCondition($user, $event)) {
    $_SESSION['error'] = 'You do not meet the requirements to register for this event.';
    header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
    exit;
}

/* -----------------------------
   Fee & capacity checks
----------------------------- */

$fee = (float)($event['event_fee'] ?? 0);
if ($fee <= 0) {
    // This handler is only for paid events
    header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
    exit;
}

// Capacity check (safety)
$capacity = $event['capacity'] !== null ? (int)$event['capacity'] : null;
if ($capacity !== null) {
    $currentCount = $registrationModel->countRegistrations($eventId);
    if ($currentCount >= $capacity) {
        $_SESSION['error'] = 'This event is full.';
        header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
        exit;
    }
}

// Ensure registration exists or create it
$registration = $registrationModel->getRegistration($eventId, $userId);
if (!$registration) {
    $ok = $registrationModel->register($userId, $eventId);
    if (!$ok) {
        $_SESSION['error'] = 'Could not register you for this event.';
        header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
        exit;
    }
    $registration = $registrationModel->getRegistration($eventId, $userId);
}

$registrationId = (int)$registration['registration_id'];

// Ensure payment record exists, then mark as completed
$payment = $paymentModel->getPaymentByRegistration($registrationId);
if (!$payment) {
    $paymentModel->createPending($registrationId, $fee, $paymentMethod);
}

$paymentModel->markCompleted($registrationId);

$_SESSION['toast_message'] = 'Payment successful. You are registered for this event.';
$_SESSION['toast_type']    = 'success';

header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
exit;
