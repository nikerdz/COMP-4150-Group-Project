<?php
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(MODELS_PATH . 'User.php');
require_once(MODELS_PATH . 'Club.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Registration.php');
require_once(MODELS_PATH . 'Payment.php');

session_start();

// --- ADMIN CHECK ---
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: " . PUBLIC_URL . "index.php");
    exit();
}

$firstName = htmlspecialchars($_SESSION['first_name'] ?? 'Admin', ENT_QUOTES);

// Models
$userModel   = new User();
$clubModel   = new Club();
$eventModel  = new Event();
$regModel    = new Registration();
$paymentModel = new Payment();

// Stats (you can refine these)
$totalUsers     = count($userModel->getAllUsers());
$totalClubs     = count($clubModel->searchClubs(null, null, 'any', 9999, 0));
$totalEvents    = count($eventModel->searchEvents(null, null, 'any', 9999, 0));
$totalPaid      = $paymentModel->countCompletedPayments();
$totalRevenue   = $paymentModel->getTotalRevenue();

// =============================
// RECENTLY VIEWED (COMBINED)
// =============================
$recentCombined = [];

// --- Clubs ---
if (!empty($_SESSION['recent_clubs'])) {
    foreach ($_SESSION['recent_clubs'] as $cid) {
        $club = $clubModel->findById((int)$cid);
        if ($club) {
            $recentCombined[] = [
                'type' => 'club',
                'data' => $club
            ];
        }
    }
}

// --- Events ---
if (!empty($_SESSION['recent_events'])) {
    foreach ($_SESSION['recent_events'] as $eid) {
        $event = $eventModel->findById((int)$eid);
        if ($event) {
            $recentCombined[] = [
                'type' => 'event',
                'data' => $event
            ];
        }
    }
}

// --- Users ---
if (!empty($_SESSION['recent_users'])) {
    foreach ($_SESSION['recent_users'] as $uid) {
        $u = $userModel->findById((int)$uid);
        if ($u) {
            $recentCombined[] = [
                'type' => 'user',
                'data' => $u
            ];
        }
    }
}

// Reverse list to newest first (since you unshifted)
$recentCombined = array_reverse($recentCombined);

// Limit to 10 items
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

    <title>ClubHub | Admin Dashboard</title>
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
                Welcome to the admin dashboard.<br> Use the quick links below to manage clubs, events, and users.
            </p>
        </div>
    </section>

    <!-- Quick Links -->
    <section class="dashboard-quicklinks">
        <div class="dashboard-quicklinks-inner">

            <?php 
            $quickLinks = [
                ['url' => ADMIN_URL . 'manage-clubs.php',   'img' => 'btn/club.png',       'label' => 'Manage Clubs'],
                ['url' => ADMIN_URL . 'manage-events.php', 'img' => 'btn/event.png',      'label' => 'Manage Events'],
                ['url' => ADMIN_URL . 'manage-users.php',  'img' => 'btn/people.png', 'label' => 'Manage Users'],
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
                                    include(LAYOUT_PATH . 'club-card.php');
                                } 
                                elseif ($item['type'] === 'event') {
                                    $event = $item['data'];
                                    include(LAYOUT_PATH . 'event-card.php');
                                } 
                                elseif ($item['type'] === 'user') {
                                    $user = $item['data'];
                                    include(LAYOUT_PATH . 'user-card.php');
                                }
                            ?>
                        <?php endforeach; ?>

                    </div>
                </div>

                <button class="carousel-btn next" type="button">›</button>
            </div>
        </section>
    <?php endif; ?>

      <!-- Admin Stats -->
    <section class="dashboard-section">
        <div class="dashboard-section-header">
            <h2>Platform Overview</h2>
            <p>Your system statistics at a glance.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $totalUsers; ?></h3>
                <p>Total Users</p>
            </div>

            <div class="stat-card">
                <h3><?php echo $totalClubs; ?></h3>
                <p>Total Clubs</p>
            </div>

            <div class="stat-card">
                <h3><?php echo $totalEvents; ?></h3>
                <p>Total Events</p>
            </div>

            <div class="stat-card">
                <h3><?php echo $totalPaid; ?></h3>
                <p>Completed Payments</p>
            </div>

            <div class="stat-card">
                <h3>$<?php echo number_format($totalRevenue, 2); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>
    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>

