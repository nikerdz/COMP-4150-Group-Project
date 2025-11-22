<?php
/**
 * Expects $event associative array with keys:
 * - event_id, event_name, club_name, event_date, event_location, event_description, event_condition
 *
 * Optional:
 * - $cardContext: 'explore' (default) or 'dashboard'
 * - $hiddenClass: extra CSS class string (e.g. 'is-hidden'), optional
 */

// Defaults so we don't get "undefined variable" notices
$cardContext = $cardContext ?? 'explore';
$hiddenClass = $hiddenClass ?? '';

// Decide base class based on context
$baseClass = ($cardContext === 'dashboard') ? 'dash-card' : 'explore-card';

// Build the final class string
$classes = $baseClass;
if (!empty($hiddenClass)) {
    $classes .= ' ' . $hiddenClass;
}

// Club helper vars
$clubName = $event['club_name'] ?? '';
$clubId   = isset($event['club_id']) ? (int)$event['club_id'] : null;
?>
<article class="<?php echo $classes; ?>">
    <h3>
        <a href="<?php echo PUBLIC_URL . 'event/view-event.php?id=' . (int)$event['event_id']; ?>">
            <?php echo htmlspecialchars($event['event_name']); ?>
        </a>
    </h3>

    <span class="explore-pill explore-pill-event">Event</span>

    <?php if (!empty($clubName)): ?>
        <p class="explore-meta">
            <?php if (!empty($clubId)): ?>
                <a href="<?php echo PUBLIC_URL . 'club/view-club.php?id=' . $clubId; ?>">
                    <?php echo htmlspecialchars($clubName); ?>
                </a>
            <?php else: ?>
                <?php echo htmlspecialchars($clubName); ?>
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <p class="explore-meta-small">
        <?php if (!empty($event['event_date'])): ?>
            <?php echo htmlspecialchars(date('M j, Y · g:i A', strtotime($event['event_date']))); ?>
        <?php endif; ?>
        <?php if (!empty($event['event_location'])): ?>
            · <?php echo htmlspecialchars($event['event_location']); ?>
        <?php endif; ?>
    </p>

    <p class="explore-text">
        <?php echo htmlspecialchars($event['event_description'] ?: 'No description has been added yet.'); ?>
    </p>

    <p class="explore-meta-small">
        Access: <?php echo htmlspecialchars(prettyCondition($event['event_condition'] ?? null)); ?>
    </p>

    <?php if (!empty($_SESSION['is_admin'])): ?>
        <?php if (!empty($event['event_status'])): ?>
            <span class="explore-pill explore-pill-status status-<?php echo htmlspecialchars($event['event_status']); ?>">
                <?php echo ucfirst($event['event_status']); ?>
            </span>
        <?php endif; ?>
    <?php endif; ?>

</article>
