<?php
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(MODELS_PATH . 'User.php');
require_once(MODELS_PATH . 'Membership.php');
require_once(MODELS_PATH . 'Registration.php');
require_once(MODELS_PATH . 'Club.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Comment.php');

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
// Track recently viewed users (per-type)
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
$faculty   = htmlspecialchars($user['faculty'] ?? 'Not set');
$level     = htmlspecialchars($user['level_of_study'] ?? 'undergraduate');
$year      = !empty($user['year_of_study']) ? (int)$user['year_of_study'] : null;
$joinDate  = !empty($user['join_date']) ? date('M j, Y', strtotime($user['join_date'])) : null;

// Clubs & events they belong to
$userClubs  = $membershipModel->getClubsForUser($targetUserId);
$userEvents = $registrationModel->getUpcomingEventsForUser($targetUserId, 6);

// Limit display items
$MAX_ITEMS     = 3;
$displayClubs  = array_slice($userClubs, 0, $MAX_ITEMS);
$displayEvents = array_slice($userEvents, 0, $MAX_ITEMS);

$hasMoreClubs  = count($userClubs) > $MAX_ITEMS;
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

// ------------------------------------
// Unified recent items (all types)
// ------------------------------------
if (!isset($_SESSION['recent_items']) || !is_array($_SESSION['recent_items'])) {
    $_SESSION['recent_items'] = [];
}

// We always track the target user as a recent item
$recentUserId = $targetUserId;

// Remove existing entry for this user
$_SESSION['recent_items'] = array_values(array_filter(
    $_SESSION['recent_items'],
    function ($item) use ($recentUserId) {
        if (!is_array($item) || !isset($item['type'], $item['id'])) {
            return true;
        }
        return !($item['type'] === 'user' && (int)$item['id'] === $recentUserId);
    }
));

// Add newest to the front
array_unshift($_SESSION['recent_items'], [
    'type' => 'user',
    'id'   => $recentUserId,
]);

// Limit unified list to latest 10
$_SESSION['recent_items'] = array_slice($_SESSION['recent_items'], 0, 10);

// Interests
$interestNames = $userModel->getInterestNames($targetUserId);

$commentModel    = new Comment();
$recentComments  = $commentModel->getCommentsForUser($targetUserId, 5);

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

                <p class="profile-meta-line">
                    <span><?php echo ucfirst($level); ?></span>
                    <?php if ($faculty !== 'Not set'): ?>
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

                <?php if (!empty($interestNames)): ?>
                    <p class="profile-interests-row">
                        <span class="profile-interests-label">Interests:</span>
                        <?php foreach ($interestNames as $name): ?>
                            <span class="profile-interest-pill">
                                <?php echo htmlspecialchars($name); ?>
                            </span>
                        <?php endforeach; ?>
                    </p>
                <?php endif; ?>
            </div>

             <div class="profile-actions">
                    <!-- SHOW ONLY IF ADMIN -->
                    <?php if (!empty($_SESSION['is_admin'])): ?>

                        <p class="profile-status-pill 
                            <?= $user['user_status'] === 'active' ? 'status-active' : 'status-suspended' ?>">
                            User Status:
                            <?= ucfirst($user['user_status']) ?>
                        </p>

                        <?php if ($user['user_status'] === 'active'): ?>
                            <form action="<?= PHP_URL ?>admin_handle_suspend_user.php" method="POST">
                                <input type="hidden" name="user_id" value="<?= $targetUserId ?>">
                                <button class="profile-edit-btn">
                                    Suspend User
                                </button>
                            </form>
                        <?php else: ?>
                            <form action="<?= PHP_URL ?>admin_handle_activate_user.php" method="POST">
                                <input type="hidden" name="user_id" value="<?= $targetUserId ?>">
                                <button class="profile-edit-btn">
                                    Activate User
                                </button>
                            </form>
                        <?php endif; ?>

                    <?php endif; ?>
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

    <!-- =============== RECENT COMMENTS =============== -->
    <section class="profile-section">
        <div class="profile-section-header-main">
            <h2>Recent Comments</h2>
        </div>

        <?php if (!empty($recentComments)): ?>
            <ul class="comments-list" id="commentsList">
                <?php foreach ($recentComments as $i => $c): ?>
                    <li class="comment-card <?= $i >= 3 ? 'is-hidden' : '' ?>">

                        <div class="comment-header">
                            <span class="comment-author-link">
                                <?= htmlspecialchars($fullName) ?>
                            </span>

                            <div class="comment-header-right">
                                <a class="comment-event-pill"
                                   href="<?= PUBLIC_URL ?>event/view-event.php?id=<?= $c['event_id'] ?>">
                                    <?= htmlspecialchars($c['event_name']) ?>
                                </a>

                                <span class="comment-date-pill">
                                    <?= date('M d, Y · g:i A', strtotime($c['comment_date'])) ?>
                                </span>
                            </div>
                        </div>

                        <!-- SAME-LINE ECHO TO AVOID BLANK FIRST LINE -->
                        <p class="comment-body"><?= nl2br(htmlspecialchars($c['comment_message'])) ?></p>

                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if (count($recentComments) > 3): ?>
                <div class="profile-section-more">
                    <button class="profile-more-btn" id="loadMoreComments">
                        Load More Comments
                    </button>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <p class="profile-empty">This user hasn’t written any comments yet.</p>
        <?php endif; ?>
    </section>

</main>

<?php if (!empty($_SESSION['toast_message'])): ?>
    <div class="auth-toast auth-toast-success" id="userToast">
        <?= htmlspecialchars($_SESSION['toast_message']); ?>
    </div>
    <?php unset($_SESSION['toast_message'], $_SESSION['toast_type']); ?>
<?php endif; ?>

<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
