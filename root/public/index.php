<?php
require_once('../src/config/constants.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClubHub | Home</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">

    <!-- Styles & Scripts -->
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
    <script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>
</head>
<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>
    <div class="hero-section">
        <h1>Welcome to ClubHub!</h1>
        <p>Connect, explore, and share your club experiences.</p>
        <br>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <a href="<?php echo ADMIN_URL; ?>dashboard.php" class="btn">Admin Dashboard</a>
            <?php else: ?>
                <a href="<?php echo USER_URL; ?>dashboard.php" class="btn">My Dashboard</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="register.php" class="btn">Get Started</a>
        <?php endif; ?>
    </div>

    <section class="features">
        <div class="feature">
            <h2>Organize Your Clubs</h2>
            <p>Create, manage, and keep track of your university or community clubs easily.</p>
            <img src="<?php echo IMG_URL; ?>clubs_graphic.png" alt="Clubs" width="200">
        </div>

        <div class="feature">
            <h2>Connect & Collaborate</h2>
            <p>Join clubs, find members, and share ideas with others.</p>
            <img src="<?php echo IMG_URL; ?>connect_graphic.png" alt="People connecting" width="200">
        </div>

        <div class="feature">
            <h2>Discover Events</h2>
            <p>Browse and attend events hosted by clubs in your community.</p>
            <img src="<?php echo IMG_URL; ?>events_graphic.png" alt="Event poster" width="200">
        </div>
    </section>
</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>

</body>
</html>
