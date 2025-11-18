<?php
require_once('../../src/config/constants.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

$firstName = isset($_SESSION['first_name'])
    ? htmlspecialchars($_SESSION['first_name'])
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

    <!-- Row 1: Your Clubs & Upcoming Events -->
    <section class="dashboard-section">
        <div class="dashboard-section-header">
            <h2>Your Clubs & Upcoming Events</h2>
            <p>Events from clubs you&rsquo;re a member of. Starred items are ones you&rsquo;re registered for.</p>
        </div>

        <div class="dashboard-carousel" data-carousel>
            <button class="carousel-btn prev" type="button" aria-label="Previous">
                ‹
            </button>

            <div class="dashboard-carousel-track">
                <!-- Filler cards for now -->
                <article class="dash-card">
                    <h3>Women in Tech – Hackathon</h3>
                    <p class="dash-tag dash-tag-registered">★ You&rsquo;re registered</p>
                    <p class="dash-meta">March 21 · 6:00 PM · Essex Hall</p>
                    <p class="dash-text">
                        A beginner-friendly evening hackathon focused on real campus challenges.
                    </p>
                </article>

                <article class="dash-card">
                    <h3>Computer Science Society – Weekly Meetup</h3>
                    <p class="dash-meta">Every Thursday · 5:30 PM · Lambton 210</p>
                    <p class="dash-text">
                        Casual hangout for CS students: project help, interview prep, and gaming.
                    </p>
                </article>

                <article class="dash-card">
                    <h3>Photography Club – Photo Walk</h3>
                    <p class="dash-meta">April 3 · 4:00 PM · Riverfront</p>
                    <p class="dash-text">
                        Explore Windsor and learn composition techniques while shooting outdoors.
                    </p>
                </article>

                <article class="dash-card">
                    <h3>Entrepreneurship Hub – Pitch Night</h3>
                    <p class="dash-tag">Club Event</p>
                    <p class="dash-meta">April 12 · 7:00 PM · EPICentre</p>
                    <p class="dash-text">
                        Watch live pitches, meet mentors, and vote for your favourite startup idea.
                    </p>
                </article>

                <article class="dash-card">
                    <h3>ACM – Coding Contest Prep</h3>
                    <p class="dash-meta">Fridays · 3:00 PM · Online</p>
                    <p class="dash-text">
                        Practice competitive programming problems with guidance from senior students.
                    </p>
                </article>
            </div>

            <button class="carousel-btn next" type="button" aria-label="Next">
                ›
            </button>
        </div>
    </section>

    <!-- Row 2: Recently Viewed Events -->
    <section class="dashboard-section">
        <div class="dashboard-section-header">
            <h2>Recently Viewed Events</h2>
            <p>Pick up where you left off and revisit events you checked out recently.</p>
        </div>

        <div class="dashboard-carousel" data-carousel>
            <button class="carousel-btn prev" type="button" aria-label="Previous">
                ‹
            </button>

            <div class="dashboard-carousel-track">
                <article class="dash-card">
                    <h3>Game Dev Night</h3>
                    <p class="dash-meta">April 5 · 7:30 PM · Makerspace</p>
                    <p class="dash-text">
                        Learn the basics of Unity and build a simple 2D game with other students.
                    </p>
                </article>

                <article class="dash-card">
                    <h3>Mental Health & Exams</h3>
                    <p class="dash-meta">March 28 · 2:00 PM · Online</p>
                    <p class="dash-text">
                        A wellness workshop on handling stress, burnout, and exam pressure.
                    </p>
                </article>

                <article class="dash-card">
                    <h3>Finance 101 for Students</h3>
                    <p class="dash-meta">April 9 · 1:00 PM · Odette</p>
                    <p class="dash-text">
                        Learn budgeting, credit scores, and how to plan for big purchases.
                    </p>
                </article>

                <article class="dash-card">
                    <h3>International Night</h3>
                    <p class="dash-meta">April 20 · 6:30 PM · CAW Centre</p>
                    <p class="dash-text">
                        Cultural performances, food, and music from student groups across campus.
                    </p>
                </article>
            </div>

            <button class="carousel-btn next" type="button" aria-label="Next">
                ›
            </button>
        </div>
    </section>

    <!-- Row 3: Recently Viewed Clubs -->
    <section class="dashboard-section">
        <div class="dashboard-section-header">
            <h2>Recently Viewed Clubs</h2>
            <p>Clubs you&rsquo;ve checked out lately. Join or revisit them anytime.</p>
        </div>

        <div class="dashboard-carousel" data-carousel>
            <button class="carousel-btn prev" type="button" aria-label="Previous">
                ‹
            </button>

            <div class="dashboard-carousel-track">
                <article class="dash-card">
                    <h3>Robotics Club</h3>
                    <p class="dash-meta">Mechatronics · Weekly builds</p>
                    <p class="dash-text">
                        Work on hands-on robotics projects and prepare for inter-university competitions.
                    </p>
                </article>

                <article class="dash-card">
                    <h3>Debate Society</h3>
                    <p class="dash-meta">Public Speaking · Competitive</p>
                    <p class="dash-text">
                        Sharpen your argument skills, meet confident speakers, and compete in tournaments.
                    </p>
                </article>

                <article class="dash-card">
                    <h3>Anime & Manga Club</h3>
                    <p class="dash-meta">Social · Weekly watch parties</p>
                    <p class="dash-text">
                        Relax, talk anime, and hang out with people who like the same shows as you.
                    </p>
                </article>

                <article class="dash-card">
                    <h3>Music Production Club</h3>
                    <p class="dash-meta">Creative · Studio sessions</p>
                    <p class="dash-text">
                        Learn mixing, beats, and production tips using accessible tools and software.
                    </p>
                </article>
            </div>

            <button class="carousel-btn next" type="button" aria-label="Next">
                ›
            </button>
        </div>
    </section>

    <!-- Row 4: Recommended for You (filler) -->
    <section class="dashboard-section">
        <div class="dashboard-section-header">
            <h2>Recommended for You</h2>
            <p>Based on your interests and faculty, these clubs and events might be a good fit.</p>
        </div>

        <div class="dashboard-carousel" data-carousel>
            <button class="carousel-btn prev" type="button" aria-label="Previous">
                ‹
            </button>

            <div class="dashboard-carousel-track">
                <article class="dash-card">
                    <h3>Data Science Study Group</h3>
                    <p class="dash-meta">CS · Weekly · Intermediate</p>
                    <p class="dash-text">
                        Work through LeetCode, Kaggle, and ML concepts with other students.
                    </p>
                </article>

                <article class="dash-card">
                    <h3>Women in STEM Mentorship</h3>
                    <p class="dash-meta">Mentorship · Multi-faculty</p>
                    <p class="dash-text">
                        Connect with upper-year students and alumni from STEM disciplines.
                    </p>
                </article>

                <article class="dash-card">
                    <h3>Campus Photography Challenge</h3>
                    <p class="dash-meta">Photo Contest · Month-long</p>
                    <p class="dash-text">
                        Submit your best campus shots and get featured on the club page.
                    </p>
                </article>

                <article class="dash-card">
                    <h3>Beginner Gym Buddy Group</h3>
                    <p class="dash-meta">Recreation · Flexible times</p>
                    <p class="dash-text">
                        Join small groups to stay accountable and learn basic gym routines.
                    </p>
                </article>
            </div>

            <button class="carousel-btn next" type="button" aria-label="Next">
                ›
            </button>
        </div>
    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
