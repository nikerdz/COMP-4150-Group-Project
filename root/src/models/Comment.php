<?php
require_once(__DIR__ . '/../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');

class Comment
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /* --------------------------
       Add a comment on an event
       -------------------------- */
    public function addEventComment(int $userId, int $eventId, string $message): bool
    {
        $stmt = $this->pdo->prepare("CALL sp_comment_add(?, ?, ?)");

        return $stmt->execute([
            $userId,
            $eventId,
            $message
        ]);
    }


    /* --------------------------
       Get all comments for event
       -------------------------- */
    public function getCommentsForEvent(int $eventId): array
    {
        $isAdmin = !empty($_SESSION['is_admin']);

        // Choose correct key query
        $file = $isAdmin
            ? 'comment/kq_comment_get_all_for_event_admin.sql'
            : 'comment/kq_comment_get_all_for_event_user.sql';

        // Load SQL
        $sql = file_get_contents(KQ_URL . $file);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$eventId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* --------------------------
       Get a single comment
       -------------------------- */
    public function getCommentById(int $commentId): ?array
    {
        // Load Key Query
        $sql = file_get_contents(KQ_URL . 'comment/kq_comment_get_by_id.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$commentId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    /* --------------------------
       Delete by comment id
       -------------------------- */
    public function deleteById(int $commentId): bool
    {
        $stmt = $this->pdo->prepare("CALL sp_comment_delete(?)");
        $stmt->execute([$commentId]);

        // rowCount() cannot be used after CALL reliably
        return true;
    }


    /* --------------------------
       Delete only by owner
       -------------------------- */
    public function deleteComment(int $commentId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("CALL sp_comment_delete_owned(?, ?)");
        $stmt->execute([$commentId, $userId]);

        // rowCount unreliable with CALL; assume success unless an exception occurs.
        return true;
    }


    /* --------------------------
   Get recent comments by user
   -------------------------- */
    public function getCommentsForUser(int $userId, int $limit = 6): array
    {
        $isAdmin = !empty($_SESSION['is_admin']);

        // Load the correct Key Query file
        $sqlFile = $isAdmin
            ? KQ_URL . 'comment/kq_comment_get_for_user_admin.sql'
            : KQ_URL . 'comment/kq_comment_get_for_user_normal.sql';

        $sql = file_get_contents($sqlFile);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $limit]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}
