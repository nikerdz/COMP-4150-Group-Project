<?php
require_once(__DIR__ . '/../../config/constants.php');
require_once(MODELS_PATH . 'Comment.php');
require_once(MODELS_PATH . 'Event.php');
require_once(MODELS_PATH . 'Membership.php');
require_once(MODELS_PATH . 'User.php');

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

$commentModel    = new Comment();
$eventModel      = new Event();
$membershipModel = new Membership();
$userModel       = new User();

// Load comment
$comment = $commentModel->getCommentById($commentId);
if (!$comment) {
    $_SESSION['error'] = 'Comment not found.';
    header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
    exit;
}

// Ensure the comment actually belongs to this event
if ((int)$comment['event_id'] !== $eventId) {
    $_SESSION['error'] = 'Comment does not belong to this event.';
    header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
    exit;
}

// Load event to know which club it belongs to
$event = $eventModel->findById($eventId);
if (!$event) {
    $_SESSION['error'] = 'Event not found.';
    header('Location: ' . PUBLIC_URL . 'dashboard.php');
    exit;
}

$clubId  = (int)$event['club_id'];
$isOwner = ((int)$comment['user_id'] === (int)$userId);

// Check if current user is an executive for this club
$isExec = false;
$membership = $membershipModel->getMembership($clubId, (int)$userId);
if ($membership) {
    $role = $membership['role'] ?? 'member';
    if ($role !== 'member') {
        $isExec = true;
    }
}

// Check if current user is a site admin
$isAdmin = false;
$currentUser = $userModel->findById((int)$userId);
if ($currentUser && ($currentUser['user_type'] ?? 'student') === 'admin') {
    $isAdmin = true;
}

// owner OR exec OR admin
if (!$isOwner && !$isExec && !$isAdmin) {
    $_SESSION['error'] = 'You do not have permission to delete this comment.';
    header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
    exit;
}

// Perform delete
$ok = $commentModel->deleteById($commentId);

if ($ok) {
    $_SESSION['toast_message'] = 'Comment deleted.';
    $_SESSION['toast_type']    = 'success';
} else {
    $_SESSION['error'] = 'Unable to delete this comment.';
}

header('Location: ' . PUBLIC_URL . 'event/view-event.php?id=' . $eventId);
exit;
