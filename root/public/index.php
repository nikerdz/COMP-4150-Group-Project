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
</head>
<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>
<main>
    <div class="hero-section">
        <div class="hero-container">
            <h1>Welcome to ClubHub!</h1>
            <p>Discover, explore, and connect your campus experiences.</p>
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
    </div>

<section class="features">
    <div class="features-container">
        <div class="feature">
            <h2>Discover</h2>
            <p>Find clubs that match your interests and uncover new communities to join.</p>
            <img src="<?php echo IMG_URL; ?>discover.png" alt="discover" width="200">
        </div>

        <div class="feature">
            <h2>Explore</h2>
            <p>Browse upcoming events and stay updated on what’s happening around campus.</p>
            <img src="<?php echo IMG_URL; ?>explore.png" alt="explore" width="200">
        </div>

        <div class="feature">
            <h2>Connect</h2>
            <p>Meet new people, collaborate with others, and strengthen your community ties.</p>
            <img src="<?php echo IMG_URL; ?>connect.png" alt="connect" width="200">
        </div>
    </section>
</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>
<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
