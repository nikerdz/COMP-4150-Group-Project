<!-- where logged in users can view the clubs profile and where club execs can edit the profile and view members 
 itll list the club exec(s), description, upcoming events, past events, and members  - with buttons to join/leave club
-->
<?php
require_once(__DIR__ . '/../../src/config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'Club.php');
session_start();

// Validate club_id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid club ID.");
}

$clubId = (int) $_GET['id'];

$clubModel = new Club();
$club = $clubModel->findById($clubId);

if (!$club) {
    $pageTitle = "Club Not Found";
} else {
    $pageTitle = $club['club_name'];
}
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

<main class="content-section container" style="padding: 40px 20px;">
    <?php if (!$club): ?>
        <h1 class="not-found">Club Not Found</h1>
        <p style="text-align:center;">The club you are looking for does not exist.</p>
    <?php else: ?>
        <div class="club-view-card">
            <h1><?= htmlspecialchars($club['club_name']); ?></h1>

            <p class="club-categories">
                <strong>Categories:</strong>
                <?= $club['categories'] ? htmlspecialchars($club['categories']) : "None" ?>
            </p>

            <?php if ($club['club_email']): ?>
                <p><strong>Email:</strong> <?= htmlspecialchars($club['club_email']); ?></p>
            <?php endif; ?>

            <p class="club-description">
                <?= nl2br(htmlspecialchars($club['club_description'])); ?>
            </p>

            <p><strong>Founded:</strong> <?= htmlspecialchars($club['creation_date']); ?></p>
            <p><strong>Restrictions:</strong> <?= htmlspecialchars($club['club_condition']); ?></p>
        </div>
    <?php endif; ?>
</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?= time(); ?>"></script>
</body>
</html>
