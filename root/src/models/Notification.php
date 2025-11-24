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
        $stmt = $this->pdo->prepare("CALL sp_notification_create(?, ?, ?, ?)");

        return $stmt->execute([
            $userId,
            $eventId,
            $message,
            $type
        ]);
    }


    /* --------------------------
       Get ALL notifications for the sidebar
       (newest first)
       -------------------------- */
    public function getAllForUser(int $userId): array
    {
        // Load Key Query file
        $sql = file_get_contents(KQ_URL . 'notification/kq_notification_get_all_for_user.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* --------------------------
       Get UNREAD notifications
       -------------------------- */
    public function getUnread(int $userId): array
    {
        // Load Key Query
        $sql = file_get_contents(KQ_URL . 'notification/kq_notification_get_unread.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* --------------------------
       Mark one as read
       -------------------------- */
    public function markRead(int $notifId): bool
    {
        $stmt = $this->pdo->prepare("CALL sp_notification_mark_read(?)");
        return $stmt->execute([$notifId]);
    }


    /* --------------------------
       Mark ALL as read
       -------------------------- */
    public function markAllRead(int $userId): bool
    {
        $stmt = $this->pdo->prepare("CALL sp_notification_mark_all_read(?)");
        return $stmt->execute([$userId]);
    }

    public function markUnread(int $notifId): bool
    {
        $stmt = $this->pdo->prepare("CALL sp_notification_mark_unread(?)");
        return $stmt->execute([$notifId]);
    }

    public function getById(int $id): ?array
    {
        // Load key query SQL file
        $sql = file_get_contents(KQ_URL . 'notification/kq_notification_get_by_id.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function setStatus(int $id, string $status): bool
    {
        $allowed = ['read', 'unread'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $stmt = $this->pdo->prepare("CALL sp_notification_set_status(?, ?)");
        return $stmt->execute([$id, $status]);
    }



}
