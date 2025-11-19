<!-- for viewing approved published events and where user can register for them, or where club exec can view registrations and choose to edit the event -->
 <?php
require_once(__DIR__ . '/../../src/config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'Event.php');
session_start();

// Validate event_id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid event ID.");
}

$eventId = (int) $_GET['id'];

$eventModel = new Event();
$event = $eventModel->findById($eventId);

if (!$event) {
    $pageTitle = "Event Not Found";
} else {
    $pageTitle = $event['event_name'];
}
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

<main class="content-section container" style="padding:40px 20px;">
    <?php if (!$event): ?>
        <h1 class="not-found">Event Not Found</h1>
        <p style="text-align:center;">The event you are looking for does not exist.</p>
    <?php else: ?>
        <div class="event-view-card">

            <h1><?= htmlspecialchars($event['event_name']); ?></h1>
            <h3 class="event-club">Hosted by: <?= htmlspecialchars($event['club_name']); ?></h3>

            <?php if ($event['event_description']): ?>
                <p class="event-description"><?= nl2br(htmlspecialchars($event['event_description'])); ?></p>
            <?php endif; ?>

            <p><strong>Location:</strong> <?= htmlspecialchars($event['event_location']); ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']); ?></p>
            <p><strong>Capacity:</strong> <?= htmlspecialchars($event['capacity']); ?></p>
            <p><strong>Restrictions:</strong> <?= htmlspecialchars($event['event_condition']); ?></p>

            <?php if ($event['event_fee'] > 0): ?>
                <p><strong>Event Fee:</strong> $<?= htmlspecialchars($event['event_fee']); ?></p>
            <?php else: ?>
                <p><strong>Event Fee:</strong> Free</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?= time(); ?>"></script>
</body>
</html>
