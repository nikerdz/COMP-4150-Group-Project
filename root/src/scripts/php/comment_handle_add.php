<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'Comment.php');

session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: ' . PUBLIC_URL . 'login.php');
    exit;
}

$eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
$comment = trim($_POST['comment'] ?? '');

if ($eventId <= 0) {
    $_SESSION['error'] = 'Invalid event.';
    header('Location: ' . PUBLIC_URL . 'dashboard.php');
    exit;
}

if ($comment === '') {
    $_SESSION['error'] = 'Please enter a comment.';
    header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
    exit;
}

if (mb_strlen($comment) > 1000) {
    $_SESSION['error'] = 'Comment is too long. Please keep it under 1000 characters.';
    header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
    exit;
}

$commentModel = new Comment();
$ok = $commentModel->addEventComment($userId, $eventId, $comment);

if ($ok) {
    $_SESSION['toast_message'] = 'Your comment has been posted.';
    $_SESSION['toast_type']    = 'success';
} else {
    $_SESSION['error'] = 'Could not save your comment. Please try again.';
}

header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
exit;
