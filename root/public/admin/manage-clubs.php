<?php
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(MODELS_PATH . 'Club.php');

session_start();

// --- ADMIN CHECK ---
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: " . PUBLIC_URL . "index.php");
    exit();
}

$clubModel = new Club();

// -----------------------------
// Filters
// -----------------------------
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'active';   // active | inactive

if (!in_array($status, ['active', 'inactive'], true)) {
    $status = 'active';
}

// -----------------------------
// Fetch clubs
// -----------------------------
$clubs = $clubModel->searchClubsAdmin($search, $status);
$totalClubs = count($clubs);

$VISIBLE = 12;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta property="og:title" content="ClubHub - Discover Your Campus Community">
    <meta property="og:description" content="Join ClubHub and explore clubs, events, and connect with fellow students on campus.">
    <meta property="og:image" content="<?php echo IMG_URL; ?>logo_hub.png">
    <meta property="og:url" content="https://khan661.myweb.cs.uwindsor.ca/COMP-4150-Group-Project/root/public/">
    <meta property="og:type" content="website"> <!-- Enhance link previews when shared on Facebook, LinkedIn, and other platforms -->

    <title>ClubHub | </title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <!-- Hero -->
    <section class="explore-hero">
        <div class="explore-hero-inner">
            <h1>Manage Clubs</h1>
            <p>Search, browse, and manage campus clubs.</p>
        </div>
    </section>

    <!-- Filters -->
    <section class="explore-filters-section">
        <form method="GET" class="explore-filters-form">

            <!-- Search Row -->
            <div class="explore-search-row">
                <input
                    type="text"
                    name="q"
                    class="explore-search-input"
                    placeholder="Search clubs (e.g. 'Chess Club', 'Sports')"
                    value="<?= htmlspecialchars($search) ?>"
                >
                <button class="explore-search-btn">Search</button>
            </div>

            <!-- Status Tabs -->
            <div class="manage-tabs">
                <a href="?status=active&q=<?= urlencode($search) ?>"
                   class="manage-tab <?= $status === 'active' ? 'active' : '' ?>">
                    Active Clubs
                </a>

                <a href="?status=inactive&q=<?= urlencode($search) ?>"
                   class="manage-tab <?= $status === 'inactive' ? 'active' : '' ?>">
                    Inactive Clubs
                </a>
            </div>

        </form>
    </section>

    <!-- Clubs Grid -->
    <section class="explore-results-section">
        <?php if ($totalClubs === 0): ?>
            <p class="explore-empty">No clubs found.</p>
        <?php else: ?>
            <div class="explore-grid" id="clubsGrid">
                <?php foreach ($clubs as $i => $club): ?>
                    <?php
                        $hiddenClass = $i >= $VISIBLE ? 'is-hidden' : '';
                        $cardContext = 'explore'; // use existing layout
                        include LAYOUT_PATH . 'club-card.php';
                    ?>
                <?php endforeach; ?>
            </div>

            <?php if ($totalClubs > $VISIBLE): ?>
                <div class="explore-load-more-wrapper">
                    <button id="loadMoreClubs" class="explore-load-more">Load More</button>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
