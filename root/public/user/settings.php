<?php
require_once('../../src/config/constants.php');
require_once(MODELS_PATH . 'User.php');

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

$userId    = (int) $_SESSION['user_id'];
$userModel = new User();
$user      = $userModel->findById($userId);

// If user vanished, force logout
if (!$user) {
    session_unset();
    session_destroy();
    header("Location: " . PUBLIC_URL . "login.php?error=Account not found. Please log in again.");
    exit();
}

// Flash messages for settings
$settingsError   = $_SESSION['settings_error']   ?? null;
$settingsSuccess = $_SESSION['settings_success'] ?? null;
unset($_SESSION['settings_error'], $_SESSION['settings_success']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta property="og:title" content="ClubHub - Account Settings">
    <meta property="og:description" content="Manage your ClubHub account settings.">
    <meta property="og:image" content="<?php echo IMG_URL; ?>logo_hub.png">
    <meta property="og:url" content="https://khan661.myweb.cs.uwindsor.ca/COMP-4150-Group-Project/root/public/user/settings.php">
    <meta property="og:type" content="website">

    <title>ClubHub | Settings</title>

    <link rel="icon" type="image/png" href="<?php echo IMG_URL; ?>favicon-32x32.png">
    <link rel="stylesheet" href="<?php echo STYLE_URL; ?>?v=<?php echo time(); ?>">
</head>

<body>

<?php include_once(LAYOUT_PATH . 'header.php'); ?>

<main>

    <section class="profile-section settings-section">
        <div class="profile-section-header">
            <h2>Account Settings</h2>
            <p>Change your password or delete your account.</p>
        </div>

        <?php if ($settingsError): ?>
            <p class="profile-message profile-message-error">
                <?php echo htmlspecialchars($settingsError, ENT_QUOTES, 'UTF-8'); ?>
            </p>
        <?php endif; ?>

        <?php if ($settingsSuccess): ?>
            <div class="auth-toast auth-toast-success">
                <?php echo htmlspecialchars($settingsSuccess, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- Change password -->
        <div class="settings-group" id="password">
            <h3 class="settings-group-title">Change password</h3>
            <p class="settings-group-text">
                Update your password for this account. You’ll use this new password the next time you log in.
            </p>

            <form action="<?php echo PHP_URL; ?>settings_handle_password.php"
                  method="POST"
                  class="auth-form settings-form">

                <div class="auth-field">
                    <label for="current_password">Current password</label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        required
                    >
                </div>

                <div class="auth-row settings-password-row">
                    <div class="auth-field">
                        <label for="new_password">New password</label>
                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            required
                        >
                    </div>

                    <div class="auth-field settings-view-wrapper">
                        <label>&nbsp;</label>
                        <button type="button"
                                id="hold-view-password"
                                class="settings-view-toggle">
                            Hold to view
                        </button>
                    </div>
                </div>

                <div class="auth-field">
                    <label for="confirm_new_password">Confirm new password</label>
                    <input
                        type="password"
                        id="confirm_new_password"
                        name="confirm_new_password"
                        required
                    >
                </div>

                <div class="settings-actions">
                    <button type="submit" class="auth-btn settings-save-btn">
                        Save new password
                    </button>
                </div>

            </form>
        </div>

        <!-- Delete account -->
        <div class="settings-group settings-danger" id="delete">
            <h3 class="settings-group-title">Delete account</h3>
            <p class="settings-group-text">
                Deleting your account will permanently remove your profile, club memberships,
                event registrations, and related data. This action cannot be undone.
            </p>

            <form action="<?php echo PHP_URL; ?>settings_handle_delete.php"
                  method="POST"
                  id="delete-account-form"
                  class="auth-form settings-form">

                <div class="auth-field">
                    <label for="delete_password">Confirm password</label>
                    <input
                        type="password"
                        id="delete_password"
                        name="delete_password"
                        required
                    >
                </div>

                <div class="settings-actions">
                    <button type="submit" class="settings-delete-btn">
                        Delete my account
                    </button>
                </div>

            </form>
        </div>

    </section>

</main>

<?php include_once(LAYOUT_PATH . 'navbar.php'); ?>
<?php include_once(LAYOUT_PATH . 'footer.php'); ?>

<script src="<?php echo JS_URL; ?>script.js?v=<?php echo time(); ?>"></script>

<!-- Page-specific JS so you can easily remove it later if you want -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // "Hold to view" for new password
    const pwInput = document.getElementById('new_password');
    const toggleBtn = document.getElementById('hold-view-password');

    if (pwInput && toggleBtn) {
        const show = () => { pwInput.type = 'text'; };
        const hide = () => { pwInput.type = 'password'; };

        toggleBtn.addEventListener('mousedown', show);
        toggleBtn.addEventListener('touchstart', show);

        toggleBtn.addEventListener('mouseup', hide);
        toggleBtn.addEventListener('mouseleave', hide);
        toggleBtn.addEventListener('touchend', hide);
        toggleBtn.addEventListener('touchcancel', hide);
    }

    // Confirm popup for delete account
    const deleteForm = document.getElementById('delete-account-form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function (e) {
            const ok = confirm(
                "Are you sure you want to delete your account?\n\n" +
                "This is permanent and cannot be undone."
            );
            if (!ok) {
                e.preventDefault();
            }
        });
    }
});
</script>

</body>
</html>
