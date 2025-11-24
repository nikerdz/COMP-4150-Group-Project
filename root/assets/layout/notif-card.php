<?php

$notifId   = (int)$notif['notification_id'];
$msg       = htmlspecialchars($notif['notification_message']);
$type      = htmlspecialchars($notif['notification_type']);
$timestamp = date('M d, Y · g:i A', strtotime($notif['notification_timestamp']));
$eventId   = (int)$notif['event_id'];

$typeLabel = ucfirst($type); // Reminder / New / Update
?>
<div class="notif-card">
    <div class="notif-card-header">
        <span class="notif-card-type notif-type-<?php echo $type; ?>">
            <?php echo $typeLabel; ?>
        </span>

        <span class="notif-card-time">
            <?php echo $timestamp; ?>
        </span>
    </div>

    <p class="notif-card-message">
        <?php echo $msg; ?>
    </p>

    <div class="notif-card-actions">

        <span class="notif-card-pill">
            <a class="notif-card-link"
            href="<?php echo PUBLIC_URL . 'event/view-event.php?id=' . $eventId; ?>">
                View Event
            </a>
        </span>

        <span class="notif-card-pill <?php echo $notif['notification_status'] === 'unread' ? 'notif-card-pill-unread' : 'notif-card-pill-read'; ?>"
            onclick="toggleNotification(<?php echo $notifId; ?>, this)">
            <a class="notif-card-link">
                <?php echo $notif['notification_status'] === 'unread' ? 'Mark Read' : 'Mark Unread'; ?>
            </a>
        </span>

    </div>


</div>
