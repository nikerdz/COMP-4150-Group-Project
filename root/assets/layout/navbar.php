<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<footer class="bottom-nav">
    <a href="<?php echo PUBLIC_URL; ?>">Home</a>
    <a href="<?php echo USER_URL; ?>">Dashboard</a>
    <a href="<?php echo CLUB_URL; ?>">Clubs</a>
    <a href="<?php echo EVENT_URL; ?>">Events</a>
</footer>

