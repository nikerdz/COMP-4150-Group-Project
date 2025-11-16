<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<header>
    <nav class="header">
        <div class="container">

            <!-- Left Button: Sidebar -->
            <button class="header-btn" onclick="toggleSidebar()">
                <img src="<?php echo IMG_URL; ?>btn/menu.png" alt="Menu">
            </button>

            <!-- Logo in center -->
            <a href="<?php echo PUBLIC_URL; ?>" class="logo">
                <img src="<?php echo IMG_URL; ?>logo.png" alt="ClubHub Logo">
            </a>

            <!-- Right Button (Login OR Notifications) -->
            <?php if (!isset($_SESSION['user_id'])): ?>
                
                <!-- User NOT logged in → show LOGIN icon -->
                <a href="<?php echo PUBLIC_URL; ?>login.php" class="header-btn">
                    <img src="<?php echo IMG_URL; ?>btn/login.png" alt="Login">
                </a>

            <?php else: ?>

                <!-- User logged in → show NOTIFICATIONS icon -->
                <button class="header-btn" onclick="openNotifications()">
                    <img src="<?php echo IMG_URL; ?>btn/notif.png" alt="Notifications">
                </button>

            <?php endif; ?>

        </div>
    </nav>
</header>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <ul class="sidebar-links">
        <li><a href="<?php echo PUBLIC_URL; ?>">Home</a></li>
        <?php if(isset($_SESSION['user_id'])): ?>
            <li><a href="<?php echo USER_URL; ?>dashboard.php">Dashboard</a></li>
        <?php endif; ?>
        <li><a href="<?php echo CLUB_URL; ?>">Clubs</a></li>
        <li><a href="<?php echo EVENT_URL; ?>">Events</a></li>
        <?php if(!isset($_SESSION['user_id'])): ?>
            <li><a href="<?php echo PUBLIC_URL; ?>login.php">Login</a></li>
            <li><a href="<?php echo PUBLIC_URL; ?>register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</div>
