<?php
/**
 * Expects $event associative array with keys:
 * - event_id, event_name, club_name, event_date, event_location, event_description, event_condition
 */
?>
<article class="explore-card <?php echo $hiddenClass; ?>">
    <h3>
        <a href="<?php echo PUBLIC_URL . 'event/view-event.php?id=' . (int)$event['event_id']; ?>">
            <?php echo htmlspecialchars($event['event_name']); ?>
        </a>
    </h3>

    <span class="explore-pill explore-pill-event">Event</span>

    <p class="explore-meta"><?php echo htmlspecialchars($event['club_name']); ?></p>

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
</article>
