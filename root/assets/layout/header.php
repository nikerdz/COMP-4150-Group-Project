<script>
    const PHP_URL = "<?php echo PHP_URL; ?>";
    const IMG_URL = "<?php echo IMG_URL; ?>";
</script>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once(MODELS_PATH . 'Notification.php');
require_once(CONFIG_PATH . 'constants.php');

// Fetch notifications ONLY if user logged in
$notifications = [];
$unreadCount = 0;

if (isset($_SESSION['user_id'])) {
    $notifModel = new Notification();
    $notifications = $notifModel->getAllForUser($_SESSION['user_id']);

    // Count unread ones
    foreach ($notifications as $n) {
        if ($n['notification_status'] === 'unread') {
            $unreadCount++;
        }
    }
}
?>
<header>
    <nav class="header">
        <div class="container">

            <!-- Left Button: Sidebar and Back-->
            <div class="header-left">
                <button class="header-btn" onclick="toggleSidebar()">
                    <img src="<?php echo IMG_URL; ?>btn/menu.png">
                </button>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="header-btn">
                        <img src="<?php echo IMG_URL; ?>btn/back.png">
                    </button>
                <?php endif; ?>
            </div>

            <a href="<?php echo PUBLIC_URL; ?>" class="logo">
                <img src="<?php echo IMG_URL; ?>logo.png">
            </a>

            <div class="header-right">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                        $unreadNotifications = $notifModel->getUnread($_SESSION['user_id']);
                        $hasUnread = !empty($unreadNotifications);
                        $notifIcon = $hasUnread ? 'notif.gif' : 'notif.png';
                        ?>

                        <button class="header-btn" onclick="toggleNotifications()">
                            <img src="<?php echo IMG_URL . 'btn/' . $notifIcon; ?>" id="notifIcon">
                        </button>
    
                    <button class="header-btn" onclick="window.location.href='<?php echo PHP_URL; ?>auth_handle_logout.php'">
                        <img src="<?php echo IMG_URL; ?>btn/logout.png">
                    </button>
                <?php else: ?>
                    <a href="<?php echo PUBLIC_URL; ?>login.php" class="header-btn">
                        <img src="<?php echo IMG_URL; ?>btn/login.png">
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </nav>
</header>

<!-- Notification Popup -->
<div id="notificationsPopup" class="notif-popup">
    <div class="notif-popup-inner">

        <h3>Notifications</h3>

        <?php if (empty($notifications)): ?>
            <p class="no-notifs">No notifications yet.</p>
        <?php else: ?>
            <?php foreach ($notifications as $notif): ?>
                <?php include(LAYOUT_PATH . 'notif-card.php'); ?>
                <br>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>


<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <ul class="sidebar-links">

        <?php if (isset($_SESSION['user_id'])): ?>

            <?php if (!empty($_SESSION['is_admin'])): ?>
                <!-- Admin Sidebar -->
                <li><a href="<?php echo ADMIN_URL; ?>dashboard.php">DASHBOARD</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>manage-events.php">MANAGE EVENTS</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>manage-clubs.php">MANAGE CLUBS</a></li>
                <li><a href="<?php echo ADMIN_URL; ?>manage-users.php">MANAGE USERS</a></li>
                <li><a href="<?php echo USER_URL; ?>profile.php">PROFILE</a></li>
                <li><a href="<?php echo USER_URL; ?>settings.php">SETTINGS</a></li>
                <li><a href="<?php echo PHP_URL; ?>auth_handle_logout.php">LOGOUT</a></li>

            <?php else: ?>
                <!-- Regular User Sidebar -->
                <li><a href="<?php echo USER_URL; ?>dashboard.php">DASHBOARD</a></li>
                <li><a href="<?php echo USER_URL; ?>explore.php">EXPLORE</a></li>
                <li><a href="<?php echo CLUB_URL; ?>user-clubs.php">MY CLUBS</a></li>
                <li><a href="<?php echo EVENT_URL; ?>user-events.php">MY EVENTS</a></li>
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


<!-- Sidebar overlay -->
<div id="sidebar-overlay" class="sidebar-overlay" onclick="closeSidebar()"></div>