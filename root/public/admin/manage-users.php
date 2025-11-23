<?php
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');
require_once(MODELS_PATH . 'User.php');

session_start();

// --- ADMIN CHECK ---
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: " . PUBLIC_URL . "index.php");
    exit();
}

$userModel = new User();
$currentAdminId = (int)$_SESSION['user_id'];

// -----------------------------
// Filters
// -----------------------------
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

if (!in_array($status, ['active', 'suspended', 'all'], true)) {
    $status = 'all';
}

// -----------------------------
// Fetch from DB
// -----------------------------
// Pass $currentAdminId so the admin does NOT see themselves in the list.
$users = $userModel->searchUsers($search, $status, $currentAdminId);
$totalUsers = count($users);

// For load more
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

    <title>ClubHub Admin | Manage Users</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <!-- Hero -->
    <section class="admin-users-hero">
        <div class="admin-users-hero-inner">
            <h1>Manage Users</h1>
            <p>Search, browse, and manage student accounts.</p>
        </div>
    </section>

    <!-- Search + Filters -->
    <section class="admin-users-filters-section">
        <form method="GET" class="admin-users-filters-form">

            <!-- Search row -->
            <div class="admin-users-search-row">
                <input
                    type="text"
                    name="q"
                    class="admin-users-search-input"
                    placeholder="Search users (e.g. 'Anika', 'Computer Science')"
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

            <!-- Collapsible filter panel -->
            <div class="admin-users-filter-panel" id="adminUsersFilterPanel">
                <div class="admin-users-filter-group">
                    <span class="admin-users-filter-label">User Status</span>
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
                                value="active"
                                <?php if ($status === 'active') echo 'checked'; ?>
                            >
                            <span>Active</span>
                        </label>

                        <label class="admin-users-status-option">
                            <input
                                type="radio"
                                name="status"
                                value="suspended"
                                <?php if ($status === 'suspended') echo 'checked'; ?>
                            >
                            <span>Suspended</span>
                        </label>
                    </div>
                </div>

                <div class="admin-users-filter-actions">
                    <button type="submit" class="admin-users-apply-btn">
                        Apply
                    </button>
                    <a
                        href="<?php echo PUBLIC_URL; ?>admin/manage-users.php"
                        class="admin-users-reset-link"
                    >
                        Reset
                    </a>
                </div>
            </div>

        </form>
    </section>

    <!-- User Grid -->
    <section class="admin-users-results-section">
        <?php if ($totalUsers === 0): ?>
            <p class="admin-users-empty">No users found.</p>
        <?php else: ?>
            <div class="admin-users-grid" id="adminUsersGrid">
                <?php foreach ($users as $i => $user): ?>
                    <?php
                        $hiddenClass = $i >= $VISIBLE ? 'admin-users-card-hidden' : '';
                        include LAYOUT_PATH . 'user-card.php';
                    ?>
                <?php endforeach; ?>
            </div>

            <?php if ($totalUsers > $VISIBLE): ?>
                <div class="admin-users-load-more-wrapper">
                    <button id="adminUsersLoadMore" class="admin-users-load-more">
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
