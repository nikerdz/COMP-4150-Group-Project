<?php
require_once(__DIR__ . '/../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');

class Notification
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /* --------------------------
       Create a notification
       -------------------------- */
    public function create(int $userId, int $eventId, string $message, string $type = 'announcement'): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO Notification (user_id, event_id, notification_message, notification_type)
            VALUES (:user, :event, :msg, :type)
        ");

        return $stmt->execute([
            ':user' => $userId,
            ':event' => $eventId,
            ':msg'   => $message,
            ':type'  => $type
        ]);
    }

    /* --------------------------
       Get ALL notifications for the sidebar
       (newest first)
       -------------------------- */
    public function getAllForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM Notification
            WHERE user_id = :user
            ORDER BY notification_timestamp DESC
        ");
        $stmt->execute([':user' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* --------------------------
       Get UNREAD notifications
       -------------------------- */
    public function getUnread(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM Notification
            WHERE user_id = :user
              AND notification_status = 'unread'
            ORDER BY notification_timestamp DESC
        ");
        $stmt->execute([':user' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* --------------------------
       Mark one as read
       -------------------------- */
    public function markRead(int $notifId): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE Notification
            SET notification_status = 'read'
            WHERE notification_id = :id
        ");

        return $stmt->execute([':id' => $notifId]);
    }

    /* --------------------------
       Mark ALL as read
       -------------------------- */
    public function markAllRead(int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE Notification
            SET notification_status = 'read'
            WHERE user_id = :user
        ");

        return $stmt->execute([':user' => $userId]);
    }

    public function markUnread(int $notifId): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE Notification
            SET notification_status = 'unread'
            WHERE notification_id = :id
        ");

        return $stmt->execute([':id' => $notifId]);
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM Notification WHERE notification_id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function setStatus(int $id, string $status): bool {
        $stmt = $this->pdo->prepare("
            UPDATE Notification
            SET notification_status = :status
            WHERE notification_id = :id
        ");

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }


}
