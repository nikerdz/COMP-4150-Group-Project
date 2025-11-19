<?php
require_once('../../src/config/constants.php');
require_once(MODELS_PATH . 'User.php');
require_once(MODELS_PATH . 'Club.php');
require_once(MODELS_PATH . 'Event.php');

session_start();

// Redirect if not logged in
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
$clubModel  = new Club();
$eventModel = new Event();

// Fetch clubs the user is a member of
$myClubs = $clubModel->getClubsForUser($userId);

// Fetch upcoming events the user is registered for
$upcomingEvents = $eventModel->getUpcomingEventsForUser($userId);

// Fetch recommended clubs (example: all active clubs the user isn't in)
$allClubs = $clubModel->searchClubs(null, null, 'any', 10); // get 10 clubs

$recommendedClubs = array_filter($allClubs, function ($club) use ($myClubs) {
    foreach ($myClubs as $myClub) {
        if ($club['club_id'] == $myClub['club_id']) {
            return false; // exclude already joined
        }
    }
    return true;
});

// Limit to 6 recommended clubs
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
    <meta property="og:url" content="https://khan661.myweb.cs.uwindsor.ca/COMP-4150-Group-Project/root/public/">
    <meta property="og:type" content="website">

    <title>ClubHub | Dashboard</title>
    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>
<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <!-- Hero / Welcome Section -->
    <section class="dashboard-hero">
        <div class="dashboard-hero-inner">
            <h1>Welcome back, <?php echo $firstName; ?></h1>
            <p>
                Here&rsquo;s a quick overview of your clubs, upcoming events, and recent activity.<br>
                Jump back into what matters most on campus.
            </p>
        </div>
    </section>

    <!-- Quick Links Section -->
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
                        <img src="<?php echo IMG_URL . $link['img']; ?>" alt="<?php echo htmlspecialchars($link['label']); ?>">
                    </a>
                    <span class="quicklink-label"><?php echo htmlspecialchars($link['label']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Row 1: Your Clubs & Upcoming Events -->
    <section class="dashboard-section">
        <div class="dashboard-section-header">
            <h2>Upcoming Events</h2>
            <p>Events from clubs you&rsquo;re a member of. Starred items are ones you&rsquo;re registered for.</p>
        </div>

        <div class="dashboard-carousel" data-carousel>
            <button
                class="carousel-btn prev"
                type="button"
                aria-label="Previous"
            >‹</button>

            <div class="dash-track-wrapper">
                <div class="dashboard-carousel-track">
                    <?php if (!empty($upcomingEvents)): ?>
                        <?php foreach ($upcomingEvents as $event): ?>
                            <?php
                                // Use dash-card style inside the dashboard carousel
                                $cardContext = 'dashboard';
                                $hiddenClass = '';
                                include(LAYOUT_PATH . 'event-card.php');
                            ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="explore-empty-text">No upcoming events from your clubs.</p>
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

    <!-- Row 2: Recommended for You -->
    <section class="dashboard-section">
        <div class="dashboard-section-header">
            <h2>Recommended for You</h2>
            <p>Based on your interests and faculty, these clubs might be a good fit.</p>
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
                        <p class="explore-empty-text">No recommendations at this time.</p>
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
