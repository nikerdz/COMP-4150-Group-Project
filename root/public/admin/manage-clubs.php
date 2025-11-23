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
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

if (!in_array($status, ['active', 'inactive', 'all'], true)) {
    $status = 'all';
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
    <meta property="og:type" content="website">

    <title>ClubHub Admin | Manage Clubs</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <!-- Hero -->
    <section class="admin-clubs-hero">
        <div class="admin-clubs-hero-inner">
            <h1>Manage Clubs</h1>
            <p>Search, browse, and manage campus clubs.</p>
        </div>
    </section>

    <!-- Search + Filters -->
    <section class="admin-clubs-filters-section">
        <form method="GET" class="admin-clubs-filters-form">

            <!-- Search Row -->
            <div class="admin-clubs-search-row">
                <input
                    type="text"
                    name="q"
                    class="admin-clubs-search-input"
                    placeholder="Search clubs (e.g. 'Chess Club', 'Sports')"
                    value="<?php echo htmlspecialchars($search); ?>"
                >
                <button class="admin-clubs-search-btn" type="submit">
                    Search
                </button>

                <button
                    type="button"
                    class="admin-clubs-filter-toggle"
                    id="adminClubsFilterToggle"
                >
                    Filters
                </button>
            </div>

            <!-- Collapsible status filter panel -->
            <div class="admin-clubs-filter-panel" id="adminClubsFilterPanel">
                <div class="admin-clubs-filter-group">
                    <span class="admin-clubs-filter-label">Club Status</span>

                    <div class="admin-clubs-status-options">

                        <label class="admin-clubs-status-option">
                            <input
                                type="radio"
                                name="status"
                                value="all"
                                <?php if ($status === 'all') echo 'checked'; ?>
                            >
                            <span>All</span>
                        </label>

                        <label class="admin-clubs-status-option">
                            <input
                                type="radio"
                                name="status"
                                value="active"
                                <?php if ($status === 'active') echo 'checked'; ?>
                            >
                            <span>Active</span>
                        </label>

                        <label class="admin-clubs-status-option">
                            <input
                                type="radio"
                                name="status"
                                value="inactive"
                                <?php if ($status === 'inactive') echo 'checked'; ?>
                            >
                            <span>Inactive</span>
                        </label>
                    </div>
                </div>

                <div class="admin-clubs-filter-actions">
                    <button type="submit" class="admin-clubs-apply-btn">
                        Apply
                    </button>
                    <a
                        href="<?php echo PUBLIC_URL; ?>admin/manage-clubs.php"
                        class="admin-clubs-reset-link"
                    >
                        Reset
                    </a>
                </div>
            </div>

        </form>
    </section>

    <!-- Clubs Grid -->
    <section class="admin-clubs-results-section">
        <?php if ($totalClubs === 0): ?>
            <p class="admin-clubs-empty">No clubs found.</p>
        <?php else: ?>
            <div class="admin-clubs-grid" id="adminClubsGrid">
                <?php foreach ($clubs as $i => $club): ?>
                    <?php
                        $hiddenClass  = $i >= $VISIBLE ? 'admin-clubs-card-hidden' : '';
                        $cardContext  = 'explore';
                        include LAYOUT_PATH . 'club-card.php';
                    ?>
                <?php endforeach; ?>
            </div>

            <?php if ($totalClubs > $VISIBLE): ?>
                <div class="admin-clubs-load-more-wrapper">
                    <button id="adminClubsLoadMore" class="admin-clubs-load-more">
                        Load More
                    </button>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </section>

</main>

<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
