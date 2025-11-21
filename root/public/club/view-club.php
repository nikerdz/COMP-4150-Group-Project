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

$club = $clubModel->findById($clubId);

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
// Fetch events for this club,
// split into upcoming vs past
// --------------------------------------

// Get a reasonable batch of events, then filter by this club
$allEvents = $eventModel->searchEvents(
    null,   // no search term
    null,   // no category filter
    null,   // no condition filter
    100,    // limit
    0       // offset
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
        // If parsing fails, treat as upcoming
        $upcomingEvents[] = $ev;
        continue;
    }

    if ($eventDate >= $now) {
        $upcomingEvents[] = $ev;
    } else {
        $pastEvents[] = $ev;
    }
}
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
                        <?php if ($userRole === 'executive'): ?>
                            <a class="club-edit-btn" href="<?= CLUB_URL ?>edit-club.php?id=<?= $clubId ?>">Edit Club</a>
                        <?php elseif ($userRole === 'member'): ?>
                            <form method="post" action="<?= PHP_URL ?>club-handle-leave.php" style="display:inline;">
                                <input type="hidden" name="club_id" value="<?= $clubId ?>">
                                <button class="club-edit-save" type="submit">Leave Club</button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="<?= PHP_URL ?>club-handle-join.php" style="display:inline;">
                                <input type="hidden" name="club_id" value="<?= $clubId ?>">
                                <button class="club-edit-save" type="submit">Join Club</button>
                            </form>
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
                    <div class="member-item">
                        <span class="member-name">
                            <a href="<?= PUBLIC_URL ?>user/view-user.php?id=<?= $member['user_id'] ?>">
                                <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                            </a>
                        </span>

                        <div class="member-bubbles">
                            <span class="member-role">
                                <?= ucfirst($member['role']) ?>
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

<script src="<?php echo JS_URL; ?>script.js?v=<?= time(); ?>"></script>

<!-- Inline JS just for Events tabs -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.event-tab');
    const upcomingTabContent = document.getElementById('tab-upcoming');
    const pastTabContent     = document.getElementById('tab-past');

    if (!tabs.length || !upcomingTabContent || !pastTabContent) {
        return;
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const target = this.dataset.tab;

            // Active state on buttons
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Show / hide content
            if (target === 'past') {
                upcomingTabContent.style.display = 'none';
                pastTabContent.style.display     = 'block';
            } else {
                upcomingTabContent.style.display = 'block';
                pastTabContent.style.display     = 'none';
            }
        });
    });
});
</script>

</body>
</html>
