<?php
/**
 * Expects $club associative array with keys:
 * - club_id, club_name, club_description, categories, club_condition
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
?>
<article class="<?php echo $classes; ?>">
    <h3>
        <a href="<?php echo PUBLIC_URL . 'club/view-club.php?id=' . (int)$club['club_id']; ?>">
            <?php echo htmlspecialchars($club['club_name']); ?>
        </a>
    </h3>

    <span class="explore-pill explore-pill-club">Club</span>

    <?php if (!empty($club['categories'])): ?>
        <p class="explore-meta">
            <?php echo htmlspecialchars($club['categories']); ?>
        </p>
    <?php endif; ?>

    <p class="explore-text">
        <?php echo htmlspecialchars($club['club_description'] ?: 'No description has been added yet.'); ?>
    </p>

    <p class="explore-meta-small">
        Access: <?php echo htmlspecialchars(prettyCondition($club['club_condition'] ?? null)); ?>
    </p>
</article>
