<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'Notification.php');

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['unread' => false]);
    exit();
}

$notif = new Notification();
$unread = $notif->getUnread($_SESSION['user_id']);

echo json_encode([
    'unread' => !empty($unread)
]);
exit();
