<?php
require_once('../src/config/constants.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ClubHub | About</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <!-- Reuse same hero style as home  -->
    <div class="hero-section">
        <div class="hero-container">
            <h1>About ClubHub</h1>
            <p>
                ClubHub is a centralized platform that brings together campus clubs, events,
                and students in one place. Instead of chasing posters and group chats,
                students can discover clubs, explore events, and manage memberships from a
                single, easy-to-use site.
            </p>
        </div>
    </div>

    <!-- About content cards -->
    <section class="about-features">
        <div class="about-container">
            <div class="about-feature">
                <h2>Our Mission</h2>
                <p>
                    Our mission is to make campus life easier to navigate and more inclusive.
                    ClubHub helps students quickly find communities that match their interests,
                    program, and schedule, whether they’re new to campus or already involved.
                </p>
            </div>

            <div class="about-feature">
                <h2>How It Works</h2>
                <p>
                    Students create a profile, set their interests, and browse a personalized
                    list of clubs and events. They can register, pay fees when required, and
                    receive reminders or updates if event details change.
                </p>
            </div>

            <div class="about-feature">
                <h2>For Clubs</h2>
                <p>
                    Club executives use ClubHub to manage their club profile, create events,
                    tag them by category, track registrations and payments, and send updates
                    to members—all from one dashboard.
                </p>
            </div>

            <div class="about-feature">
                <h2>For Students</h2>
                <p>
                    Students can browse clubs by category, filter events by date or type, and
                    see what fits their interests, faculty, and year of study. From one
                    account, they manage memberships, track upcoming events, and stay informed
                    with notifications.
                </p>
            </div>
        </div>
    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>