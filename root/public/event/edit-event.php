<?php
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Membership.php');

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

// Event ID required
$eventId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($eventId <= 0) {
    die("Invalid event ID.");
}

$eventModel       = new Event();
$membershipModel  = new Membership();

// Fetch event
$event = $eventModel->findById($eventId);
if (!$event) {
    die("Event not found.");
}

$clubId = (int)$event['club_id'];

// Check if user is exec for this club
$membership = $membershipModel->getMembership($clubId, $_SESSION['user_id'] ?? 0);
if (!$membership || $membership['role'] === "member") {
    // No permission -> back to view-event page
    header("Location: " . PUBLIC_URL . "event/view-event.php?id=" . $eventId);
    exit();
}

// Prefilled values
$nameVal      = htmlspecialchars($event['event_name']        ?? '', ENT_QUOTES, 'UTF-8');
$locationVal  = htmlspecialchars($event['event_location']    ?? '', ENT_QUOTES, 'UTF-8');
$descVal      = htmlspecialchars($event['event_description'] ?? '', ENT_QUOTES, 'UTF-8');
$conditionVal = $event['event_condition'] ?? 'none';

// Capacity (int or blank)
$capacityVal = '';
if ($event['capacity'] !== null) {
    $capacityVal = (string)(int)$event['capacity'];
}

// Fee (decimal or blank)
$feeVal = '';
if ($event['event_fee'] !== null) {
    $feeVal = number_format((float)$event['event_fee'], 2, '.', '');
}

// Date/time for <input type="datetime-local">
$eventDateInputVal = '';
if (!empty($event['event_date'])) {
    $ts = strtotime($event['event_date']);
    if ($ts !== false) {
        $eventDateInputVal = date('Y-m-d\TH:i', $ts);
    }
}

// Flash messages
$eventError   = $_SESSION['event_edit_error']   ?? null;
$eventSuccess = $_SESSION['event_edit_success'] ?? null; // not set yet, but here for future use

// Clear flash
unset($_SESSION['event_edit_error'], $_SESSION['event_edit_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ClubHub | Edit Event</title>

    <meta property="og:title" content="ClubHub - Edit Event">
    <meta property="og:description" content="Update your event details on ClubHub.">
    <meta property="og:image" content="<?php echo IMG_URL; ?>logo_hub.png">
    <meta property="og:url" content="<?php echo EVENT_URL; ?>edit-event.php?id=<?php echo $eventId; ?>">
    <meta property="og:type" content="website">

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>
<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>
    <section class="club-section event-edit-section">
        <div class="club-section-header">
            <h2>Edit Event</h2>
            <p>Update the details for <strong><?php echo $nameVal; ?></strong>.</p>
        </div>

        <?php if ($eventError): ?>
            <div class="event-message event-message-error">
                <?php echo htmlspecialchars($eventError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($eventSuccess): ?>
            <div class="event-message event-message-success">
                <?php echo htmlspecialchars($eventSuccess, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- Small hint which club this event belongs to -->
        <p class="event-meta-secondary" style="margin-bottom: 10px;">
            <strong>Club:</strong>
            <?php if (!empty($event['club_name'])): ?>
                <?php echo htmlspecialchars($event['club_name'], ENT_QUOTES, 'UTF-8'); ?>
            <?php else: ?>
                #<?php echo (int)$clubId; ?>
            <?php endif; ?>
        </p>

        <form
            class="event-edit-form"
            action="<?php echo PHP_URL; ?>event_handle_edit.php"
            method="post"
        >
            <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">

            <!-- Event Name -->
            <div class="auth-field">
                <label for="event_name">Event Name</label>
                <input
                    type="text"
                    id="event_name"
                    name="event_name"
                    value="<?php echo $nameVal; ?>"
                    required
                    maxlength="60"
                >
            </div>

            <!-- Location -->
            <div class="auth-field">
                <label for="event_location">Location</label>
                <input
                    type="text"
                    id="event_location"
                    name="event_location"
                    value="<?php echo $locationVal; ?>"
                    placeholder="e.g. CAW Centre, Room 123"
                >
            </div>

            <!-- Date & Time -->
            <div class="auth-field">
                <label for="event_date">Date &amp; Time</label>
                <input
                    type="datetime-local"
                    id="event_date"
                    name="event_date"
                    value="<?php echo $eventDateInputVal; ?>"
                >
                <small style="font-size:0.8rem; color:var(--dark-blue); opacity:0.8;">
                    Leave blank if the date is not decided yet.
                </small>
            </div>

            <!-- Capacity -->
            <div class="auth-field">
                <label for="capacity">Capacity</label>
                <input
                    type="number"
                    id="capacity"
                    name="capacity"
                    min="1"
                    value="<?php echo htmlspecialchars($capacityVal, ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Leave blank for no limit"
                >
            </div>

            <!-- Event Fee -->
            <div class="auth-field">
                <label for="event_fee">Event Fee ($)</label>
                <input
                    type="number"
                    id="event_fee"
                    name="event_fee"
                    step="0.01"
                    min="0"
                    value="<?php echo htmlspecialchars($feeVal, ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="0.00"
                >
                <small style="font-size:0.8rem; color:var(--dark-blue); opacity:0.8;">
                    Set to 0 for a free event.
                </small>
            </div>

            <!-- Description -->
            <div class="auth-field">
                <label for="event_description">Description</label>
                <textarea
                    id="event_description"
                    name="event_description"
                    rows="4"
                    maxlength="600"
                ><?php echo $descVal; ?></textarea>
            </div>

            <!-- Access Restrictions -->
            <div class="auth-field">
                <label for="event_condition">Access Restrictions</label>
                <select id="event_condition" name="event_condition">
                    <option value="none" <?php echo $conditionVal === 'none' ? 'selected' : ''; ?>>
                        None
                    </option>
                    <option value="women_only" <?php echo $conditionVal === 'women_only' ? 'selected' : ''; ?>>
                        Women Only
                    </option>
                    <option value="undergrad_only" <?php echo $conditionVal === 'undergrad_only' ? 'selected' : ''; ?>>
                        Undergraduate Students Only
                    </option>
                    <option value="first_year_only" <?php echo $conditionVal === 'first_year_only' ? 'selected' : ''; ?>>
                        First-Year Students Only
                    </option>
                </select>
            </div>

            <!-- Actions -->
            <div class="event-edit-actions">
                <a
                    href="<?php echo PUBLIC_URL; ?>event/view-event.php?id=<?php echo $eventId; ?>"
                    class="event-edit-cancel"
                >
                    Cancel
                </a>
                <button type="submit" class="event-edit-save">
                    Save Changes
                </button>
            </div>
        </form>
    </section>
</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
