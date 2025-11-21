<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'Comment.php');

session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: ' . PUBLIC_URL . 'login.php');
    exit;
}

$commentId = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
$eventId   = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;

if ($commentId <= 0 || $eventId <= 0) {
    $_SESSION['error'] = 'Invalid comment.';
    header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
    exit;
}

$commentModel = new Comment();
$ok = $commentModel->deleteComment($commentId, $userId);

if ($ok) {
    $_SESSION['toast_message'] = 'Your comment has been deleted.';
    $_SESSION['toast_type']    = 'success';
} else {
    $_SESSION['error'] = 'Unable to delete this comment.';
}

header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
exit;
