<?php
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(MODELS_PATH . 'User.php');
require_once(MODELS_PATH . 'Membership.php');
require_once(MODELS_PATH . 'Registration.php');
require_once(MODELS_PATH . 'Club.php');
require_once(MODELS_PATH . 'Event.php');

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

$viewerId = (int)$_SESSION['user_id'];

// -----------------------------
// Validate user ID from URL
// -----------------------------
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid user ID.");
}

$targetUserId = (int)$_GET['id'];

// Models
$userModel = new User();
$membershipModel = new Membership();
$registrationModel = new Registration();
$clubModel = new Club();
$eventModel = new Event();

// Fetch the user being viewed
$user = $userModel->findById($targetUserId);

if (!$user) {
    echo "User not found.";
    exit();
}

// -------------------------------------------
// Track recently viewed users for dashboard
// -------------------------------------------
if (!isset($_SESSION['recent_users'])) {
    $_SESSION['recent_users'] = [];
}

$_SESSION['recent_users'] = array_unique(
    array_merge([$targetUserId], $_SESSION['recent_users'])
);
$_SESSION['recent_users'] = array_slice($_SESSION['recent_users'], 0, 6);

// Basic info formatting
$firstName = htmlspecialchars($user['first_name']);
$lastName  = htmlspecialchars($user['last_name']);
$fullName  = trim("$firstName $lastName");
$email     = htmlspecialchars($user['user_email']);

// Clubs & events they belong to
$userClubs = $membershipModel->getClubsForUser($targetUserId);
$userEvents = $registrationModel->getUpcomingEventsForUser($targetUserId, 6);

// Limit display items
$MAX_ITEMS = 3;
$displayClubs = array_slice($userClubs, 0, $MAX_ITEMS);
$displayEvents = array_slice($userEvents, 0, $MAX_ITEMS);

$hasMoreClubs = count($userClubs) > $MAX_ITEMS;
$hasMoreEvents = count($userEvents) > $MAX_ITEMS;

// Avatar initial
$initial = strtoupper(substr($firstName ?: $lastName, 0, 1));

$viewUserId = (int)$user['user_id'];

if ($viewUserId !== $_SESSION['user_id']) {

    if (!isset($_SESSION['recent_users'])) {
        $_SESSION['recent_users'] = [];
    }

    $_SESSION['recent_users'] = array_filter($_SESSION['recent_users'], function($id) use ($viewUserId) {
        return $id != $viewUserId;
    });

    array_unshift($_SESSION['recent_users'], $viewUserId);
    $_SESSION['recent_users'] = array_slice($_SESSION['recent_users'], 0, 10);
}

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

    <title>ClubHub | <?= $fullName ?></title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>
<!-- =============== PROFILE HERO =============== -->
<section class="profile-hero">
    <div class="profile-hero-inner">

        <div class="profile-avatar">
            <span><?= $initial ?></span>
        </div>

        <div class="profile-main-info">
            <h1><?= $fullName ?></h1>

            <p class="profile-meta-secondary">
                <?= $email ?>
            </p>
        </div>

    </div>
</section>

<!-- =============== USER CLUBS =============== -->
<section class="profile-section">
    <div class="profile-section-header-with-cta">
        <h2><?= $firstName ?>'s Clubs</h2>

        <?php if ($hasMoreClubs): ?>
            <a class="profile-section-cta" href="<?= CLUB_URL ?>user-clubs.php?id=<?= $targetUserId ?>">
                View All
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($displayClubs)): ?>
        <div class="profile-grid">
            <?php foreach ($displayClubs as $club): ?>
                <?php
                    $cardContext = 'explore';
                    $hiddenClass = '';
                    include LAYOUT_PATH . 'club-card.php';
                ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="profile-empty">This user is not in any clubs.</p>
    <?php endif; ?>
</section>

<!-- =============== USER EVENTS =============== -->
<section class="profile-section">
    <div class="profile-section-header-with-cta">
        <h2><?= $firstName ?>'s Upcoming Events</h2>

        <?php if ($hasMoreEvents): ?>
            <a class="profile-section-cta" href="<?= EVENT_URL ?>user-events.php?id=<?= $targetUserId ?>">
                View All
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($displayEvents)): ?>
        <div class="profile-grid">
            <?php foreach ($displayEvents as $event): ?>
                <?php
                    $cardContext = 'explore';
                    include LAYOUT_PATH . 'event-card.php';
                ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="profile-empty">This user is not registered for any events.</p>
    <?php endif; ?>
</section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
