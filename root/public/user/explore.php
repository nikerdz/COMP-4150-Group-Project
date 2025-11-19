<?php
require_once('../../src/config/constants.php');
require_once(MODELS_PATH . 'Club.php');
require_once(MODELS_PATH . 'Event.php');

session_start();

// Optional: require login for explore
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

// Helpers
function prettyCondition(?string $cond): string
{
    return match ($cond) {
        'women_only'      => 'Women only',
        'undergrad_only'  => 'Undergraduates only',
        'first_year_only' => 'First years only',
        'none', null, ''  => 'Open to all',
        default           => ucfirst(str_replace('_', ' ', $cond)),
    };
}

// Models
$clubModel  = new Club();
$eventModel = new Event();

// View mode: all / clubs / events
$view = isset($_GET['view']) ? strtolower(trim($_GET['view'])) : 'all';
if (!in_array($view, ['clubs', 'events', 'all'], true)) {
    $view = 'all';
}

// Filters from query string
$search     = isset($_GET['q']) ? trim($_GET['q']) : '';
$categoryId = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;
$condition  = isset($_GET['condition']) && $_GET['condition'] !== '' ? $_GET['condition'] : null;

// Placeholder text depends on view
$searchPlaceholder = match ($view) {
    'events' => "Search events (e.g. 'Hackathon')",
    'clubs'  => "Search clubs (e.g. 'Chess Club')",
    default  => "Search clubs or events (e.g. 'Chess')",
};

// For “Load more” we’ll fetch up to this many total items
$MAX_ITEMS = 50;

// Get categories for filter dropdown
$categories = $clubModel->getAllCategories();

// Fetch clubs + events respecting view
$clubs  = [];
$events = [];

if ($view === 'all' || $view === 'clubs') {
    $clubs = $clubModel->searchClubs($search, $categoryId, $condition, $MAX_ITEMS, 0);
}

if ($view === 'all' || $view === 'events') {
    $events = $eventModel->searchEvents($search, $categoryId, $condition, $MAX_ITEMS, 0);
}

// Merge into a single list of items
$items = [];

// Tag them so we know what they are when rendering
foreach ($clubs as $c) {
    $items[] = [
        'type' => 'club',
        'data' => $c
    ];
}

foreach ($events as $e) {
    $items[] = [
        'type' => 'event',
        'data' => $e
    ];
}

// Sort by name (you can change this later)
usort($items, function ($a, $b) {
    $nameA = $a['type'] === 'club'
        ? ($a['data']['club_name'] ?? '')
        : ($a['data']['event_name'] ?? '');

    $nameB = $b['type'] === 'club'
        ? ($b['data']['club_name'] ?? '')
        : ($b['data']['event_name'] ?? '');
    
    return strcasecmp($nameA, $nameB);
});

// How many cards visible initially
$VISIBLE_COUNT = 9;
$totalItems    = count($items);
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

    <title>ClubHub | Explore</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <!-- Explore hero / heading -->
    <section class="explore-hero">
        <div class="explore-hero-inner">
            <h1>Explore Clubs &amp; Events</h1>
            <p>
                Discover new clubs, find upcoming events, and narrow things down using search and filters.
            </p>
        </div>
    </section>

    <!-- Filters + Search -->
    <section class="explore-filters-section">
        <form method="GET" class="explore-filters-form">
            <div class="explore-search-row">
                <input
                    type="text"
                    name="q"
                    class="explore-search-input"
                    placeholder="<?php echo htmlspecialchars($searchPlaceholder); ?>"
                    value="<?php echo htmlspecialchars($search); ?>"
                >

                <button type="submit" class="explore-search-btn">Search</button>

                <button
                    type="button"
                    class="explore-filter-toggle"
                    id="exploreFilterToggle"
                >
                    Filters
                </button>
            </div>

            <div class="explore-filter-panel" id="exploreFilterPanel">
                <!-- View filter: All / Clubs only / Events only -->
                <div class="explore-filter-group">
                    <span class="explore-filter-label">Showing</span>
                    <div class="explore-view-options">
                        <label class="explore-view-option">
                            <input
                                type="radio"
                                name="view"
                                value="all"
                                <?php if ($view === 'all') echo 'checked'; ?>
                            >
                            <span>All (clubs &amp; events)</span>
                        </label>

                        <label class="explore-view-option">
                            <input
                                type="radio"
                                name="view"
                                value="clubs"
                                <?php if ($view === 'clubs') echo 'checked'; ?>
                            >
                            <span>Clubs only</span>
                        </label>

                        <label class="explore-view-option">
                            <input
                                type="radio"
                                name="view"
                                value="events"
                                <?php if ($view === 'events') echo 'checked'; ?>
                            >
                            <span>Events only</span>
                        </label>
                    </div>
                </div>

                <!-- Category filter -->
                <div class="explore-filter-group">
                    <label for="category">Category</label>
                    <select name="category" id="category">
                        <option value="">Any category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option
                                value="<?php echo (int)$cat['category_id']; ?>"
                                <?php if ($categoryId === (int)$cat['category_id']) echo 'selected'; ?>
                            >
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Condition filter -->
                <div class="explore-filter-group">
                    <label for="condition">Access</label>
                    <select name="condition" id="condition">
                        <option value="">Any</option>
                        <option value="none"           <?php if ($condition === 'none')           echo 'selected'; ?>>Open to all</option>
                        <option value="undergrad_only" <?php if ($condition === 'undergrad_only') echo 'selected'; ?>>Undergrad only</option>
                        <option value="women_only"     <?php if ($condition === 'women_only')     echo 'selected'; ?>>Women only</option>
                        <option value="first_year_only"<?php if ($condition === 'first_year_only')echo 'selected'; ?>>First year only</option>
                    </select>
                </div>

                <div class="explore-filter-actions">
                    <button type="submit" class="explore-apply-btn">Apply</button>
                    <a
                        href="<?php echo USER_URL; ?>explore.php"
                        class="explore-reset-link"
                    >
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </section>

    <!-- Results grid -->
    <section class="explore-results-section">
        <?php if ($totalItems === 0): ?>
            <p class="explore-empty">
                No items match your search yet. Try adjusting your search or filters.
            </p>
        <?php else: ?>
            <div class="explore-grid" id="exploreGrid">
                <?php foreach ($items as $index => $item): ?>
                    <?php
                        $isHidden    = $index >= $VISIBLE_COUNT;
                        $type        = $item['type'];
                        $data        = $item['data'];
                        $hiddenClass = $isHidden ? 'is-hidden' : '';
                    ?>

                    <?php if ($type === 'club'): ?>
                        <?php $club = $data; include LAYOUT_PATH . 'club-card.php'; ?>
                    <?php else: ?>
                        <?php $event = $data; include LAYOUT_PATH . 'event-card.php'; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <?php if ($totalItems > $VISIBLE_COUNT): ?>
                <div class="explore-load-more-wrapper">
                    <button type="button" class="explore-load-more" id="exploreLoadMore">
                        Load more
                    </button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

<!-- Small inline script just for explore’s Load More + filter toggle -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const grid       = document.getElementById('exploreGrid');
    const loadMore   = document.getElementById('exploreLoadMore');
    const filterBtn  = document.getElementById('exploreFilterToggle');
    const filterPane = document.getElementById('exploreFilterPanel');

    if (filterBtn && filterPane) {
        filterBtn.addEventListener('click', () => {
            filterPane.classList.toggle('is-open');
        });
    }

    if (grid && loadMore) {
        loadMore.addEventListener('click', () => {
            const hidden = grid.querySelectorAll('.explore-card.is-hidden');
            let count = 0;

            hidden.forEach(card => {
                if (count < 9) {
                    card.classList.remove('is-hidden');
                    count++;
                }
            });

            if (grid.querySelectorAll('.explore-card.is-hidden').length === 0) {
                loadMore.style.display = 'none';
            }
        });
    }
});
</script>

</body>
</html>
