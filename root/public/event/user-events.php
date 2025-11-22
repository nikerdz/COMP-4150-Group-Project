<?php
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(MODELS_PATH . 'Registration.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'User.php');

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

$loggedInUserId = (int)$_SESSION['user_id'];

// Models
$registrationModel = new Registration();
$userModel = new User();

// --------------------------------------
// Determine whose events we are viewing
// --------------------------------------
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $targetUserId = (int)$_GET['id'];
    $isSelf = ($targetUserId === $loggedInUserId);
} else {
    $targetUserId = $loggedInUserId;
    $isSelf = true;
}

// Name used in hero/headers
if ($isSelf) {
    $displayName = htmlspecialchars($_SESSION['first_name'] ?? 'You', ENT_QUOTES, 'UTF-8');
} else {
    $targetUser = $userModel->findById($targetUserId);
    $displayName = htmlspecialchars($targetUser['first_name'] ?? 'User', ENT_QUOTES, 'UTF-8');
}

// --------------------------------------
// Fetch their registered upcoming events
// --------------------------------------
$userEvents  = $registrationModel->getUpcomingEventsForUser($targetUserId, 200);
$totalEvents = count($userEvents);

// Number of visible cards before "Load more"
$VISIBLE_COUNT = 6;

// --------------------------------------
// Split user events into upcoming vs past
// --------------------------------------
$upcomingEvents = [];
$pastEvents = [];

$now = new DateTime('now');

foreach ($userEvents as $ev) {

    // Treat no-date events as upcoming
    if (empty($ev['event_date'])) {
        $upcomingEvents[] = $ev;
        continue;
    }

    try {
        $eventDate = new DateTime($ev['event_date']);
    } catch (Exception $e) {
        $upcomingEvents[] = $ev;
        continue;
    }

    if ($eventDate >= $now) {
        $upcomingEvents[] = $ev;
    } else {
        $pastEvents[] = $ev;
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta property="og:title" content="ClubHub - User Events">
    <meta property="og:description" content="See upcoming events a user is registered for on ClubHub.">
    <meta property="og:image" content="<?php echo IMG_URL; ?>logo_hub.png">
    <meta property="og:url" content="<?php echo EVENT_URL; ?>user-events.php">
    <meta property="og:type" content="website">

    <title>ClubHub | <?= $isSelf ? "My Events" : $displayName . "'s Events" ?></title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <!-- Hero -->
    <section class="user-events-hero">
        <div class="user-events-hero-inner">
            <h1><?= $isSelf ? "Your Events" : $displayName . "'s Events" ?></h1>

            <p>
                <?php if ($isSelf): ?>
                    Here are all the upcoming events you’re registered for, <?= $displayName ?>. <br>
                <?php else: ?>
                    Here are all the upcoming events <?= $displayName ?> is registered for. <br>
                <?php endif; ?>
                Jump into an event page to see details.
            </p>
        </div>
    </section>

    <!-- Events list -->
<section class="user-events-section">
    <div class="user-events-header">
        <h2>
            <?= $isSelf ? "Events you're registered for" : $displayName . " is registered for" ?>
        </h2>
        <p>
            <?= $isSelf 
                ? "Keep track of what's coming up so you don't miss anything."
                : "These are the events $displayName plans to attend." ?>
        </p>
    </div>

    <!-- Tabs -->
    <div class="event-tabs">
        <button class="event-tab active" data-tab="upcoming">Upcoming</button>
        <button class="event-tab" data-tab="past">Past</button>
    </div>
    <br>

    <!-- UPCOMING EVENTS -->
    <div class="event-tab-content" id="tab-upcoming" style="display:block;">
        <?php if (!empty($upcomingEvents)): ?>
            <div class="user-events-grid">
                <?php foreach ($upcomingEvents as $index => $event): ?>
                    <?php 
                        $hiddenClass = ($index >= $VISIBLE_COUNT) ? 'is-hidden' : '';
                        $cardContext = 'explore'; 
                        include LAYOUT_PATH . 'event-card.php'; 
                    ?>
                <?php endforeach; ?>
            </div>

            <?php if (count($upcomingEvents) > $VISIBLE_COUNT): ?>
                <div class="user-events-load-more-wrapper">
                    <button id="userEventsLoadMoreUpcoming" class="user-events-load-more">
                        Load more events
                    </button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p class="user-events-empty">
                <?= $isSelf ? "You have no upcoming events." : "$displayName has no upcoming events." ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- PAST EVENTS -->
    <div class="event-tab-content" id="tab-past" style="display:none;">
        <?php if (!empty($pastEvents)): ?>
            <div class="user-events-grid">
                <?php foreach ($pastEvents as $event): ?>
                    <?php 
                        $cardContext = 'explore'; 
                        $hiddenClass = ''; 
                        include LAYOUT_PATH . 'event-card.php'; 
                    ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="user-events-empty">
                <?= $isSelf ? "You have no past events." : "$displayName has no past events." ?>
            </p>
        <?php endif; ?>
    </div>
</section>


</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

<script>
// Load more events
document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('userEventsGrid');
    const loadMoreBtn = document.getElementById('userEventsLoadMore');

    if (!grid || !loadMoreBtn) return;

    loadMoreBtn.addEventListener('click', () => {
        const hiddenCards = grid.querySelectorAll('.explore-card.is-hidden');
        let revealed = 0;

        hiddenCards.forEach(card => {
            if (revealed < 6) {
                card.classList.remove('is-hidden');
                revealed++;
            }
        });

        if (!grid.querySelector('.explore-card.is-hidden')) {
            loadMoreBtn.style.display = 'none';
        }
    });
});
</script>

</body>
</html>
