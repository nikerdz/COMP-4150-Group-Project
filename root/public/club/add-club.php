<?php
// where logged in users can choose to become a club exec by adding a club
require_once('../../src/config/constants.php');
require_once('../../src/config/utils.php');

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

// Pull old form values + errors from session (flash)
$errors   = $_SESSION['add_club_errors'] ?? [];
$oldInput = $_SESSION['add_club_old']    ?? [];

// Clear flash after reading
unset($_SESSION['add_club_errors'], $_SESSION['add_club_old']);

// Form field values
$clubName        = $oldInput['club_name']        ?? '';
$clubEmail       = $oldInput['club_email']       ?? '';
$clubDescription = $oldInput['club_description'] ?? '';
$clubCondition   = $oldInput['club_condition']   ?? 'none';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta property="og:title" content="ClubHub - Create a Club">
    <meta property="og:description" content="Create a new club on ClubHub and become a club executive.">
    <meta property="og:image" content="<?php echo IMG_URL; ?>logo_hub.png">
    <meta property="og:url" content="<?php echo CLUB_URL; ?>add-club.php">
    <meta property="og:type" content="website"> 

    <title>ClubHub | Create a Club</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>
    <section class="auth-section">
        <div class="auth-card">
            <h1>Create a Club</h1>
            <p class="auth-subtitle">
                Start a new club, become an executive, and help other students find your community.
            </p>

            <?php if (!empty($errors)): ?>
                <div class="auth-error">
                    <?php foreach ($errors as $err): ?>
                        <?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form
                method="post"
                class="auth-form"
                action="<?php echo PHP_URL; ?>club_handle_add.php"
            >
                <div class="auth-row">
                    <div class="auth-field">
                        <label for="club_name">Club name</label>
                        <input
                            type="text"
                            id="club_name"
                            name="club_name"
                            required
                            maxlength="60"
                            value="<?php echo htmlspecialchars($clubName, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="e.g. AI &amp; Machine Learning Club"
                        >
                    </div>
                </div>

                <div class="auth-row">
                    <div class="auth-field">
                        <label for="club_email">Club email (optional)</label>
                        <input
                            type="email"
                            id="club_email"
                            name="club_email"
                            value="<?php echo htmlspecialchars($clubEmail, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="e.g. aiml@uwindsor.ca"
                        >
                    </div>
                </div>

                <div class="auth-row">
                    <div class="auth-field">
                        <label for="club_condition">Restrictions</label>
                        <select id="club_condition" name="club_condition">
                            <option value="none" <?php if ($clubCondition === 'none') echo 'selected'; ?>>
                                Open to all
                            </option>
                            <option value="undergrad_only" <?php if ($clubCondition === 'undergrad_only') echo 'selected'; ?>>
                                Undergraduates only
                            </option>
                            <option value="women_only" <?php if ($clubCondition === 'women_only') echo 'selected'; ?>>
                                Women only
                            </option>
                            <option value="first_year_only" <?php if ($clubCondition === 'first_year_only') echo 'selected'; ?>>
                                First years only
                            </option>
                        </select>
                    </div>
                </div>

                <div class="auth-row">
                    <div class="auth-field">
                        <label for="club_description">Club description</label>
                        <textarea
                            id="club_description"
                            name="club_description"
                            rows="4"
                            maxlength="600"
                            style="padding:10px 12px;border:2px solid var(--dark-blue);border-radius:10px;font-size:1rem;color:var(--dark-blue);box-sizing:border-box;"
                            placeholder="Tell students what your club is about, what you do, and how often you meet."
                        ><?php echo htmlspecialchars($clubDescription, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="auth-btn">
                    Create club
                </button>
            </form>
        </div>
    </section>
</main>

<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
