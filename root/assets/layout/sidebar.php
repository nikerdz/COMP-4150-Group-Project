<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <ul class="sidebar-links">
        <li><a href="<?php echo PUBLIC_URL; ?>index.php">Home</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (!empty($_SESSION['is_admin'])): ?>
                <!-- Admin Links -->
                <li><a href="<?php echo ADMIN_URL; ?>dashboard.php">Admin Dashboard</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>monitor.php">Systems Monitor</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>manage-recipes.php">Manage Recipes</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>manage-users.php">Manage Users</a></li>
                <li><a href="<?php echo USER_URL; ?>user-settings.php">Settings</a></li>
            <?php else: ?>
                <!-- Regular User Links -->
                <li><a href="<?php echo USER_URL; ?>explore.php">Explore</a></li>
                <li><a href="<?php echo USER_URL; ?>dashboard.php">My Dashboard</a></li>
                <li><a href="<?php echo USER_URL; ?>profile.php">My Profile</a></li>
                <li><a href="<?php echo USER_URL; ?>user-settings.php">Settings</a></li>
            <?php endif; ?>
        <?php endif; ?>

        <li><a href="<?php echo PUBLIC_URL; ?>about.php">About</a></li>
        <li><a href="<?php echo PUBLIC_URL; ?>contact.php">Contact</a></li>
    </ul>

    <!-- Profile Section at Bottom -->
    <div class="sidebar-profile">
        <img src="<?php echo $profilePic; ?>" alt="Profile Picture">
        <div class="profile-info">
            <?php if (isset($_SESSION['user_id'])): ?>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <a href="<?php echo PHP_URL; ?>logout_submit.php" class="logout-btn">Log Out</a>
            <?php else: ?>
                <a href="<?php echo PUBLIC_URL; ?>login.php" class="auth-link">Log In</a>
                <a href="<?php echo PUBLIC_URL; ?>register.php" class="auth-link">Register</a>
            <?php endif; ?>
        </div>
    </div>
</div>
