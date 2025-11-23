<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'Notification.php');

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

$notifId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($notifId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    exit();
}

$notif = new Notification();

// Fetch notification
$current = $notif->getById($notifId);

if (!$current || $current['user_id'] != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'error' => 'Not found']);
    exit();
}

$newStatus = ($current['notification_status'] === 'unread') ? 'read' : 'unread';
$notif->setStatus($notifId, $newStatus);

echo json_encode([
    'success' => true,
    'newStatus' => $newStatus
]);
exit();
