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

// -----------------------------
// Filters
// -----------------------------
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'active';  // active | suspended

if (!in_array($status, ['active', 'suspended'], true)) {
    $status = 'active';
}

// -----------------------------
// Fetch from DB
// -----------------------------
$users = $userModel->searchUsers($search, $status); 
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
    <meta property="og:type" content="website"> <!-- Enhance link previews when shared on Facebook, LinkedIn, and other platforms -->

    <title>ClubHub Admin | Manage Users</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <!-- Hero -->
    <section class="explore-hero">
        <div class="explore-hero-inner">
            <h1>Manage Users</h1>
            <p>Search, browse, and manage student accounts.</p>
        </div>
    </section>

    <!-- Tabs -->
    <section class="explore-filters-section">
        <form method="GET" class="explore-filters-form">

            <!-- Search Row -->
            <div class="explore-search-row">
                <input
                    type="text"
                    name="q"
                    class="explore-search-input"
                    placeholder="Search students (e.g. 'Anika', 'Computer Science')"
                    value="<?= htmlspecialchars($search) ?>"
                >
                <button class="explore-search-btn">Search</button>
            </div>

            <!-- Status Tabs -->
            <div class="manage-tabs">
                <a href="?status=active&q=<?= urlencode($search) ?>"
                   class="manage-tab <?= $status === 'active' ? 'active' : '' ?>">
                    Active Users
                </a>

                <a href="?status=suspended&q=<?= urlencode($search) ?>"
                   class="manage-tab <?= $status === 'suspended' ? 'active' : '' ?>">
                    Suspended Users
                </a>
            </div>

        </form>
    </section>

    <!-- User Grid -->
    <section class="explore-results-section">
        <?php if ($totalUsers === 0): ?>
            <p class="explore-empty">No users found.</p>
        <?php else: ?>
            <div class="explore-grid" id="usersGrid">
                <?php foreach ($users as $i => $user): ?>
                    <?php
                        $hiddenClass = $i >= $VISIBLE ? 'is-hidden' : '';
                        include LAYOUT_PATH . 'user-card.php';
                    ?>
                <?php endforeach; ?>
            </div>

            <?php if ($totalUsers > $VISIBLE): ?>
                <div class="explore-load-more-wrapper">
                    <button id="loadMoreUsers" class="explore-load-more">
                        Load More
                    </button>
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
