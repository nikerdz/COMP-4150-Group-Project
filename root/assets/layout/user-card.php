<?php
/**
 * Expects $user associative array with keys:
 * - user_id, first_name, last_name, faculty, level_of_study, year_of_study, gender, join_date
 *
 * Optional:
 * - $cardContext: 'explore' (default) or 'dashboard'
 * - $hiddenClass: CSS class string (e.g. 'is-hidden')
 */

$cardContext = $cardContext ?? 'explore';
$hiddenClass = $hiddenClass ?? '';

$baseClass = ($cardContext === 'dashboard') ? 'dash-card' : 'explore-card';
$classes   = trim($baseClass . ' ' . $hiddenClass);

// Safely start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loggedInUserId = $_SESSION['user_id'] ?? null;

// Name
$first = htmlspecialchars($user['first_name'] ?? 'User');
$last  = htmlspecialchars($user['last_name'] ?? '');
$full  = trim("$first $last");

// User details
$faculty = htmlspecialchars($user['faculty'] ?? '');
$level   = htmlspecialchars($user['level_of_study'] ?? '');
$year    = $user['year_of_study'] ?? null;

// Avatar letter
$initial = strtoupper(substr($first ?: $last, 0, 1));

// Avatar color logic (optional)
$genderRaw   = $user['gender'] ?? null;
$genderUpper = $genderRaw ? strtoupper($genderRaw) : null;

$avatarClass = 'usercard-avatar';
if ($genderUpper === 'F') {
    $avatarClass .= ' usercard-avatar-female';
} elseif ($genderUpper === 'M') {
    $avatarClass .= ' usercard-avatar-male';
}

// If card is for the logged-in user
$selfLabel = ($loggedInUserId && $loggedInUserId == $user['user_id']) ? 'You' : null;

?>
<article class="<?= $classes ?>">
    <div class="usercard-header">
        <div class="<?= $avatarClass ?>">
            <span><?= $initial ?></span>
        </div>

        <h3 class="usercard-name">
            <a href="<?= PUBLIC_URL . 'user/view-user.php?id=' . (int)$user['user_id'] ?>">
                <?= $full ?>
            </a>
        </h3>

        <?php if ($selfLabel): ?>
            <span class="usercard-pill usercard-pill-self"><?= $selfLabel ?></span>
        <?php elseif (!empty($user['user_type']) && $user['user_type'] === 'admin'): ?>
            <span class="usercard-pill usercard-pill-admin">Admin</span>
        <?php endif; ?>
    </div>

    <!-- Meta -->
    <p class="usercard-meta">
        <?php if ($level): ?>
            <?= ucfirst($level) ?>
        <?php endif; ?>

        <?php if ($faculty): ?>
            · <?= $faculty ?>
        <?php endif; ?>

        <?php if (!empty($year)): ?>
            · Year <?= (int)$year ?>
        <?php endif; ?>
    </p>

    <?php if (!empty($user['join_date'])): ?>
        <p class="usercard-meta-small">
            Joined <?= date('M d, Y', strtotime($user['join_date'])) ?>
        </p>
    <?php endif; ?>
</article>
