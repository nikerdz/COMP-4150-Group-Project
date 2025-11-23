<?php
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Club.php');

session_start();

// --- ADMIN CHECK ---
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: " . PUBLIC_URL . "index.php");
    exit();
}

$eventModel = new Event();

// -----------------------------
// Filters
// -----------------------------
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
if (!in_array($status, ['pending', 'approved', 'cancelled', 'all'], true)) {
    $status = 'all';
}

// -----------------------------
// Fetch Events (admin-specific search)
// -----------------------------
$events = $eventModel->searchEventsAdmin($search, $status);

$totalEvents = count($events);
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

    <title>ClubHub Admin | Manage Events</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <!-- =========================
         HERO
    ========================== -->
    <section class="admin-users-hero">
        <div class="admin-users-hero-inner">
            <h1>Manage Events</h1>
            <p>Review pending events, approve them, or manage existing ones.</p>
        </div>
    </section>

    <!-- =========================
         SEARCH + FILTERS
    ========================== -->
    <section class="admin-users-filters-section">
        <form method="GET" class="admin-users-filters-form">

            <!-- Search Row -->
            <div class="admin-users-search-row">
                <input
                    type="text"
                    name="q"
                    class="admin-users-search-input"
                    placeholder="Search events (e.g. 'Game Night', 'Chess Club')"
                    value="<?php echo htmlspecialchars($search); ?>"
                >

                <button class="admin-users-search-btn" type="submit">
                    Search
                </button>

                <button
                    type="button"
                    class="admin-users-filter-toggle"
                    id="adminUsersFilterToggle"
                >
                    Filters
                </button>
            </div>

            <!-- Filter panel -->
            <div class="admin-users-filter-panel" id="adminUsersFilterPanel">
                <div class="admin-users-filter-group">
                    <span class="admin-users-filter-label">Event Status</span>

                    <div class="admin-users-status-options">

                        <label class="admin-users-status-option">
                            <input
                                type="radio"
                                name="status"
                                value="all"
                                <?php if ($status === 'all') echo 'checked'; ?>
                            >
                            <span>All</span>
                        </label>

                        <label class="admin-users-status-option">
                            <input
                                type="radio"
                                name="status"
                                value="pending"
                                <?php if ($status === 'pending') echo 'checked'; ?>
                            >
                            <span>Pending</span>
                        </label>

                        <label class="admin-users-status-option">
                            <input
                                type="radio"
                                name="status"
                                value="approved"
                                <?php if ($status === 'approved') echo 'checked'; ?>
                            >
                            <span>Approved</span>
                        </label>

                    </div>
                </div>

                <div class="admin-users-filter-actions">
                    <button type="submit" class="admin-users-apply-btn">
                        Apply
                    </button>

                    <a
                        href="<?php echo PUBLIC_URL; ?>admin/manage-events.php"
                        class="admin-users-reset-link"
                    >
                        Reset
                    </a>
                </div>
            </div>

        </form>
    </section>

    <!-- =========================
         GRID
    ========================== -->
    <section class="admin-users-results-section">
        <?php if ($totalEvents === 0): ?>
            <p class="admin-users-empty">No events found.</p>
        <?php else: ?>
            <div class="admin-users-grid" id="adminEventsGrid">
                <?php foreach ($events as $i => $event): ?>
                    <?php
                        $hiddenClass = $i >= $VISIBLE ? 'admin-users-card-hidden' : '';
                        include LAYOUT_PATH . 'event-card.php';
                    ?>
                <?php endforeach; ?>
            </div>

            <?php if ($totalEvents > $VISIBLE): ?>
                <div class="admin-users-load-more-wrapper">
                    <button id="adminEventsLoadMore" class="admin-users-load-more">
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
