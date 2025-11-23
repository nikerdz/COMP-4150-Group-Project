<?php
require_once('../../src/config/constants.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Membership.php');
require_once(MODELS_PATH . 'Club.php');

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to add an event.";
    // ✅ Correct login path
    header("Location: " . PUBLIC_URL . 'login.php');
    exit;
}

// Get club ID from GET
$clubId = isset($_GET['club_id']) ? (int)$_GET['club_id'] : 0;

$clubModel       = new Club();
$membershipModel = new Membership();
$eventModel      = new Event();

$club = $clubModel->findById($clubId);
if (!$club) {
    $_SESSION['error'] = "Club not found.";
    header("Location: " . PUBLIC_URL . 'clubs.php'); // fallback page
    exit;
}

// Check if user is an executive
$membership = $membershipModel->getMembership($clubId, $_SESSION['user_id']);
if (!$membership || $membership['role'] === 'member') {
    $_SESSION['error'] = "Only club executives can add events.";
    header("Location: " . PUBLIC_URL . 'club/view-club.php?id=' . $clubId);
    exit;
}

// Grab flash error (no success here – success goes to view-club)
$error = $_SESSION['error'] ?? null;

// Clear flash messages so they don't stick
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="ClubHub - Discover Your Campus Community">
    <meta property="og:description" content="Join ClubHub and explore clubs, events, and connect with fellow students on campus.">
    <meta property="og:image" content="<?php echo IMG_URL; ?>logo_hub.png">
    <meta property="og:url" content="https://khan661.myweb.cs.uwindsor.ca/COMP-4150-Group-Project/root/public/">
    <meta property="og:type" content="website"> <!-- Enhance link previews when shared on Facebook, LinkedIn, and other platforms -->
    <title>ClubHub | Add Event for <?= htmlspecialchars($club['club_name']) ?></title>
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?= time(); ?>">
</head>
<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main class="add-event-section">
    <div class="add-event-card">
        <h1>Add Event</h1>
        <p class="add-event-subtitle">
            Create a new event for your club: <strong><?= htmlspecialchars($club['club_name']) ?></strong>. <br> 
            After creation, a ClubHub Admin must approve your event before it becomes available for registration.
        </p>

        <?php if ($error): ?>
            <div class="add-event-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="<?= PHP_URL ?>event_handle_add.php" method="post" class="add-event-form">
            <input type="hidden" name="club_id" value="<?= $clubId ?>">

            <div class="auth-field">
                <label for="event_name">Event Name</label>
                <input
                    type="text"
                    id="event_name"
                    name="event_name"
                    required
                    maxlength="60"
                >
            </div>

            <div class="auth-field">
                <label for="event_description">Description</label>
                <textarea
                    id="event_description"
                    name="event_description"
                    maxlength="600"
                ></textarea>
            </div>

            <div class="auth-field">
                <label for="event_location">Location</label>
                <input type="text" id="event_location" name="event_location">
            </div>

            <div class="auth-field">
                <label for="event_date">Date &amp; Time</label>
                <input type="datetime-local" id="event_date" name="event_date" required>
            </div>

            <div class="auth-field">
                <label for="capacity">Capacity</label>
                <input type="number" id="capacity" name="capacity" min="0">
            </div>

            <div class="auth-field">
                <label for="event_condition">Condition</label>
                <select id="event_condition" name="event_condition">
                    <option value="none">None</option>
                    <option value="women_only">Women Only</option>
                    <option value="undergrad_only">Undergrad Only</option>
                    <option value="first_year_only">First Year Only</option>
                </select>
            </div>

            <div class="auth-field">
                <label for="event_fee">Event Fee</label>
                <input type="number" id="event_fee" name="event_fee" step="0.01" min="0" value="0">
            </div>

            <button type="submit" class="add-event-btn">Add Event</button>
        </form>
    </div>
</main>

<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?= JS_URL ?>script.js?v=<?= time(); ?>"></script>
</body>
</html>
