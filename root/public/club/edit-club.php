<?php
require_once('../../src/config/constants.php');
require_once(MODELS_PATH . 'Club.php');
require_once(MODELS_PATH . 'Membership.php');

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

// Club ID required
$clubId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($clubId <= 0) {
    die("Invalid club ID.");
}

$clubModel       = new Club();
$membershipModel = new Membership();

// Fetch club
$club = $clubModel->findById($clubId);
if (!$club) {
    die("Club not found.");
}

// Check if user is exec
$membership = $membershipModel->getMembership($clubId, $_SESSION['user_id']);
if (!$membership || $membership['role'] === "member") {
    header("Location: " . PUBLIC_URL . "club/view-club.php?id=" . $clubId);
    exit();
}

// Prefilled values
$nameVal        = htmlspecialchars($club['club_name']        ?? '', ENT_QUOTES, 'UTF-8');
$emailVal       = htmlspecialchars($club['club_email']       ?? '', ENT_QUOTES, 'UTF-8');
$descVal        = htmlspecialchars($club['club_description'] ?? '', ENT_QUOTES, 'UTF-8');
$conditionVal   = $club['club_condition'] ?? 'none';

// All categories
$allCategories  = $clubModel->getAllCategories();

// Selected tag IDs
$selectedCatIds = $clubModel->getClubCategoryIds($clubId);

$clubError   = $_SESSION['club_edit_error'] ?? null;
$clubSuccess = $_SESSION['club_edit_success'] ?? null;

// Clear flash
unset($_SESSION['club_edit_error'], $_SESSION['club_edit_success']);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClubHub | Edit Club</title>
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>
<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>
    <section class="club-section club-edit-section">
        <div class="club-section-header">
            <h2>Edit Club</h2>
            <p>Update your club’s profile and settings.</p>
        </div>

        <?php if ($clubError): ?>
            <div class="club-message club-message-error"><?= htmlspecialchars($clubError) ?></div>
        <?php endif; ?>

        <?php if ($clubSuccess): ?>
            <div class="club-message club-message-success"><?= htmlspecialchars($clubSuccess) ?></div>
        <?php endif; ?>

        <form class="club-edit-form" action="<?= PHP_URL ?>club-handle-edit.php" method="post">
            <input type="hidden" name="club_id" value="<?= $clubId ?>">

            <!-- Club Name -->
            <div class="auth-field">
                <label for="club_name">Club Name</label>
                <input type="text" id="club_name" name="club_name" value="<?= $nameVal ?>" required>
            </div>

            <!-- Contact Email -->
            <div class="auth-field">
                <label for="club_email">Club Email</label>
                <input type="email" id="club_email" name="club_email" value="<?= $emailVal ?>">
            </div>

            <!-- Description -->
            <div class="auth-field">
                <label for="club_description">Description</label>
                <textarea id="club_description" name="club_description" rows="4"><?= $descVal ?></textarea>
            </div>

            <!-- Categories -->
            <div class="form-group club-edit-interests">
                <label class="club-interests-title">Categories / Tags</label>
                <div class="club-interests-grid">
                    <?php foreach ($allCategories as $cat): ?>
                        <?php $isChecked = in_array($cat['category_id'], $selectedCatIds); ?>
                        <label class="club-interest-chip">
                            <input type="checkbox" name="categories[]" value="<?= $cat['category_id'] ?>" <?= $isChecked ? 'checked' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Access Restrictions -->
            <div class="auth-field">
                <label for="club_condition">Access Restrictions</label>
                <select id="club_condition" name="club_condition">
                    <option value="none"           <?= $conditionVal === 'none' ? 'selected' : '' ?>>None</option>
                    <option value="women_only"     <?= $conditionVal === 'women_only' ? 'selected' : '' ?>>Women Only</option>
                    <option value="undergrad_only"  <?= $conditionVal === 'undergrad_only' ? 'selected' : '' ?>>Undergraduate Students Only</option>
                    <option value="first_year_only"     <?= $conditionVal === 'first_year_only' ? 'selected' : '' ?>>First-Year Students Only</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="club-edit-actions">
                <a href="<?= PUBLIC_URL ?>club/view-club.php?id=<?= $clubId ?>" class="club-edit-cancel">Cancel</a>
                <button type="submit" class="club-edit-save">Save Changes</button>
            </div>
        </form>
    </section>
</main>

<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

</body>
</html>
