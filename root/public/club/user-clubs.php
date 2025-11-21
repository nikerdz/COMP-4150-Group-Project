<?php
// Shows all clubs the logged-in user is a member of (6 at a time with "Load more")
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(MODELS_PATH . 'Membership.php');
require_once(MODELS_PATH . 'Club.php');

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

// Toast flash (delete club, errors, etc.)
$toastMessage = $_SESSION['toast_message'] ?? null;
$toastType    = $_SESSION['toast_type']    ?? null;
unset($_SESSION['toast_message'], $_SESSION['toast_type']);

$userId = (int)$_SESSION['user_id'];

// Name for hero title (fallback to generic)
$displayName = !empty($_SESSION['first_name'])
    ? htmlspecialchars($_SESSION['first_name'], ENT_QUOTES, 'UTF-8')
    : 'you';

// Models
$membershipModel = new Membership();

// Fetch ALL clubs this user is in
$userClubs  = $membershipModel->getClubsForUser($userId);
$totalClubs = count($userClubs);

// How many cards visible initially
$VISIBLE_COUNT = 6;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ClubHub | My Clubs</title>

    <meta property="og:title" content="ClubHub - My Clubs">
    <meta property="og:description" content="See all clubs you’re a member of on ClubHub.">
    <meta property="og:image" content="<?php echo IMG_URL; ?>logo_hub.png">
    <meta property="og:url" content="<?php echo CLUB_URL; ?>user-clubs.php">
    <meta property="og:type" content="website">

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <?php if ($toastMessage): ?>
        <div class="auth-toast <?php echo ($toastType === 'success') ? 'auth-toast-success' : ''; ?>">
            <?php echo htmlspecialchars($toastMessage, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <!-- Hero -->
    <section class="user-clubs-hero">
        <div class="user-clubs-hero-inner">
            <h1>Your Clubs</h1>
            <p>
                Here are all the clubs you’re a member of, <?php echo $displayName; ?>.
                Browse your list or jump into a club’s page to see events and details.
            </p>
        </div>
    </section>

    <!-- Clubs list -->
    <section class="user-clubs-section">
        <div class="user-clubs-header">
            <div class="user-clubs-header-main">
                <h2>Clubs you’ve joined</h2>
                <p>Manage and revisit the communities you’re already part of.</p>
            </div>

            <a
                href="<?php echo CLUB_URL; ?>add-club.php"
                class="user-clubs-create-btn"
            >
                Create a club
            </a>
        </div>

        <?php if ($totalClubs === 0): ?>
            <p class="user-clubs-empty">
                You’re not a member of any clubs yet.
                <a href="<?php echo USER_URL; ?>explore.php?view=clubs">Browse clubs on Explore</a>
                to find something that interests you.
            </p>
        <?php else: ?>
            <div class="user-clubs-grid" id="userClubsGrid">
                <?php foreach ($userClubs as $index => $club): ?>
                    <?php
                        // Show first 6, hide the rest until "Load more" is clicked
                        $hiddenClass = ($index >= $VISIBLE_COUNT) ? 'is-hidden' : '';
                        // Use normal explore-style card
                        $cardContext = 'explore';
                        include LAYOUT_PATH . 'club-card.php';
                    ?>
                <?php endforeach; ?>
            </div>

            <?php if ($totalClubs > $VISIBLE_COUNT): ?>
                <div class="user-clubs-load-more-wrapper">
                    <button
                        type="button"
                        id="userClubsLoadMore"
                        class="user-clubs-load-more"
                    >
                        Load more clubs
                    </button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
