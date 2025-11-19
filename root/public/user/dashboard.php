<?php
require_once('../../src/config/constants.php');
require_once(MODELS_PATH . 'User.php');
require_once(MODELS_PATH . 'Club.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Membership.php');
require_once(MODELS_PATH . 'Registration.php');

session_start();

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

// Helper to pretty-print conditions (used in cards/layouts)
function prettyCondition(?string $cond): string
{
    return match ($cond) {
        'women_only'      => 'Women only',
        'undergrad_only'  => 'Undergraduates only',
        'first_year_only' => 'First years only',
        'none', null, ''  => 'Open to all',
        default           => ucfirst(str_replace('_', ' ', $cond)),
    };
}

// Get logged-in user's info
$userId = (int)$_SESSION['user_id'];

// First name for greeting – supports both session keys
if (!empty($_SESSION['first_name'])) {
    $firstName = htmlspecialchars($_SESSION['first_name'], ENT_QUOTES, 'UTF-8');
} elseif (!empty($_SESSION['user_name'])) {
    $firstName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
} else {
    $firstName = 'there';
}

// Instantiate models
$userModel        = new User();
$clubModel        = new Club();
$eventModel       = new Event();
$membershipModel  = new Membership();
$registrationModel = new Registration();

// Fetch clubs the user is a member of
$myClubs = $membershipModel->getClubsForUser($userId);

// Build a quick lookup of club IDs the user already belongs to
$myClubIds = [];
foreach ($myClubs as $myClub) {
    if (isset($myClub['club_id'])) {
        $myClubIds[(int)$myClub['club_id']] = true;
    }
}

// Fetch upcoming events user is registered for
$upcomingEvents = $registrationModel->getUpcomingEventsForUser($userId, 6);

// ------------------------------
// Recommended clubs by interests
// ------------------------------

// Get the user's interest category IDs from User_Interests
$interestCategoryIds = $userModel->getInterestCategoryIds($userId); // returns array of category_id

$recommendedClubs = [];

if (!empty($interestCategoryIds)) {
    $seenClubIds = [];

    foreach ($interestCategoryIds as $catId) {
        $catId = (int)$catId;
        if ($catId <= 0) {
            continue;
        }

        // Get clubs tagged with this interest category
        // searchClubs(search, categoryId, condition, limit, offset)
        $clubsForCategory = $clubModel->searchClubs(
            null,        // no text search
            $catId,      // this category
            'any',       // ignore condition filter
            50,          // up to 50 per category (you can tweak this)
            0
        );

        foreach ($clubsForCategory as $club) {
            $cid = (int)$club['club_id'];

            // Skip if user is already a member
            if (isset($myClubIds[$cid])) {
                continue;
            }

            // Skip if we've already added this club from another category
            if (isset($seenClubIds[$cid])) {
                continue;
            }

            $seenClubIds[$cid] = true;
            $recommendedClubs[] = $club;
        }
    }
}

// Limit to 6 recommended clubs overall
$recommendedClubs = array_slice($recommendedClubs, 0, 6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta property="og:title" content="ClubHub - Discover Your Campus Community">
    <meta property="og:description" content="Join ClubHub and explore clubs, events, and connect with fellow students on campus.">
    <meta property="og:image" content="<?php echo IMG_URL; ?>logo_hub.png">
    <meta property="og:url" content="<?php echo PUBLIC_URL; ?>">
    <meta property="og:type" content="website">

    <title>ClubHub | Dashboard</title>
    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <!-- Hero -->
    <section class="dashboard-hero">
        <div class="dashboard-hero-inner">
            <h1>Welcome back, <?php echo $firstName; ?></h1>
            <p>
                Here’s a quick overview of your clubs and events.
                Pick up right where you left off.
            </p>
        </div>
    </section>

    <!-- Quick Links -->
    <section class="dashboard-quicklinks">
        <div class="dashboard-quicklinks-inner">

            <?php 
            $quickLinks = [
                ['url' => CLUB_URL . 'user-clubs.php',   'img' => 'btn/club.png',       'label' => 'My Clubs'],
                ['url' => EVENT_URL . 'user-events.php', 'img' => 'btn/event.png',      'label' => 'My Events'],
                ['url' => USER_URL . 'explore.php',      'img' => 'btn/explorebtn.png', 'label' => 'Explore'],
                ['url' => USER_URL . 'profile.php',      'img' => 'btn/profile.png',    'label' => 'My Profile'],
                ['url' => USER_URL . 'settings.php',     'img' => 'btn/settings.png',   'label' => 'Settings'],
            ];
            foreach ($quickLinks as $link): ?>
                <div class="dashboard-quicklink">
                    <a href="<?php echo $link['url']; ?>" class="quicklink-icon">
                        <img src="<?php echo IMG_URL . $link['img']; ?>" alt="<?php echo $link['label']; ?>">
                    </a>
                    <span class="quicklink-label"><?php echo $link['label']; ?></span>
                </div>
            <?php endforeach; ?>

        </div>
    </section>

    <!-- Recommended Clubs -->
    <section class="dashboard-section">
        <div class="dashboard-section-header">
            <h2>Recommended for You</h2>
            <p>Based on your interests, these clubs might be a good fit.</p>
        </div>

        <div class="dashboard-carousel" data-carousel>
            <button
                class="carousel-btn prev"
                type="button"
                aria-label="Previous"
            >‹</button>

            <div class="dash-track-wrapper">
                <div class="dashboard-carousel-track">
                    <?php if (!empty($recommendedClubs)): ?>
                        <?php foreach ($recommendedClubs as $club): ?>
                            <?php
                                // Use dash-card style inside the dashboard carousel
                                $cardContext = 'dashboard';
                                $hiddenClass = '';
                                include(LAYOUT_PATH . 'club-card.php');
                            ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="explore-empty-text">
                            <?php if (empty($interestCategoryIds)): ?>
                                No recommendations yet. Try adding some interests on your profile first.
                            <?php else: ?>
                                No clubs match your current interests yet. Check back soon or explore all clubs.
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <button
                class="carousel-btn next"
                type="button"
                aria-label="Next"
            >›</button>
        </div>
    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
