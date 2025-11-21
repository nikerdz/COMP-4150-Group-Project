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
        $sql = "
            INSERT INTO Comments (user_id, event_id, comment_message)
            VALUES (:uid, :eid, :msg)
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':uid' => $userId,
            ':eid' => $eventId,
            ':msg' => $message,
        ]);
    }

    /* --------------------------
       Get all comments for event
       -------------------------- */
    public function getCommentsForEvent(int $eventId): array
    {
        $sql = "
            SELECT
                c.comment_id,
                c.user_id,
                c.event_id,
                c.comment_message,
                c.comment_date,
                CONCAT(u.first_name, ' ', u.last_name) AS user_name
            FROM Comments c
            JOIN User u ON c.user_id = u.user_id
            WHERE c.event_id = :eid
            ORDER BY c.comment_date DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':eid' => $eventId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* --------------------------
       Delete a comment (only by owner)
       -------------------------- */
    public function deleteComment(int $commentId, int $userId): bool
    {
        $sql = "
            DELETE FROM Comments
            WHERE comment_id = :cid AND user_id = :uid
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':cid' => $commentId,
            ':uid' => $userId,
        ]);

        return $stmt->rowCount() > 0;
    }
}
