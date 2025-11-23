<?php
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
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

// Get logged-in user's info
$userId = (int)$_SESSION['user_id'];

// First name for greeting
if (!empty($_SESSION['first_name'])) {
    $firstName = htmlspecialchars($_SESSION['first_name'], ENT_QUOTES, 'UTF-8');
} elseif (!empty($_SESSION['user_name'])) {
    $firstName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
} else {
    $firstName = 'there';
}

$userModel         = new User();
$clubModel         = new Club();
$eventModel        = new Event();
$membershipModel   = new Membership();
$registrationModel = new Registration();

// Fetch clubs the user is a member of
$myClubs = $membershipModel->getClubsForUser($userId);

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
$interestCategoryIds = $userModel->getInterestCategoryIds($userId);

$recommendedClubs = [];

if (!empty($interestCategoryIds)) {
    $seenClubIds = [];

    foreach ($interestCategoryIds as $catId) {
        $catId = (int)$catId;
        if ($catId <= 0) {
            continue;
        }

        // Get clubs tagged with this interest category
        $clubsForCategory = $clubModel->searchClubs(
            null,    
            $catId,  
            'any',   
            50, 
            0
        );

        foreach ($clubsForCategory as $club) {
            $cid = (int)$club['club_id'];

            // Skip if user is already a member of this club
            if (isset($myClubIds[$cid])) {
                continue;
            }

            // Skip if already added this club from another category
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

// =============================
// RECENTLY VIEWED (COMBINED)
// =============================
$recentCombined = [];

if (!empty($_SESSION['recent_items']) && is_array($_SESSION['recent_items'])) {
    foreach ($_SESSION['recent_items'] as $entry) {
        if (!is_array($entry) || empty($entry['type']) || !isset($entry['id'])) {
            continue;
        }

        $id = (int)$entry['id'];
        if ($id <= 0) {
            continue;
        }

        if ($entry['type'] === 'club') {
            $club = $clubModel->findById($id);
            if ($club) {
                $recentCombined[] = [
                    'type' => 'club',
                    'data' => $club
                ];
            }
        } elseif ($entry['type'] === 'event') {
            $event = $eventModel->findById($id);
            if ($event) {
                $recentCombined[] = [
                    'type' => 'event',
                    'data' => $event
                ];
            }
        } elseif ($entry['type'] === 'user') {
            $u = $userModel->findById($id);
            if ($u) {
                $recentCombined[] = [
                    'type' => 'user',
                    'data' => $u
                ];
            }
        }
    }
}

// just in case, cap at 10
$recentCombined = array_slice($recentCombined, 0, 10);

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

    <?php if (!empty($recentCombined)): ?>
        <section class="dashboard-section">

            <div class="dashboard-section-header">
                <h2>Recently Viewed</h2>
                <p>Your most recent clubs, events, and profiles</p>
            </div>

            <div class="dashboard-carousel" data-carousel>
                <button class="carousel-btn prev" type="button">‹</button>

                <div class="dash-track-wrapper">
                    <div class="dashboard-carousel-track">
                        
                        <?php foreach ($recentCombined as $item): ?>
                            <?php
                                $cardContext = 'dashboard';
                                $hiddenClass = '';

                                if ($item['type'] === 'club') {
                                    $club = $item['data'];
                                    include LAYOUT_PATH . 'club-card.php';
                                } 
                                elseif ($item['type'] === 'event') {
                                    $event = $item['data'];
                                    include LAYOUT_PATH . 'event-card.php';
                                } 
                                elseif ($item['type'] === 'user') {
                                    $user = $item['data'];
                                    include LAYOUT_PATH . 'user-card.php';
                                }
                            ?>
                        <?php endforeach; ?>

                    </div>
                </div>

                <button class="carousel-btn next" type="button">›</button>
            </div>

        </section>
    <?php endif; ?>
        
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

<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
