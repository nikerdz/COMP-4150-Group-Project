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

// Safely start session if not already started (prevents warnings)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(MODELS_PATH . 'Membership.php');

// Detect logged-in user automatically
$userId = $_SESSION['user_id'] ?? null;

$userRole = null;

if (!empty($userId)) {
    $membershipModel = new Membership();
    $membership = $membershipModel->getMembership($club['club_id'], $userId);

    if ($membership) {
        // exec = anything not 'member'
        $userRole = ($membership['role'] !== 'member') ? 'executive' : 'member';
    }
}

?>
<article class="<?php echo $classes; ?>">
    <h3>
        <a href="<?php echo PUBLIC_URL . 'club/view-club.php?id=' . (int)$club['club_id']; ?>">
            <?php echo htmlspecialchars($club['club_name']); ?>
        </a>
    </h3>

    <div class="explore-pill-row">
        <span class="explore-pill explore-pill-club">Club</span>

        <?php if ($userRole): ?>
            <span class="explore-role-pill explore-role-<?php echo $userRole; ?>">
                <?php echo ucfirst($userRole); ?>
            </span>
        <?php endif; ?>
    </div>

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
