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
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Add a comment for an event.
     * Matches your table:
     * Comments(user_id, event_id, comment_message, comment_date DEFAULT CURRENT_TIMESTAMP)
     */
    public function addComment(int $userId, int $eventId, string $text): bool
    {
        $sql = "
            INSERT INTO Comments (user_id, event_id, comment_message)
            VALUES (:uid, :eid, :msg)
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':uid' => $userId,
            ':eid' => $eventId,
            ':msg' => $text
        ]);
    }

    /**
     * Get comments for a specific event.
     * Uses:
     *  - Comments.comment_message
     *  - Comments.comment_date
     *  - User.first_name / last_name
     */
    public function getCommentsForEvent(int $eventId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                c.comment_id,
                c.comment_message,
                c.comment_date,
                u.user_id,
                CONCAT(u.first_name, ' ', u.last_name) AS user_name
            FROM Comments c
            JOIN User u ON c.user_id = u.user_id
            WHERE c.event_id = :eid
            ORDER BY c.comment_date DESC
        ");

        $stmt->execute([':eid' => $eventId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
