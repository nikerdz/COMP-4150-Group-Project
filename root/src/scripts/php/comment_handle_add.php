<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'Comment.php');

session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: ' . PUBLIC_URL . 'login.php');
    exit;
}

$eventId     = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
$commentText = trim($_POST['comment'] ?? '');

if ($eventId <= 0 || $commentText === '') {
    $_SESSION['error'] = 'Could not add comment.';
    header('Location: ' . PUBLIC_URL . 'dashboard.php');
    exit;
}

$commentModel = new Comment();
$ok = $commentModel->addComment($userId, $eventId, $commentText);

if ($ok) {
    $_SESSION['toast_message'] = 'Comment posted.';
    $_SESSION['toast_type']    = 'success';
} else {
    $_SESSION['error'] = 'There was a problem posting your comment.';
}

header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
exit;
