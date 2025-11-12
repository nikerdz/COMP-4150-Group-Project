<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<header>
    <nav class="header">
        <div class="container">
            <!-- Left Button: Sidebar -->
            <button class="header-btn left-btn" onclick="openSidebar()">☰</button>

            <!-- Logo in center -->
            <a href="<?php echo PUBLIC_URL; ?>" class="logo">
                <img src="<?php echo IMG_URL; ?>logo.png" alt="ClubHub Logo">
            </a>

            <!-- Right Button: Notifications -->
            <button class="header-btn right-btn" onclick="openNotifications()">🔔</button>
        </div>
    </nav>
</header>
