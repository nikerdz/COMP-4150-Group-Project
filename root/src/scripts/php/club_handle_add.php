<?php
// Handle "Create Club" form submission (POST from add-club.php)

require_once(__DIR__ . '/../../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');
require_once(MODELS_PATH . 'Club.php');
require_once(MODELS_PATH . 'Membership.php');

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . PUBLIC_URL . "login.php");
    exit();
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . CLUB_URL . "add-club.php");
    exit();
}

$userId = (int) $_SESSION['user_id'];

// ------------------------------
// Read + sanitize form fields
// ------------------------------
$name        = trim($_POST['club_name']        ?? '');
$email       = trim($_POST['club_email']       ?? '');
$description = trim($_POST['club_description'] ?? '');
$condition   = $_POST['club_condition']        ?? 'none';
$categories  = $_POST['categories']            ?? [];

// Basic validation
if ($name === '') {
    $_SESSION['club_add_error'] = 'Club name is required.';
    header("Location: " . CLUB_URL . "add-club.php");
    exit();
}

// Ensure condition is valid
$allowedConditions = ['none', 'women_only', 'undergrad_only', 'first_year_only'];
if (!in_array($condition, $allowedConditions, true)) {
    $condition = 'none';
}

$clubModel       = new Club();
$membershipModel = new Membership();

// We need raw PDO as well for tags + Executive insert
global $pdo;

try {
    $pdo->beginTransaction();

    // 1) Insert into Club table via model
    $created = $clubModel->create([
        'club_name'        => $name,
        'club_email'       => $email !== '' ? $email : null,
        'club_description' => $description !== '' ? $description : null,
        'club_condition'   => $condition,
        'club_status'      => 'active',
    ]);

    if (!$created) {
        throw new Exception('Club create failed.');
    }

    // Get new club_id
    $clubId = (int) $pdo->lastInsertId();
    if ($clubId <= 0) {
        throw new Exception('Could not determine new club ID.');
    }

    // 2) Insert selected categories into Club_Tags
    if (!empty($categories) && is_array($categories)) {
        $stmtTag = $pdo->prepare("
            INSERT IGNORE INTO Club_Tags (club_id, category_id)
            VALUES (:club_id, :cat_id)
        ");

        foreach ($categories as $catId) {
            $catId = (int) $catId;
            if ($catId <= 0) {
                continue;
            }

            $stmtTag->execute([
                ':club_id' => $clubId,
                ':cat_id'  => $catId,
            ]);
        }
    }

    // 3) Add creator as a member
    $membershipModel->join($userId, $clubId);

    // 4) Add creator as an executive
    $stmtExec = $pdo->prepare("
        INSERT INTO Executive (user_id, club_id, executive_role)
        VALUES (:uid, :cid, 'Executive')
        ON DUPLICATE KEY UPDATE executive_role = VALUES(executive_role)
    ");

    $stmtExec->execute([
        ':uid' => $userId,
        ':cid' => $clubId,
    ]);

    // All good
    $pdo->commit();

    // ✅ Flash success toast message for next page
    $_SESSION['club_add_success'] = 'Your club has been created!';

    // Redirect to the club’s page (this is “whichever page” that will show the toast)
    header("Location: " . PUBLIC_URL . "club/view-club.php?id=" . $clubId);
    exit();
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Optional: log $e->getMessage() somewhere

    $_SESSION['club_add_error'] = 'Could not create club. Please try again.';
    header("Location: " . CLUB_URL . "add-club.php");
    exit();
}
