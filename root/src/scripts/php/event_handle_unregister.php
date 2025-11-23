<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'Registration.php');
require_once(MODELS_PATH . 'Payment.php');

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

$registrationModel = new Registration();
$paymentModel      = new Payment();

// Find registration record
$registration = $registrationModel->getRegistration($eventId, $userId);
if (!$registration) {
    $_SESSION['error'] = 'You are not registered for this event.';
    header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
    exit;
}

$registrationId = (int)$registration['registration_id'];

// Check if there is a payment for this registration
$payment = $paymentModel->getPaymentByRegistration($registrationId);

if ($payment && (float)($payment['amount'] ?? 0) > 0) {
    // Mark payment as refunded
    $paymentModel->refund($registrationId);
    $message = 'You have been unregistered from this paid event. You will receive a refund within a few business days.';
} else {
    $message = 'You have been unregistered from this event.';
}

// Remove registration and this will also delete Payment
$registrationModel->unregister($userId, $eventId);

$_SESSION['toast_message'] = $message;
$_SESSION['toast_type']    = 'success';

header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
exit;
