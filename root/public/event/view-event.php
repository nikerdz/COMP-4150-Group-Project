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
$userId = $_SESSION['user_id'] ?? null;

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

if ($eventId > 0) {
    $event = $eventModel->findById($eventId);
}

if ($event) {
    $pageTitle = $event['event_name'];
    $clubId    = (int)$event['club_id'];
    $clubName  = $event['club_name'];
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

    if ($userId) {
        $isRegistered = $registrationModel->isRegistered($userId, $eventId);

        // Membership & exec check (same logic as view-club)
        if ($clubId) {
            $membership = $membershipModel->getMembership($clubId, $userId);
            if ($membership) {
                $userRole = ($membership['role'] !== 'member') ? 'executive' : 'member';
                $isExec   = ($userRole === 'executive');
            }
        }
    }

    // Get registered users (with roles)
    $registeredUsers = $registrationModel->getUsersForEvent($eventId);
}

// Load comments for this event (uses Comments table)
$comments = $event ? $commentModel->getCommentsForEvent($eventId) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClubHub | <?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?= time(); ?>">
</head>
<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>
    <?php if (!$event): ?>
        <section class="club-section">
            <div class="club-section-header">
                <h2>Event Not Found</h2>
            </div>
            <p class="club-empty">The event you are looking for does not exist.</p>
        </section>
    <?php else: ?>

        <!-- Event Hero (matches club hero vibe) -->
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
                                <?php elseif ($isRegistered): ?>
                                    <span class="event-badge event-badge-registered">You are registered</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="event-actions">
                            <?php if ($userId): ?>
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
                                        <a class="event-primary-btn"
                                           href="<?= PUBLIC_URL ?>event/pay-event.php?id=<?= $eventId ?>">
                                            Pay &amp; Register
                                        </a>
                                    <?php else: ?>
                                        <form method="post"
                                              action="<?= PHP_URL ?>event_handle_register.php"
                                              style="display:inline;">
                                            <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                            <button class="event-primary-btn" type="submit">Register</button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <a class="event-primary-btn" href="<?= PUBLIC_URL ?>login.php">
                                    Log in to register
                                </a>
                            <?php endif; ?>

                            <?php if ($isExec): ?>
                                <a class="event-secondary-btn"
                                   href="<?= EVENT_URL ?>edit-event.php?id=<?= $eventId ?>">
                                    Edit Event
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

        <!-- Registrations Section (on the same page) -->
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
                                    <?= htmlspecialchars($regUser['first_name'] . ' ' . $regUser['last_name']); ?>
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
                    <p class="club-empty">
                        <a href="<?= PUBLIC_URL ?>login.php">Log in</a> to post a comment.
                    </p>
                <?php endif; ?>

                <?php if (!empty($comments)): ?>
                    <ul class="comments-list">
                        <?php foreach ($comments as $comment): ?>
                            <li class="comment-item">
                                <div class="comment-header">
                                    <span class="comment-author">
                                        <?= htmlspecialchars($comment['user_name']); ?>
                                    </span>
                                    <span class="comment-date-pill">
                                        <?php
                                        $cts = strtotime($comment['comment_date']);
                                        echo $cts
                                            ? date('M d, Y · g:i A', $cts)
                                            : htmlspecialchars($comment['comment_date']);
                                        ?>
                                    </span>
                                </div>

                                <div class="comment-bubble">
                                    <p class="comment-body">
                                        <?= nl2br(htmlspecialchars($comment['comment_message'])); ?>
                                    </p>

                                    <?php if ($userId && (int)$comment['user_id'] === (int)$userId): ?>
                                        <form method="post"
                                              action="<?= PHP_URL ?>comment_handle_delete.php"
                                              class="comment-delete-form">
                                            <input type="hidden" name="comment_id" value="<?= (int)$comment['comment_id']; ?>">
                                            <input type="hidden" name="event_id" value="<?= $eventId; ?>">
                                            <button type="submit" class="comment-delete-btn">
                                                Delete
                                            </button>
                                        </form>
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

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?= time(); ?>"></script>
</body>
</html>
