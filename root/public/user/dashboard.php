<?php
require_once('../../src/config/constants.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

// Use the first name that was stored in session at login
$firstName = isset($_SESSION['user_name'])
    ? htmlspecialchars($_SESSION['user_name'])
    : 'there';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ClubHub | Dashboard</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <!-- Hero / Welcome Section -->
    <section class="dashboard-hero">
        <div class="dashboard-hero-inner">
            <h1>Welcome back, <?php echo $firstName; ?></h1>
            <p>
                Here&rsquo;s a quick overview of your clubs, upcoming events, and recent activity.<br>
                Jump back into what matters most on campus.
            </p>
        </div>
    </section>

    <!-- Quick Links Section -->
    <section class="dashboard-quicklinks">
        <div class="dashboard-quicklinks-inner">
            <div class="quicklinks-grid">

                <!-- My Clubs -->
                <a
                    href="<?php echo CLUB_URL; ?>user-clubs.php"
                    class="quicklink-item"
                >
                    <img
                        src="<?php echo IMG_URL; ?>btn/club.png"
                        alt="My Clubs"
                        class="quicklink-img"
                    >
                    <span class="quicklink-label">My Clubs</span>
                </a>

                <!-- My Events -->
                <a
                    href="<?php echo EVENT_URL; ?>user-events.php"
                    class="quicklink-item"
                >
                    <img
                        src="<?php echo IMG_URL; ?>btn/event.png"
                        alt="My Events"
                        class="quicklink-img"
                    >
                    <span class="quicklink-label">My Events</span>
                </a>

                <!-- Explore -->
                <a
                    href="<?php echo USER_URL; ?>explore.php"
                    class="quicklink-item"
                >
                    <img
                        src="<?php echo IMG_URL; ?>btn/explorebtn.png"
                        alt="Explore"
                        class="quicklink-img"
                    >
                    <span class="quicklink-label">Explore</span>
                </a>

                <!-- My Profile -->
                <a
                    href="<?php echo USER_URL; ?>profile.php"
                    class="quicklink-item"
                >
                    <img
                        src="<?php echo IMG_URL; ?>btn/profile.png"
                        alt="My Profile"
                        class="quicklink-img"
                    >
                    <span class="quicklink-label">My Profile</span>
                </a>

                <!-- Settings -->
                <a
                    href="<?php echo USER_URL; ?>settings.php"
                    class="quicklink-item"
                >
                    <img
                        src="<?php echo IMG_URL; ?>btn/settings.png"
                        alt="Settings"
                        class="quicklink-img"
                    >
                    <span class="quicklink-label">Settings</span>
                </a>

            </div>
        </div>
    </section>

    <!-- Row 1: Your Clubs & Upcoming Events -->
    <section class="dashboard-section">
        <div class="dashboard-section-header">
            <h2>Your Clubs & Upcoming Events</h2>
            <p>Events from clubs you&rsquo;re a member of. Starred items are ones you&rsquo;re registered for.</p>
        </div>

        <div class="dashboard-carousel" data-carousel>
            <button class="carousel-btn prev" type="button" aria-label="Previous">‹</button>

            <div class="dash-track-wrapper">
                <div class="dashboard-carousel-track">

                    <article class="dash-card">
                        <h3>Women in Tech – Hackathon</h3>
                        <p class="dash-tag dash-tag-registered">★ You&rsquo;re registered</p>
                        <p class="dash-meta">March 21 · 6:00 PM · Essex Hall</p>
                        <p class="dash-text">
                            A beginner-friendly evening hackathon focused on real campus challenges.
                        </p>
                    </article>

                </div>
            </div>

            <button class="carousel-btn next" type="button" aria-label="Next">›</button>
        </div>
    </section>

    <!-- Row 2: Recommended for You -->
    <section class="dashboard-section">
        <div class="dashboard-section-header">
            <h2>Recommended for You</h2>
            <p>Based on your interests and faculty, these clubs and events might be a good fit.</p>
        </div>

        <div class="dashboard-carousel" data-carousel>
            <button class="carousel-btn prev" type="button" aria-label="Previous">‹</button>

            <div class="dash-track-wrapper">
                <div class="dashboard-carousel-track">

                    <article class="dash-card">
                        <h3>Data Science Study Group</h3>
                        <p class="dash-meta">CS · Weekly · Intermediate</p>
                        <p class="dash-text">
                            Work through LeetCode, Kaggle, and ML concepts with other students.
                        </p>
                    </article>
                    
                </div>
            </div>

            <button class="carousel-btn next" type="button" aria-label="Next">›</button>
        </div>
    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
