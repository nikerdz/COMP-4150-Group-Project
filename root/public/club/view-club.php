<?php
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(MODELS_PATH . 'Club.php');
require_once(MODELS_PATH . 'Membership.php');
require_once(MODELS_PATH . 'Event.php');

session_start();

// Success toast after creating a club
$clubAddSuccess = $_SESSION['club_add_success'] ?? null;
if ($clubAddSuccess !== null) {
    $clubAddSuccess = htmlspecialchars($clubAddSuccess, ENT_QUOTES, 'UTF-8');
}
unset($_SESSION['club_add_success']);

$clubId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$clubModel       = new Club();
$membershipModel = new Membership();
$eventModel      = new Event();

$isAdmin = !empty($_SESSION['is_admin']);

// Admins can see all clubs (even inactive),
// regular users only see active ones.
if ($isAdmin) {
    $club = $clubModel->findById($clubId);
} else {
    $club = $clubModel->findVisibleById($clubId);
}

if (!$club) {
    echo "Club not found.";
    exit;
}

// Determine user role in this club (if logged in)
$userRole   = null;
$membership = null;

if (isset($_SESSION['user_id'])) {
    $membership = $membershipModel->getMembership($clubId, $_SESSION['user_id']);
    if ($membership) {
        // If role is anything other than 'member', treat as exec
        $userRole = ($membership['role'] !== 'member') ? 'executive' : 'member';
    }
}

// Fetch all club members
$members = $membershipModel->getClubMembers($clubId);

// --------------------------------------
// Sort members: execs first, then by first name
// --------------------------------------
if (!empty($members)) {
    usort($members, function ($a, $b) {
        $roleA = strtolower($a['role'] ?? 'member');
        $roleB = strtolower($b['role'] ?? 'member');

        $isExecA = ($roleA !== 'member');
        $isExecB = ($roleB !== 'member');

        // Execs first
        if ($isExecA !== $isExecB) {
            return $isExecA ? -1 : 1;
        }

        // Then order by first name (case-insensitive)
        $firstA = strtolower($a['first_name'] ?? '');
        $firstB = strtolower($b['first_name'] ?? '');
        if ($firstA === $firstB) {
            // Tie-break by last name if needed
            $lastA = strtolower($a['last_name'] ?? '');
            $lastB = strtolower($b['last_name'] ?? '');
            return $lastA <=> $lastB;
        }
        return $firstA <=> $firstB;
    });
}

// --------------------------------------
// Fetch events for this club,
// split into upcoming vs past
// --------------------------------------

// includeInactiveClubs = $isAdmin
$allEvents = $eventModel->searchEvents(
    null,   // no search string
    null,   // no category filter
    null,   // no condition filter
    100,    // limit
    0,      // offset
    $isAdmin // if admin, include events even if club is inactive
);

// Filter events by this club
$clubEvents = array_filter($allEvents, fn($e) => (int)$e['club_id'] === $clubId);

$upcomingEvents = [];
$pastEvents     = [];

$now = new DateTime('now');

foreach ($clubEvents as $ev) {
    if (empty($ev['event_date'])) {
        // If no date, treat as upcoming by default
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

if (!isset($_SESSION['recent_clubs'])) {
    $_SESSION['recent_clubs'] = [];
}

$clubId = (int)$club['club_id'];

$_SESSION['recent_clubs'] = array_filter($_SESSION['recent_clubs'], function($id) use ($clubId) {
    return $id != $clubId;
});

array_unshift($_SESSION['recent_clubs'], $clubId);
$_SESSION['recent_clubs'] = array_slice($_SESSION['recent_clubs'], 0, 10);

// ------------------------------------
// Unified recent items (all types)
// ------------------------------------
if (!isset($_SESSION['recent_items']) || !is_array($_SESSION['recent_items'])) {
    $_SESSION['recent_items'] = [];
}

// Remove existing entry for this club
$_SESSION['recent_items'] = array_values(array_filter(
    $_SESSION['recent_items'],
    function ($item) use ($clubId) {
        if (!is_array($item) || !isset($item['type'], $item['id'])) {
            return true;
        }
        return !($item['type'] === 'club' && (int)$item['id'] === $clubId);
    }
));

// Add newest to the front
array_unshift($_SESSION['recent_items'], [
    'type' => 'club',
    'id'   => $clubId,
]);

// Limit unified list to latest 10
$_SESSION['recent_items'] = array_slice($_SESSION['recent_items'], 0, 10);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClubHub | <?= htmlspecialchars($club['club_name']) ?></title>

    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?= time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>
    <?php if ($clubAddSuccess): ?>
        <div class="auth-toast auth-toast-success">
            <?php echo $clubAddSuccess; ?>
        </div>
    <?php endif; ?>

    <!-- Club Hero Section -->
    <section class="club-hero">
        <div class="club-hero-inner">
            <div class="club-avatar">
                <span><?= strtoupper(substr($club['club_name'], 0, 1)) ?></span>
            </div>

            <div class="club-main-info">
                <div class="club-main-header-row">
                    <div class="club-main-text">
                        <h1><?= htmlspecialchars($club['club_name']); ?></h1>

                        <p class="club-meta-secondary">
                            <b>Tags:</b>
                            <?php if (!empty($club['categories'])): ?>
                                <?php foreach (explode(',', $club['categories']) as $cat): ?>
                                    <span class="club-interest-pill"><?= htmlspecialchars($cat) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                None
                            <?php endif; ?>
                        </p>

                        <p class="club-meta-secondary">
                            <?= nl2br(htmlspecialchars($club['club_description'])); ?>
                        </p>

                        <p class="club-meta-secondary">
                            <strong>Founded:</strong> <?= htmlspecialchars($club['creation_date']); ?> ·
                            <strong>Restrictions:</strong> <?= htmlspecialchars(prettyCondition($club['club_condition'])); ?>
                        </p>

                        <?php if (!empty($club['club_email'])): ?>
                            <p class="club-meta-secondary">
                                <strong>Contact:</strong>
                                <a href="mailto:<?= htmlspecialchars($club['club_email']) ?>">
                                    <?= htmlspecialchars($club['club_email']) ?>
                                </a>
                            </p>
                        <?php endif; ?>

                        <?php if ($userRole && $membership): ?>
                            <p class="club-meta-line">
                                <span><strong>Your Role:</strong> <?= ucfirst($userRole) ?></span>
                                <span class="member-join-bubble">
                                    <strong>Joined:</strong>
                                    <?= date('M d, Y', strtotime($membership['membership_date'])) ?>
                                </span>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="club-actions">

                    <!-- If admin → show Activate/Deactivate instead of member buttons -->
                    <?php if (!empty($_SESSION['is_admin'])): ?>

                        <p class="profile-status-pill 
                            <?= $club['club_status'] === 'active' ? 'status-active' : 'status-inactive' ?>">
                            Club Status: <?= ucfirst($club['club_status']) ?>
                        </p>

                        <?php if ($club['club_status'] === 'active'): ?>
                            <form method="post" action="<?= PHP_URL ?>admin_handle_deactivate_club.php">
                                <input type="hidden" name="club_id" value="<?= $clubId ?>">
                                <button class="club-edit-btn">
                                    Deactivate Club
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="<?= PHP_URL ?>admin_handle_activate_club.php">
                                <input type="hidden" name="club_id" value="<?= $clubId ?>">
                                <button class="club-edit-btn">
                                    Activate Club
                                </button>
                            </form>
                        <?php endif; ?>

                    <?php else: ?>

                        <!-- Normal user logic -->
                        <?php if ($userRole === 'executive'): ?>
                            <a class="club-edit-btn" href="<?= CLUB_URL ?>edit-club.php?id=<?= $clubId ?>">Edit Club</a>

                        <?php elseif ($userRole === 'member'): ?>
                            <form method="post" action="<?= PHP_URL ?>club_handle_leave.php" style="display:inline;">
                                <input type="hidden" name="club_id" value="<?= $clubId ?>">
                                <button class="club-edit-save" type="submit">Leave Club</button>
                            </form>

                        <?php else: ?>
                            <form method="post" action="<?= PHP_URL ?>club_handle_join.php" style="display:inline;">
                                <input type="hidden" name="club_id" value="<?= $clubId ?>">
                                <button class="club-edit-save" type="submit">Join Club</button>
                            </form>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>

                </div>
            </div>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="club-error-message">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </section>

    <!-- Club Events Section -->
    <section class="club-section">
        <div class="club-section-header">
            <h2>Events</h2>
        </div>

        <!-- Tabs + Add Event button in one row -->
        <div class="club-events-header-row">
            <div class="event-tabs">
                <button class="event-tab active" data-tab="upcoming">Upcoming</button>
                <button class="event-tab" data-tab="past">Past</button>
            </div>

            <?php if ($userRole === 'executive'): ?>
                <a class="club-add-event-btn" href="<?= EVENT_URL ?>add-event.php?club_id=<?= $clubId ?>">
                    Add Event
                </a>
            <?php endif; ?>
        </div>

        <!-- UPCOMING TAB CONTENT -->
        <div class="event-tab-content" id="tab-upcoming" style="display:block;">
            <?php if (!empty($upcomingEvents)): ?>
                <div class="club-cards-grid">
                    <?php foreach ($upcomingEvents as $event): ?>
                        <?php include(LAYOUT_PATH . 'event-card.php'); ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="club-empty">No upcoming events.</p>
            <?php endif; ?>
        </div>

        <!-- PAST TAB CONTENT -->
        <div class="event-tab-content" id="tab-past" style="display:none;">
            <?php if (!empty($pastEvents)): ?>
                <div class="club-cards-grid">
                    <?php foreach ($pastEvents as $event): ?>
                        <?php include(LAYOUT_PATH . 'event-card.php'); ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="club-empty">No past events.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Club Members Section -->
    <section class="club-section">
        <div class="club-section-header">
            <h2>Members</h2>
        </div>

        <?php if (!empty($members)): ?>
            <div class="member-list">
                <?php foreach ($members as $member): ?>
                    <?php
                        $roleRaw   = strtolower($member['role'] ?? 'member');
                        $roleLabel = ucfirst($roleRaw);
                        $roleClass = 'member-role';
                        // Anything not "member" is treated as exec-style
                        if ($roleRaw !== 'member') {
                            $roleClass .= ' member-role-exec';
                        }
                    ?>
                    <div class="member-item">
                        <span class="member-name">
                            <a href="<?= PUBLIC_URL ?>user/view-user.php?id=<?= (int)$member['user_id'] ?>">
                                <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                            </a>
                        </span>

                        <div class="member-bubbles">
                            <span class="<?= $roleClass ?>">
                                <?= htmlspecialchars($roleLabel) ?>
                            </span>

                            <span class="member-join-bubble">
                                Joined: <?= date('M d, Y', strtotime($member['membership_date'])) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="club-empty">No members found.</p>
        <?php endif; ?>
    </section>

</main>

<?php if (!empty($_SESSION['toast_message'])): ?>
    <div class="auth-toast auth-toast-success" id="clubToast">
        <?= htmlspecialchars($_SESSION['toast_message']) ?>
    </div>
    <?php
        // Clear flash after displaying so it doesn't keep showing
        unset($_SESSION['toast_message'], $_SESSION['toast_type']);
    ?>
<?php endif; ?>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
