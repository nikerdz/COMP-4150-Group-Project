<?php
// For the logged in user to view their profile and related info
require_once('../../src/config/constants.php');
require_once(MODELS_PATH . 'User.php');
require_once(MODELS_PATH . 'Club.php');
require_once(MODELS_PATH . 'Event.php');

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];

$userModel  = new User();
$clubModel  = new Club();
$eventModel = new Event();

// Get user from DB
$user = $userModel->findById($userId);

// If somehow user not found, force logout
if (!$user) {
    session_unset();
    session_destroy();
    header("Location: " . PUBLIC_URL . "login.php?error=Account not found. Please log in again.");
    exit();
}

// Keep first name in session for other pages (dashboard, etc.)
$_SESSION['first_name'] = $user['first_name'] ?? '';

// Profile display values
$firstName = htmlspecialchars($user['first_name'] ?? '');
$lastName  = htmlspecialchars($user['last_name'] ?? '');
$fullName  = trim($firstName . ' ' . $lastName);
$email     = htmlspecialchars($user['user_email'] ?? '');
$faculty   = htmlspecialchars($user['faculty'] ?? 'Not set');
$level     = htmlspecialchars($user['level_of_study'] ?? 'undergraduate');
$year      = !empty($user['year_of_study']) ? (int)$user['year_of_study'] : null;
$joinDate  = !empty($user['join_date']) ? date('M j, Y', strtotime($user['join_date'])) : null;

// First initial for avatar
$initial = strtoupper(substr($firstName !== '' ? $firstName : ($lastName ?? 'U'), 0, 1));

// Get upcoming events user registered for
$upcomingEvents = $eventModel->getUpcomingEventsForUser($userId, 6);

// Get clubs user is a member of
$userClubs = $clubModel->getClubsForUser($userId);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta property="og:title" content="ClubHub - My Profile">
    <meta property="og:description" content="View your ClubHub profile, upcoming events, and clubs.">
    <meta property="og:image" content="<?php echo IMG_URL; ?>logo_hub.png">
    <meta property="og:url" content="https://khan661.myweb.cs.uwindsor.ca/COMP-4150-Group-Project/root/public/user/profile.php">
    <meta property="og:type" content="website">

    <title>ClubHub | My Profile</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <!-- Profile Hero -->
    <section class="profile-hero">
        <div class="profile-hero-inner">
            <div class="profile-avatar">
                <span><?php echo htmlspecialchars($initial); ?></span>
            </div>

            <div class="profile-main-info">
                <div class="profile-main-header-row">
                    <div class="profile-main-text">
                        <h1><?php echo $fullName !== '' ? $fullName : 'Student'; ?></h1>

                        <p class="profile-meta-line">
                            <span><?php echo ucfirst($level); ?></span>
                            <?php if ($faculty && $faculty !== 'Not set'): ?>
                                <span><?php echo $faculty; ?></span>
                            <?php endif; ?>
                            <?php if ($year): ?>
                                <span>Year <?php echo $year; ?></span>
                            <?php endif; ?>
                        </p>

                        <p class="profile-meta-secondary">
                            <span><?php echo $email; ?></span>
                            <?php if ($joinDate): ?>
                                <span>· Joined <?php echo $joinDate; ?></span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="profile-actions">
                        <a class="profile-edit-btn" href="<?php echo USER_URL; ?>edit-profile.php">
                            Edit profile
                        </a>
                        <a class="profile-settings-btn" href="<?php echo USER_URL; ?>settings.php">
                            Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Upcoming Events section -->
    <section class="profile-section">
        <div class="profile-section-header">
            <h2>Upcoming Events</h2>
            <p>Events you’re registered for. Don’t miss out.</p>
        </div>

        <?php if (!empty($upcomingEvents)): ?>
            <div class="profile-grid">
                <?php foreach ($upcomingEvents as $event): ?>
                    <?php
                        $eventName  = htmlspecialchars($event['event_name']);
                        $clubName   = htmlspecialchars($event['club_name'] ?? '');
                        $location   = htmlspecialchars($event['event_location'] ?? 'TBA');
                        $eventDate  = !empty($event['event_date'])
                            ? date('M j, Y · g:i A', strtotime($event['event_date']))
                            : 'Date TBA';
                        $fee        = isset($event['event_fee']) ? (float)$event['event_fee'] : 0.0;
                    ?>
                    <article class="profile-card">
                        <h3><?php echo $eventName; ?></h3>
                        <?php if ($clubName): ?>
                            <p class="profile-card-meta"><?php echo $clubName; ?></p>
                        <?php endif; ?>
                        <p class="profile-card-meta"><?php echo $eventDate; ?> · <?php echo $location; ?></p>

                        <?php if ($fee > 0): ?>
                            <p class="profile-card-tag">Paid event · $<?php echo number_format($fee, 2); ?></p>
                        <?php else: ?>
                            <p class="profile-card-tag profile-card-tag-free">Free event</p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="profile-empty">
                You aren’t registered for any upcoming events yet.
                <a href="<?php echo USER_URL; ?>explore.php">Explore events</a>
            </p>
        <?php endif; ?>
    </section>

    <!-- Clubs section -->
    <section class="profile-section">
        <div class="profile-section-header">
            <h2>Your Clubs</h2>
            <p>Clubs you’re a member of on campus.</p>
        </div>

        <?php if (!empty($userClubs)): ?>
            <div class="profile-grid">
                <?php foreach ($userClubs as $club): ?>
                    <?php
                        $clubName       = htmlspecialchars($club['club_name']);
                        $clubDesc       = htmlspecialchars($club['club_description'] ?? 'No description yet.');
                        $membershipDate = !empty($club['membership_date'])
                            ? date('M j, Y', strtotime($club['membership_date']))
                            : null;
                        $categories     = !empty($club['categories'])
                            ? explode(',', $club['categories'])
                            : [];
                    ?>
                    <article class="profile-card">
                        <h3><?php echo $clubName; ?></h3>

                        <?php if (!empty($categories)): ?>
                            <p class="profile-card-tag">
                                <?php echo htmlspecialchars(implode(' · ', $categories)); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ($membershipDate): ?>
                            <p class="profile-card-meta">Member since <?php echo $membershipDate; ?></p>
                        <?php endif; ?>

                        <p class="profile-card-text">
                            <?php echo $clubDesc; ?>
                        </p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="profile-empty">
                You’re not a member of any clubs yet.
                <a href="<?php echo USER_URL; ?>explore.php">Find a club to join</a>
            </p>
        <?php endif; ?>
    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
