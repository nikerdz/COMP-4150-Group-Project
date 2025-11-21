<?php
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Club.php');
require_once(MODELS_PATH . 'User.php'); // for user role and registration status
require_once(MODELS_PATH . 'Registration.php'); // for checking registrations
require_once(MODELS_PATH . 'Comment.php'); // for event comments
require_once(MODELS_PATH . 'Payment.php');
require_once(MODELS_PATH . 'Membership.php');

session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$eventId = (int)$_GET['id'];
$userId = $_SESSION['user_id'];
$eventModel = new Event();
$clubModel = new Club();
$registrationModel = new Registration();
$paymentModel = new Payment();

// Club helper vars
$clubName = $event['club_name'] ?? '';
$clubId   = isset($event['club_id']) ? (int)$event['club_id'] : null;

// Validate event_id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid event ID.");
}

$event = $eventModel->findById($eventId);
if (!$event) {
    $pageTitle = "Event Not Found";
} else {
    $pageTitle = $event['event_name'];
    $clubId   = (int)$event['club_id'];
    $clubName = $event['club_name'];
}

$userId = $_SESSION['user_id'] ?? null;

// --------------------------------------------------
// USER ROLE + EVENT REGISTRATION STATUS
// --------------------------------------------------
$isRegistered = false;
$registrationRecord = null;
$isExec = false; // <--- IMPORTANT, define empty

if ($userId) {
    $registrationRecord = $registrationModel->getRegistration($eventId, $userId);
    $isRegistered = !empty($registrationRecord);

    // Fetch membership role in this club
    $membershipModel = new Membership();
    $membership = $membershipModel->getMembership($event['club_id'], $userId);

    if ($membership) {
        $userRole = ($membership['role'] !== 'member') ? 'executive' : 'member';
        $isExec = ($userRole === 'executive'); // <--- SAME AS view-club
    } else {
        $userRole = null;
    }
} else {
    $userRole = null;
}


// --------------------------------------------------
// CURRENT PARTICIPANT COUNT
// --------------------------------------------------
$participantCount = $registrationModel->countRegistrations($eventId);
$capacity = $event['capacity'] ?? null;

// Fee logic
$isPaidEvent = ($event['event_fee'] > 0);

// --------------------------------------------------
// HANDLE REGISTER / UNREGISTER
// --------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {

    if (isset($_POST['register'])) {

        if (!$isRegistered) {
            $regId = $registrationModel->register($userId, $eventId);

            // For paid events, create a pending payment THEN show window
            if ($isPaidEvent) {
                $paymentModel->createPending($regId, $event['event_fee']);
                header("Location: pay.php?reg_id=" . $regId);
                exit;
            }
        }

        // Free event => reload page
        header("Location: view-event.php?id=" . $eventId);
        exit;
    }

    if (isset($_POST['unregister']) && $isRegistered) {
        // Remove payment record first if exists
        $paymentModel->deletePaymentByRegistration($registrationRecord['registration_id']);
        $registrationModel->unregister($eventId, $userId);

        header("Location: view-event.php?id=" . $eventId);
        exit;
    }
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
            <h3 class="event-club">
                Hosted by: 
                <a href="<?php echo PUBLIC_URL . 'club/view-club.php?id=' . $clubId; ?>">
                    <?= htmlspecialchars($event['club_name']); ?>
                </a>
            </h3>

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

            <!-- Registration status pill -->
            <?php if ($isRegistered): ?>
                <span class="pill registered">Registered</span>
            <?php endif; ?>

            <!-- User actions -->
            <div class="event-actions">
                <?php if ($isRegistered): ?>
                    <form action="<?= PHP_URL ?>event_handle_deregister.php" method="POST" style="display:inline;">
                        <input type="hidden" name="event_id" value="<?= $eventId ?>">
                        <button type="submit">Deregister</button>
                    </form>
                <?php else: ?>
                    <form action="<?= PHP_URL ?>event_handle_register.php"  method="POST" style="display:inline;">
                        <input type="hidden" name="event_id" value="<?= $eventId ?>">
                        <button type="submit">
                            <?= $event['event_fee'] > 0 ? 'Pay & Register' : 'Register' ?>
                        </button>
                    </form>
                <?php endif; ?>

                <!-- Exec options -->
                <?php if ($isExec): ?>
                    <a href="<?= EVENT_URL ?>edit-event.php?id=<?= $eventId ?>"><button>Edit Event</button></a>
                    <a href="/events/view-registrations.php?id=<?= $eventId ?>"><button>View Registrations</button></a>
                <?php endif; ?>
            </div>

        </div>

        <!-- Comments section -->
        <div class="comments-section">
            <h3>Comments</h3>
            <form action="<?= PHP_URL ?>comment_handle_add.php" method="POST">
                <input type="hidden" name="event_id" value="<?= $eventId ?>">
                <textarea name="comment" rows="3" placeholder="Add a comment..." required></textarea>
                <button type="submit">Post Comment</button>
            </form>

            <?php if (!empty($comments)): ?>
                <ul class="comments-list">
                    <?php foreach ($comments as $comment): ?>
                        <li>
                            <strong><?= htmlspecialchars($comment['user_name']); ?>:</strong>
                            <?= nl2br(htmlspecialchars($comment['comment_text'])); ?>
                            <span class="comment-date"><?= htmlspecialchars($comment['created_at']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No comments yet.</p>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?= time(); ?>"></script>
</body>
</html>
