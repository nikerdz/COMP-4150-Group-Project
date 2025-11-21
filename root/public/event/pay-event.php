<?php
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Registration.php');

session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: ' . PUBLIC_URL . 'login.php');
    exit;
}

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$eventModel        = new Event();
$registrationModel = new Registration();

if ($eventId <= 0) {
    $_SESSION['error'] = 'Invalid event.';
    header('Location: ' . PUBLIC_URL . 'dashboard.php');
    exit;
}

$event = $eventModel->findById($eventId);
if (!$event) {
    $_SESSION['error'] = 'Event not found.';
    header('Location: ' . PUBLIC_URL . 'dashboard.php');
    exit;
}

$fee = (float)($event['event_fee'] ?? 0);
if ($fee <= 0) {
    // This page is only for paid events
    header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
    exit;
}

// Already registered? Just send them back.
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

$pageTitle    = 'Pay for ' . $event['event_name'];
$errorMessage = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClubHub | <?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?= time(); ?>">
</head>
<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>
    <section class="club-section">
        <div class="club-section-header">
            <h2>Pay &amp; Register</h2>
            <p>Complete your payment to secure your spot for this event.</p>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div class="club-error-message">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <div class="event-view-card">
            <h3><?= htmlspecialchars($event['event_name']); ?></h3>

            <p class="event-meta-secondary">
                <strong>Fee:</strong>
                $<?= htmlspecialchars(number_format($fee, 2)); ?>
            </p>

            <?php
            $prettyDate = '';
            if (!empty($event['event_date'])) {
                $ts = strtotime($event['event_date']);
                if ($ts !== false) {
                    $prettyDate = date('M d, Y · g:i A', $ts);
                } else {
                    $prettyDate = $event['event_date'];
                }
            }
            ?>
            <?php if ($prettyDate || !empty($event['event_location'])): ?>
                <p class="event-meta-secondary">
                    <?php if ($prettyDate): ?>
                        <strong>Date:</strong> <?= htmlspecialchars($prettyDate); ?>
                    <?php endif; ?>
                    <?php if (!empty($event['event_location'])): ?>
                        <?php if ($prettyDate): ?> · <?php endif; ?>
                        <strong>Location:</strong> <?= htmlspecialchars($event['event_location']); ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>

            <form action="<?= PHP_URL ?>event_handle_pay.php" method="POST" class="auth-form" style="margin-top:16px;">
                <input type="hidden" name="event_id" value="<?= $eventId ?>">

                <div class="auth-field">
                    <label for="payment_method">Payment Method</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="credit_card">Credit</option>
                        <option value="debit">Debit</option>
                    </select>
                </div>

                <!-- Card info (digits only in UI) -->
                <div class="auth-field">
                    <label for="card_number">Card Number</label>
                    <input
                        type="text"
                        id="card_number"
                        name="card_number"
                        placeholder="1234567890123456"
                        inputmode="numeric"
                        pattern="\d{13,19}"
                        maxlength="19"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                        required
                    >
                </div>

                <div class="auth-field" style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label for="expiry">Expiry (MMYY)</label>
                        <input
                            type="text"
                            id="expiry"
                            name="expiry"
                            placeholder="MMYY"
                            inputmode="numeric"
                            pattern="\d{4}"
                            maxlength="4"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                            required
                        >
                    </div>
                    <div style="flex:1;">
                        <label for="cvv">CVV</label>
                        <input
                            type="text"
                            id="cvv"
                            name="cvv"
                            placeholder="123"
                            inputmode="numeric"
                            pattern="\d{3,4}"
                            maxlength="4"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="event-primary-btn" style="margin-top:12px;">
                    Confirm Payment
                </button>
            </form>
        </div>
    </section>
</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?= time(); ?>"></script>
</body>
</html>
