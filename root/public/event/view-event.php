<?php
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Club.php');
require_once(MODELS_PATH . 'User.php');
require_once(MODELS_PATH . 'Registration.php');
require_once(MODELS_PATH . 'Comment.php');
require_once(MODELS_PATH . 'Membership.php');

session_start();

// Logged-in user (can be null)
$userId  = $_SESSION['user_id'] ?? null;
$isAdmin = !empty($_SESSION['is_admin']);

// Flash messages
$errorMessage = $_SESSION['error'] ?? null;
unset($_SESSION['error']);

// Validate event id
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$eventModel        = new Event();
$registrationModel = new Registration();
$membershipModel   = new Membership();
$commentModel      = new Comment();

$event     = null;
$clubId    = null;
$clubName  = '';
$pageTitle = 'Event Not Found';

// Admin sees everything; normal users only see approved events from active clubs
if ($isAdmin) {
    $event = $eventModel->findById($eventId);
} else {
    $event = $eventModel->findVisibleById($eventId);
}

if ($event) {
    $pageTitle = $event['event_name'];
    $clubId    = (int)$event['club_id'];
    $clubName  = $event['club_name'] ?? '';
}

// Registration / role info
$isRegistered     = false;
$isExec           = false;
$userRole         = null;
$participantCount = 0;
$capacity         = null;
$isPaidEvent      = false;
$registeredUsers  = [];

if ($event) {
    $participantCount = $registrationModel->countRegistrations($eventId);
    $capacity         = $event['capacity'] !== null ? (int)$event['capacity'] : null;
    $isPaidEvent      = ($event['event_fee'] ?? 0) > 0;

    if ($userId && !$isAdmin) {
        // Only relevant for normal users; admins don't register
        $isRegistered = $registrationModel->isRegistered($userId, $eventId);
    }

    // Membership & exec check (same logic as view-club) – applies to non-admin execs
    if ($clubId && $userId) {
        $membership = $membershipModel->getMembership($clubId, $userId);
        if ($membership) {
            $userRole = ($membership['role'] !== 'member') ? 'executive' : 'member';
            $isExec   = ($userRole === 'executive');
        }
    }

    // Get registered users (with roles)
    $registeredUsers = $registrationModel->getUsersForEvent($eventId);
}

// Load comments for this event (uses Comments table)
$comments = $event ? $commentModel->getCommentsForEvent($eventId) : [];

// ------------------------------------
// Track recent events (legacy per-type)
// ------------------------------------
if (!isset($_SESSION['recent_events'])) {
    $_SESSION['recent_events'] = [];
}

// Prevent duplicates and remove if exists
$_SESSION['recent_events'] = array_filter($_SESSION['recent_events'], function($id) use ($eventId) {
    return $id != $eventId;
});

// Add newest to the front
array_unshift($_SESSION['recent_events'], $eventId);

// Limit to latest 10
$_SESSION['recent_events'] = array_slice($_SESSION['recent_events'], 0, 10);

// ------------------------------------
// Unified recent items (all types)
// ------------------------------------
if (!isset($_SESSION['recent_items']) || !is_array($_SESSION['recent_items'])) {
    $_SESSION['recent_items'] = [];
}

// Remove existing entry for this event
$_SESSION['recent_items'] = array_values(array_filter(
    $_SESSION['recent_items'],
    function ($item) use ($eventId) {
        if (!is_array($item) || !isset($item['type'], $item['id'])) {
            return true;
        }
        return !($item['type'] === 'event' && (int)$item['id'] === $eventId);
    }
));

// Add newest to the front
array_unshift($_SESSION['recent_items'], [
    'type' => 'event',
    'id'   => $eventId,
]);

// Limit unified list to latest 10
$_SESSION['recent_items'] = array_slice($_SESSION['recent_items'], 0, 10);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClubHub | <?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?= time(); ?>">
    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
</head>
<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>
    <?php if (!$event): ?>
        <section class="club-section">
            <div class="club-section-header">
                <h2>Event Not Found</h2>
            </div>
            <p class="club-empty">
                The club hosting this event may have been deactivated, the event is not approved yet,
                or the event you are looking for does not exist.
            </p>
        </section>
    <?php else: ?>

        <!-- Event Hero -->
        <section class="event-hero">
            <div class="event-hero-inner">
                <div class="event-avatar">
                    <span><?= strtoupper(substr($event['event_name'], 0, 1)) ?></span>
                </div>

                <div class="event-main-info">
                    <div class="event-main-header-row">
                        <div class="event-main-text">
                            <h1><?= htmlspecialchars($event['event_name']); ?></h1>

                            <?php if (!empty($clubName)): ?>
                                <p class="explore-meta event-host-meta">
                                    Hosted by:
                                    <?php if (!empty($clubId)): ?>
                                        <a href="<?= PUBLIC_URL ?>club/view-club.php?id=<?= $clubId ?>">
                                            <?= htmlspecialchars($clubName); ?>
                                        </a>
                                    <?php else: ?>
                                        <?= htmlspecialchars($clubName); ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>

                            <?php
                            $prettyDate = '';
                            if (!empty($event['event_date'])) {
                                $ts = strtotime($event['event_date']);
                                if ($ts !== false) {
                                    $prettyDate = date('M d, Y · g:i A', $ts);
                                } else {
                                    $prettyDate = $event['event_date'];
                                }
                            }
                            ?>

                            <?php if ($prettyDate || !empty($event['event_location'])): ?>
                                <p class="event-meta-secondary">
                                    <?php if ($prettyDate): ?>
                                        <strong>Date:</strong> <?= htmlspecialchars($prettyDate); ?>
                                    <?php endif; ?>
                                    <?php if (!empty($event['event_location'])): ?>
                                        <?php if ($prettyDate): ?> · <?php endif; ?>
                                        <strong>Location:</strong> <?= htmlspecialchars($event['event_location']); ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>

                            <p class="event-meta-secondary">
                                <strong>Restrictions:</strong>
                                <?= htmlspecialchars(prettyCondition($event['event_condition'] ?? 'none')); ?>
                            </p>

                            <p class="event-meta-secondary">
                                <strong>Event Fee:</strong>
                                <?php if ($isPaidEvent): ?>
                                    $<?= htmlspecialchars(number_format((float)$event['event_fee'], 2)); ?>
                                <?php else: ?>
                                    Free
                                <?php endif; ?>
                            </p>

                            <p class="event-meta-line">
                                <span>
                                    <strong>Registrations:</strong>
                                    <?= (int)$participantCount ?>
                                    <?php if ($capacity !== null): ?>
                                        / <?= $capacity ?>
                                    <?php endif; ?>
                                </span>

                                <?php if ($capacity !== null && $participantCount >= $capacity): ?>
                                    <span class="event-badge event-badge-full">Event Full</span>
                                <?php elseif (!$isAdmin && $isRegistered): ?>
                                    <span class="event-badge event-badge-registered">You are registered</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="event-actions">
                            <?php if ($isAdmin): ?>

                                <?php if (!empty($event['event_status'])): ?>
                                    <span class="explore-pill explore-pill-status status-<?php echo htmlspecialchars($event['event_status']); ?>"> Event Status:
                                        <?php echo ucfirst($event['event_status']); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($event['event_status'] === 'pending'): ?>
                                    <form method="post"
                                          action="<?= PHP_URL ?>admin_handle_approve_event.php"
                                          style="display:inline;">
                                        <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                        <button class="event-primary-btn" type="submit">
                                            Approve Event
                                        </button>
                                    </form>
                                <?php elseif ($event['event_status'] === 'approved'): ?>
                                    <form method="post"
                                          action="<?= PHP_URL ?>admin_handle_unapprove_event.php"
                                          style="display:inline;">
                                        <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                        <button class="event-primary-btn" type="submit">
                                            Unapprove Event
                                        </button>
                                    </form>
                                <?php endif; ?>

                            <?php elseif ($userId): ?>

                                <?php if ($isRegistered): ?>
                                    <form method="post"
                                          action="<?= PHP_URL ?>event_handle_unregister.php"
                                          style="display:inline;">
                                        <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                        <button class="event-primary-btn" type="submit">
                                            <?= $isPaidEvent ? 'Refund &amp; Unregister' : 'Unregister' ?>
                                        </button>
                                    </form>
                                <?php elseif ($capacity !== null && $participantCount >= $capacity): ?>
                                    <span class="event-badge event-badge-full">Event Full</span>
                                <?php else: ?>
                                    <?php if ($isPaidEvent): ?>
                                        <form method="post"
                                              action="<?= PHP_URL ?>event_handle_register.php"
                                              style="display:inline;">
                                            <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                            <button class="event-primary-btn" type="submit">
                                                Pay &amp; Register
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="post"
                                              action="<?= PHP_URL ?>event_handle_register.php"
                                              style="display:inline;">
                                            <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                            <button class="event-primary-btn" type="submit">Register</button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ($isExec): ?>
                                    <a class="event-secondary-btn"
                                       href="<?= EVENT_URL ?>edit-event.php?id=<?= $eventId ?>">
                                        Edit Event
                                    </a>
                                <?php endif; ?>

                            <?php else: ?>
                                <a class="event-primary-btn" href="<?= PUBLIC_URL ?>login.php">
                                    Log in to register
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($errorMessage)): ?>
                <div class="club-error-message">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Event Details -->
        <section class="club-section">
            <div class="club-section-header">
                <h2>About this Event</h2>
            </div>

            <div class="event-view-card">
                <?php if (!empty($event['event_description'])): ?>
                    <p class="event-description">
                        <?= nl2br(htmlspecialchars($event['event_description'])); ?>
                    </p>
                <?php else: ?>
                    <p class="club-empty">No description provided for this event.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Registrations Section -->
        <section class="club-section">
            <div class="club-section-header">
                <h2>Registrations</h2>
            </div>

            <?php if (!empty($registeredUsers)): ?>
                <div class="event-registrations">
                    <div class="registration-list">
                        <?php foreach ($registeredUsers as $regUser): ?>
                            <?php
                            $roleRaw   = strtolower($regUser['role'] ?? 'member');
                            $roleLabel = ucfirst($roleRaw);
                            $roleClass = 'registration-role-member';

                            if ($roleRaw !== 'member') {
                                $roleClass = 'registration-role-exec';
                                if (in_array($roleRaw, ['admin', 'administrator', 'owner', 'president'], true)) {
                                    $roleClass = 'registration-role-admin';
                                }
                            }
                            ?>
                            <div class="registration-pill">
                                <span class="registration-name">
                                    <a href="<?= PUBLIC_URL ?>user/view-user.php?id=<?= (int)$regUser['user_id'] ?>">
                                        <?= htmlspecialchars($regUser['first_name'] . ' ' . $regUser['last_name']); ?>
                                    </a>
                                </span>

                                <span class="registration-role <?= $roleClass; ?>">
                                    <?= htmlspecialchars($roleLabel); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <p class="club-empty">No one has registered for this event yet.</p>
            <?php endif; ?>
        </section>

        <!-- Comments / Discussion -->
        <section class="club-section">
            <div class="club-section-header">
                <h2>Discussion</h2>
                <p>Ask questions or share your thoughts about this event.</p>
            </div>

            <div class="event-comments">
                <?php if ($userId): ?>
                    <form class="comment-form"
                          action="<?= PHP_URL ?>comment_handle_add.php"
                          method="POST">
                        <input type="hidden" name="event_id" value="<?= $eventId ?>">

                        <textarea name="comment"
                                  rows="3"
                                  placeholder="Add a comment..."
                                  required></textarea>

                        <button class="event-primary-btn comment-submit-btn" type="submit">
                            Post Comment
                        </button>
                    </form>
                <?php else: ?>
                    <p class="club-empty event-comments-login">
                        <a href="<?= PUBLIC_URL ?>login.php">Log in</a> to post a comment.
                    </p>
                <?php endif; ?>

                <?php if (!empty($comments)): ?>
                    <ul class="comments-list">
                        <?php foreach ($comments as $comment): ?>
                            <?php
                            $commentUserId = (int)$comment['user_id'];
                            $canDelete = $userId && (
                                (int)$userId === $commentUserId || $isExec
                            );
                            ?>
                            <li class="comment-item">
                                <div class="comment-card">
                                    <div class="comment-header">
                                        <a class="comment-author-link"
                                           href="<?= PUBLIC_URL ?>user/view-user.php?id=<?= $commentUserId ?>">
                                            <?= htmlspecialchars($comment['user_name']); ?>
                                        </a>
                                        <span class="comment-date-pill">
                                            <?php
                                            $cts = strtotime($comment['comment_date']);
                                            echo $cts
                                                ? date('M d, Y · g:i A', $cts)
                                                : htmlspecialchars($comment['comment_date']);
                                            ?>
                                        </span>
                                    </div>

                                    <p class="comment-body"><?= nl2br(htmlspecialchars($comment['comment_message'])); ?></p>

                                    <?php if ($canDelete): ?>
                                        <div class="comment-footer">
                                            <form method="post"
                                                  action="<?= PHP_URL ?>comment_handle_delete.php"
                                                  class="comment-delete-form">
                                                <input type="hidden" name="comment_id"
                                                       value="<?= (int)$comment['comment_id']; ?>">
                                                <input type="hidden" name="event_id"
                                                       value="<?= $eventId; ?>">
                                                <button type="submit" class="comment-delete-btn">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="club-empty">No comments yet. Be the first to comment!</p>
                <?php endif; ?>
            </div>
        </section>

    <?php endif; ?>
</main>

<?php if (!empty($_SESSION['toast_message'])): ?>
    <div class="auth-toast auth-toast-success" id="eventToast">
        <?= htmlspecialchars($_SESSION['toast_message']) ?>
    </div>
    <?php unset($_SESSION['toast_message'], $_SESSION['toast_type']); ?>
<?php endif; ?>

<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?= time(); ?>"></script>
</body>
</html>
