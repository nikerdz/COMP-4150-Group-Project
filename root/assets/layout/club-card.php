<?php
/**
 * Expects $club associative array with keys:
 * - club_id, club_name, club_description, categories, club_condition
 */
?>
<article class="explore-card <?php echo $hiddenClass; ?>">
    <h3>
        <a href="<?php echo PUBLIC_URL . 'club/view-club.php?id=' . (int)$club['club_id']; ?>">
            <?php echo htmlspecialchars($club['club_name']); ?>
        </a>
    </h3>

    <span class="explore-pill explore-pill-club">Club</span>

    <?php if (!empty($club['categories'])): ?>
        <p class="explore-meta"><?php echo htmlspecialchars($club['categories']); ?></p>
    <?php endif; ?>

    <p class="explore-text">
        <?php echo htmlspecialchars($club['club_description'] ?: 'No description has been added yet.'); ?>
    </p>

    <p class="explore-meta-small">
        Access: <?php echo htmlspecialchars(prettyCondition($club['club_condition'] ?? null)); ?>
    </p>
</article>
