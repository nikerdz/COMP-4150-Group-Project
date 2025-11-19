<?php
require_once('../../src/config/constants.php');
require_once(MODELS_PATH . 'User.php');
require_once(MODELS_PATH . 'Club.php');

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

$userId    = (int) $_SESSION['user_id'];
$userModel = new User();
$clubModel = new Club();

$user      = $userModel->findById($userId);

// If user somehow vanished, force logout
if (!$user) {
    session_unset();
    session_destroy();
    header("Location: " . PUBLIC_URL . "login.php?error=Account not found. Please log in again.");
    exit();
}

// Prefill values
$firstNameVal = htmlspecialchars($user['first_name']      ?? '', ENT_QUOTES, 'UTF-8');
$lastNameVal  = htmlspecialchars($user['last_name']       ?? '', ENT_QUOTES, 'UTF-8');
$facultyVal   = htmlspecialchars($user['faculty']         ?? '', ENT_QUOTES, 'UTF-8');
$levelVal     = $user['level_of_study']                  ?? 'undergraduate';
$yearVal      = !empty($user['year_of_study']) ? (int)$user['year_of_study'] : '';

// All categories for interest checkboxes
$allCategories       = $clubModel->getAllCategories();
// IDs of categories this user already selected
$selectedInterestIds = $userModel->getInterestCategoryIds($userId);

// Flash error (success now shows on profile.php)
$profileError = $_SESSION['profile_error'] ?? null;
unset($_SESSION['profile_error']);
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

    <title>ClubHub | Edit Profile</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <section class="profile-section profile-edit-section">
        <div class="profile-section-header">
            <h2>Edit Profile</h2>
            <p>Update your name, faculty, study details, and interests.</p>
        </div>

        <?php if ($profileError): ?>
            <p class="profile-message profile-message-error">
                <?php echo htmlspecialchars($profileError, ENT_QUOTES, 'UTF-8'); ?>
            </p>
        <?php endif; ?>

        <form action="<?php echo PHP_URL; ?>profile_handle_update.php"
              method="POST"
              class="auth-form profile-edit-form">

            <div class="auth-row">
                <div class="auth-field">
                    <label for="first_name">First Name</label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        value="<?php echo $firstNameVal; ?>"
                        required
                    >
                </div>

                <div class="auth-field">
                    <label for="last_name">Last Name</label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        value="<?php echo $lastNameVal; ?>"
                        required
                    >
                </div>
            </div>

            <div class="auth-row">
                <div class="auth-field">
                    <label for="faculty">Faculty</label>
                    <select id="faculty" name="faculty" required>
                        <option value="" disabled <?php echo $facultyVal === '' ? 'selected' : ''; ?>>
                            Select your faculty
                        </option>
                        <option value="Arts, Humanities & Social Sciences"
                            <?php echo $facultyVal === 'Arts, Humanities & Social Sciences' ? 'selected' : ''; ?>>
                            Arts, Humanities & Social Sciences
                        </option>
                        <option value="Odette School of Business"
                            <?php echo $facultyVal === 'Odette School of Business' ? 'selected' : ''; ?>>
                            Business (Odette)
                        </option>
                        <option value="Education"
                            <?php echo $facultyVal === 'Education' ? 'selected' : ''; ?>>
                            Education
                        </option>
                        <option value="Engineering"
                            <?php echo $facultyVal === 'Engineering' ? 'selected' : ''; ?>>
                            Engineering
                        </option>
                        <option value="Graduate Studies"
                            <?php echo $facultyVal === 'Graduate Studies' ? 'selected' : ''; ?>>
                            Graduate Studies
                        </option>
                        <option value="Human Kinetics"
                            <?php echo $facultyVal === 'Human Kinetics' ? 'selected' : ''; ?>>
                            Human Kinetics
                        </option>
                        <option value="Law"
                            <?php echo $facultyVal === 'Law' ? 'selected' : ''; ?>>
                            Law
                        </option>
                        <option value="Nursing"
                            <?php echo $facultyVal === 'Nursing' ? 'selected' : ''; ?>>
                            Nursing
                        </option>
                        <option value="Science"
                            <?php echo $facultyVal === 'Science' ? 'selected' : ''; ?>>
                            Science
                        </option>
                    </select>
                </div>

                <div class="auth-field">
                    <label for="level_of_study">Level of Study</label>
                    <select id="level_of_study" name="level_of_study">
                        <option value="undergraduate" <?php echo $levelVal === 'undergraduate' ? 'selected' : ''; ?>>
                            Undergraduate
                        </option>
                        <option value="graduate" <?php echo $levelVal === 'graduate' ? 'selected' : ''; ?>>
                            Graduate
                        </option>
                    </select>
                </div>
            </div>

            <div class="auth-row">
                <div class="auth-field">
                    <label for="year_of_study">Year of Study</label>
                    <input
                        type="number"
                        id="year_of_study"
                        name="year_of_study"
                        min="1"
                        max="20"
                        value="<?php echo htmlspecialchars((string)$yearVal, ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="e.g., 3"
                    >
                </div>
            </div>

            <!-- Interests -->
            <div class="profile-edit-interests">
                <label class="profile-interests-title">Your interests</label>
                <div class="profile-interests-grid">
                    <?php foreach ($allCategories as $cat): ?>
                        <?php
                            $catId   = (int)$cat['category_id'];
                            $catName = htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8');
                            $checked = in_array($catId, $selectedInterestIds);
                        ?>
                        <label class="profile-interest-chip">
                            <input
                                type="checkbox"
                                name="interests[]"
                                value="<?php echo $catId; ?>"
                                <?php echo $checked ? 'checked' : ''; ?>
                            >
                            <span><?php echo $catName; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="profile-interests-hint">
                    Choose the topics you care about so we can recommend matching clubs and events.
                </p>
            </div>

            <div class="profile-edit-actions">
                <a href="<?php echo USER_URL; ?>profile.php" class="profile-edit-cancel">
                    Cancel
                </a>
                <button type="submit" class="auth-btn profile-edit-save">
                    Save changes
                </button>
            </div>

        </form>
    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

</body>
</html>
