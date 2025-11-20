<?php
// viewing all events pertaining to the logged in user (registered/upcoming)
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(MODELS_PATH . 'Registration.php');
require_once(MODELS_PATH . 'Event.php');

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];

// Name for hero title (fallback to generic)
$displayName = !empty($_SESSION['first_name'])
    ? htmlspecialchars($_SESSION['first_name'], ENT_QUOTES, 'UTF-8')
    : 'you';

// Models
$registrationModel = new Registration();
// $eventModel = new Event(); // keep for future if you want, not needed right now

// Fetch ALL upcoming events this user is registered for.
// Using a higher limit here so Load More has something to work with.
$userEvents  = $registrationModel->getUpcomingEventsForUser($userId, 200);
$totalEvents = count($userEvents);

// How many cards visible initially – 2 rows of 3
$VISIBLE_COUNT = 6;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta property="og:title" content="ClubHub - My Events">
    <meta property="og:description" content="See all events you’re registered for on ClubHub.">
    <meta property="og:image" content="<?php echo IMG_URL; ?>logo_hub.png">
    <meta property="og:url" content="<?php echo EVENT_URL; ?>user-events.php">
    <meta property="og:type" content="website">

    <title>ClubHub | My Events</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <!-- Hero -->
    <section class="user-events-hero">
        <div class="user-events-hero-inner">
            <h1>Your Events</h1>
            <p>
                Here are all the upcoming events you’re registered for, <?php echo $displayName; ?>.
                Jump into an event page to see full details and club info.
            </p>
        </div>
    </section>

    <!-- Events list -->
    <section class="user-events-section">
        <div class="user-events-header">
            <h2>Events you’re registered for</h2>
            <p>Keep track of what’s coming up so you don’t miss anything.</p>
        </div>

        <?php if ($totalEvents === 0): ?>
            <p class="user-events-empty">
                You’re not registered for any upcoming events yet.
                <a href="<?php echo USER_URL; ?>explore.php?view=events">Browse events on Explore</a>
                to find something that interests you.
            </p>
        <?php else: ?>
            <div class="user-events-grid" id="userEventsGrid">
                <?php foreach ($userEvents as $index => $event): ?>
                    <?php
                        // Show first 6, hide the rest until "Load more" is clicked
                        $hiddenClass = ($index >= $VISIBLE_COUNT) ? 'is-hidden' : '';
                        // Use normal explore-style card (same as Explore & profile grids)
                        $cardContext = 'explore';
                        include LAYOUT_PATH . 'event-card.php';
                    ?>
                <?php endforeach; ?>
            </div>

            <?php if ($totalEvents > $VISIBLE_COUNT): ?>
                <div class="user-events-load-more-wrapper">
                    <button
                        type="button"
                        id="userEventsLoadMore"
                        class="user-events-load-more"
                    >
                        Load more events
                    </button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

<!-- Inline JS just for "Load more" behaviour on this page -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const grid = document.getElementById('userEventsGrid');
    const loadMoreBtn = document.getElementById('userEventsLoadMore');

    if (!grid || !loadMoreBtn) return;

    loadMoreBtn.addEventListener('click', function () {
        // Reveal up to 6 hidden cards each click (2 more rows of 3)
        const hiddenCards = grid.querySelectorAll('.explore-card.is-hidden');
        let revealed = 0;

        hiddenCards.forEach(card => {
            if (revealed < 6) {
                card.classList.remove('is-hidden');
                revealed++;
            }
        });

        // If no hidden cards remain, hide the button
        if (!grid.querySelector('.explore-card.is-hidden')) {
            loadMoreBtn.style.display = 'none';
        }
    });
});
</script>

</body>
</html>
