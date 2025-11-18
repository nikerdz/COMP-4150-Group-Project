<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<?php if (isset($_SESSION['user_id'])): ?>
<footer class="bottom-nav">
    <a href="<?php echo USER_URL; ?>dashboard.php">DASHBOARD</a>
    <a href="<?php echo USER_URL; ?>explore.php">EXPLORE</a>
    <a href="<?php echo USER_URL; ?>profile.php">PROFILE</a>
    <a href="<?php echo USER_URL; ?>settings.php">SETTINGS</a>
</footer>
<?php endif; ?>
