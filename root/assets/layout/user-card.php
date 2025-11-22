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

// If card is for the logged-in user
$selfLabel = ($loggedInUserId && $loggedInUserId == $user['user_id']) ? 'You' : null;

?>
<article class="<?= $classes ?>">
    <div class="usercard-header">

        <h3 class="usercard-name">
            <a href="<?= PUBLIC_URL . 'user/view-user.php?id=' . (int)$user['user_id'] ?>">
                <?= $full ?>
            </a>
        </h3>

        <span class="explore-pill usercard-pill-type">User</span>

        <?php if ($selfLabel): ?>
            <span class="usercard-pill usercard-pill-type"><?= $selfLabel ?></span>
        <?php elseif (!empty($user['user_type']) && $user['user_type'] === 'admin'): ?>
            <span class="usercard-pill usercard-pill-admin">Admin</span>
        <?php endif; ?>
    </div>

    <!-- Meta -->
    <p class="explore-meta">
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
        <p class="explore-meta-small">
            Joined <?= date('M d, Y', strtotime($user['join_date'])) ?>
        </p>
    <?php endif; ?>

    <?php if (!empty($_SESSION['is_admin'])): ?>
        <span class="profile-status-pill status-<?php echo htmlspecialchars($user['user_status']); ?>">
            <?php echo ucfirst($user['user_status']); ?>
        </span>
    <?php endif; ?>
</article>
