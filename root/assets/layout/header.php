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
                <button class="header-btn" onclick="window.location.href='<?php echo PHP_URL; ?>auth_handle_logout.php'">
                    <img src="<?php echo IMG_URL; ?>btn/logout.png" alt="Logout">
                </button>

            <?php endif; ?>

        </div>
    </nav>
</header>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <ul class="sidebar-links">

        <?php if (isset($_SESSION['user_id'])): ?>

            <?php if (!empty($_SESSION['is_admin'])): ?>
                <!-- Admin Sidebar -->
                <li><a href="<?php echo ADMIN_URL; ?>dashboard.php">DASHBOARD</a></li>
                <li><a href="<?php echo USER_URL; ?>profile.php">PROFILE</a></li>
                <li><a href="<?php echo USER_URL; ?>settings.php">SETTINGS</a></li>
                <li><a href="<?php echo PHP_URL; ?>auth_handle_logout.php">LOGOUT</a></li>

            <?php else: ?>
                <!-- Regular User Sidebar -->
                <li><a href="<?php echo USER_URL; ?>dashboard.php">DASHBOARD</a></li>
                <li><a href="<?php echo USER_URL; ?>explore.php">EXPLORE</a></li>
                <li><a href="<?php echo USER_URL; ?>profile.php">PROFILE</a></li>
                <li><a href="<?php echo USER_URL; ?>settings.php">SETTINGS</a></li>
                <li><a href="<?php echo PHP_URL; ?>auth_handle_logout.php">LOGOUT</a></li>
            <?php endif; ?>

        <?php else: ?>
            <!-- Public Sidebar (not logged in) -->
            <li><a href="<?php echo PUBLIC_URL; ?>">HOME</a></li>
            <li><a href="<?php echo PUBLIC_URL; ?>login.php">LOGIN</a></li>
            <li><a href="<?php echo PUBLIC_URL; ?>register.php">REGISTER</a></li>
        <?php endif; ?>

    </ul>

    <!-- Sidebar logo at bottom -->
    <div class="sidebar-footer">
        <img
            src="<?php echo IMG_URL; ?>logo_hub.png"
            alt="ClubHub Logo"
            class="sidebar-logo"
            id="sidebar-logo"
            data-duck-src="<?php echo IMG_URL; ?>dancing-duck.gif"
        >
    </div>
</div>


<!-- Sidebar overlay (click outside to close) -->
<div id="sidebar-overlay" class="sidebar-overlay" onclick="closeSidebar()"></div>
