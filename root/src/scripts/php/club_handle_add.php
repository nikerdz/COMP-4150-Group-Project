<?php
// Handles POST for creating a new club.
// Can be reused from any "Create club" form.

require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'Club.php');

session_start();

// Must be POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . CLUB_URL . 'add-club.php');
    exit();
}

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . PUBLIC_URL . 'login.php');
    exit();
}

$userId = (int)$_SESSION['user_id'];

$clubName        = trim($_POST['club_name']        ?? '');
$clubEmail       = trim($_POST['club_email']       ?? '');
$clubDescription = trim($_POST['club_description'] ?? '');
$clubCondition   = $_POST['club_condition']        ?? 'none';

$errors = [];

// Validation
if ($clubName === '') {
    $errors[] = 'Club name is required.';
}

if ($clubEmail !== '' && !filter_var($clubEmail, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid club email address or leave it blank.';
}

$allowedConditions = ['none', 'women_only', 'undergrad_only', 'first_year_only'];
if (!in_array($clubCondition, $allowedConditions, true)) {
    $clubCondition = 'none';
}

// If any errors, flash to session and return to form
if (!empty($errors)) {
    $_SESSION['add_club_errors'] = $errors;
    $_SESSION['add_club_old'] = [
        'club_name'        => $clubName,
        'club_email'       => $clubEmail,
        'club_description' => $clubDescription,
        'club_condition'   => $clubCondition,
    ];

    header('Location: ' . CLUB_URL . 'add-club.php');
    exit();
}

$clubModel = new Club();
global $pdo;

try {
    // Create the club row
    $created = $clubModel->create([
        'club_name'        => $clubName,
        'club_email'       => ($clubEmail !== '') ? $clubEmail : null,
        'club_description' => ($clubDescription !== '') ? $clubDescription : null,
        'club_condition'   => $clubCondition,
        'club_status'      => 'active',
    ]);

    if (!$created) {
        throw new RuntimeException('Insert failed.');
    }

    // New club id
    $clubId = (int)$pdo->lastInsertId();

    // Make creator a member of the club
    try {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO Membership (user_id, club_id, membership_date)
            VALUES (:uid, :cid, CURRENT_DATE)
        ");
        $stmt->execute([
            ':uid' => $userId,
            ':cid' => $clubId,
        ]);
    } catch (PDOException $e) {
        // Not fatal
    }

    // Make creator an executive (President) of the club
    try {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO Executive (user_id, club_id, executive_role)
            VALUES (:uid, :cid, 'President')
        ");
        $stmt->execute([
            ':uid' => $userId,
            ':cid' => $clubId,
        ]);
    } catch (PDOException $e) {
        // Not fatal
    }

    // On success, go to that club's page
    header('Location: ' . CLUB_URL . 'view-club.php?id=' . $clubId);
    exit();

} catch (Throwable $e) {
    // Likely a uniqueness violation (name/email) or other DB error
    $_SESSION['add_club_errors'] = [
        'A club with that name or email may already exist, or an error occurred. Please try again.'
    ];
    $_SESSION['add_club_old'] = [
        'club_name'        => $clubName,
        'club_email'       => $clubEmail,
        'club_description' => $clubDescription,
        'club_condition'   => $clubCondition,
    ];

    header('Location: ' . CLUB_URL . 'add-club.php');
    exit();
}
