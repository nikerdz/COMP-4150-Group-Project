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
        $isAdmin = !empty($_SESSION['is_admin']);

        $sql = "
            SELECT
                c.comment_id,
                c.user_id,
                c.event_id,
                c.comment_message,
                c.comment_date,
                CONCAT(u.first_name, ' ', u.last_name) AS user_name,
                e.event_status,
                cl.club_status
            FROM Comments c
            JOIN User u ON c.user_id = u.user_id
            JOIN Event e ON c.event_id = e.event_id
            JOIN Club cl ON e.club_id = cl.club_id
            WHERE c.event_id = :eid
        ";

        if (!$isAdmin) {
            $sql .= "
                AND e.event_status = 'approved'
                AND cl.club_status = 'active'
            ";
        }

        $sql .= " ORDER BY c.comment_date DESC ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':eid' => $eventId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* --------------------------
       Get a single comment
       -------------------------- */
    public function getCommentById(int $commentId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM Comments
            WHERE comment_id = :cid
            LIMIT 1
        ");
        $stmt->execute([':cid' => $commentId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* --------------------------
       Delete by comment id
       -------------------------- */
    public function deleteById(int $commentId): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM Comments
            WHERE comment_id = :cid
        ");
        $stmt->execute([':cid' => $commentId]);

        return $stmt->rowCount() > 0;
    }

    /* --------------------------
       Delete only by owner
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

    /* --------------------------
   Get recent comments by user
   -------------------------- */
    public function getCommentsForUser(int $userId, int $limit = 6): array
    {
        $isAdmin = !empty($_SESSION['is_admin']);

        $sql = "
            SELECT 
                c.comment_id,
                c.comment_message,
                c.comment_date,
                c.event_id,
                e.event_name,
                e.event_status,
                cl.club_status
            FROM Comments c
            JOIN Event e ON c.event_id = e.event_id
            JOIN Club cl ON e.club_id = cl.club_id
            WHERE c.user_id = :uid
        ";

        if (!$isAdmin) {
            $sql .= "
                AND e.event_status = 'approved'
                AND cl.club_status = 'active'
            ";
        }

        $sql .= "
            ORDER BY c.comment_date DESC
            LIMIT :lim
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}
