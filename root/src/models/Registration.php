<?php
/*
Handles:
register user for event
unregister
check if registered
get events user is registered for
get list of users registered to event
*/

require_once(__DIR__ . '/../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');

class Registration
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /* ---------------------------------------------------
       Register a user for an event
       --------------------------------------------------- */
    public function register(int $userId, int $eventId): bool
    {
        $sql = "
            INSERT INTO Registration (user_id, event_id)
            VALUES (:uid, :eid)
        ";

        $stmt = $this->pdo->prepare($sql);

        try {
            return $stmt->execute([
                ':uid' => $userId,
                ':eid' => $eventId
            ]);
        } catch (PDOException $e) {
            // Duplicate = already registered
            return false;
        }
    }

    /* ---------------------------------------------------
       Cancel registration
       --------------------------------------------------- */
    public function unregister(int $userId, int $eventId): bool
    {
        $sql = "
            DELETE FROM Registration
            WHERE user_id = :uid AND event_id = :eid
        ";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':uid' => $userId,
            ':eid' => $eventId
        ]);
    }

    /* ---------------------------------------------------
       Check if a user is registered
       --------------------------------------------------- */
    public function isRegistered(int $userId, int $eventId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM Registration
            WHERE user_id = :uid AND event_id = :eid
        ");
        $stmt->execute([
            ':uid' => $userId,
            ':eid' => $eventId
        ]);

        return $stmt->fetchColumn() > 0;
    }

    /* ---------------------------------------------------
       Get all event IDs a user is registered for
       --------------------------------------------------- */
    public function getEventIdsForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT event_id
            FROM Registration
            WHERE user_id = :uid
        ");
        $stmt->execute([':uid' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /* ---------------------------------------------------
       Get full event details for upcoming registered events
       (moved logically out of Event.php)
       --------------------------------------------------- */
    public function getUpcomingEventsForUser(int $userId, int $limit = 6): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                e.*, 
                c.club_name,
                r.registration_date
            FROM Registration r
            JOIN Event e ON r.event_id = e.event_id
            JOIN Club c ON e.club_id = c.club_id
            WHERE r.user_id = :uid
              AND e.event_status <> 'cancelled'
              AND c.club_status = 'active'
              AND (e.event_date >= NOW() OR e.event_date IS NULL)
            ORDER BY e.event_date ASC
            LIMIT :limit
        ");

        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------------------------------------------------
       Get all users registered for an event
       (used for the registrations section)
       --------------------------------------------------- */
    public function getUsersForEvent(int $eventId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                u.user_id,
                u.first_name,
                u.last_name,
                COALESCE(e.executive_role, 'member') AS role
            FROM Registration r
            JOIN User u ON r.user_id = u.user_id
            JOIN Event ev ON r.event_id = ev.event_id
            LEFT JOIN Executive e 
              ON u.user_id = e.user_id 
             AND ev.club_id = e.club_id
            WHERE r.event_id = :eid
            ORDER BY 
                CASE
                    WHEN LOWER(COALESCE(e.executive_role, 'member')) IN ('admin','administrator','president','owner')
                        THEN 0
                    WHEN LOWER(COALESCE(e.executive_role, 'member')) <> 'member'
                        THEN 1
                    ELSE 2
                END,
                u.first_name ASC
        ");
        $stmt->execute([':eid' => $eventId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------------------------------------------------
       Count number of people registered for event
       --------------------------------------------------- */
    public function countRegistrations(int $eventId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM Registration
            WHERE event_id = :eid
        ");

        $stmt->execute([':eid' => $eventId]);
        return (int)$stmt->fetchColumn();
    }

    /* ---------------------------------------------------
       Get a single registration record (or null)
       --------------------------------------------------- */
    public function getRegistration(int $eventId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM Registration
            WHERE event_id = :eid AND user_id = :uid
            LIMIT 1
        ");

        $stmt->execute([
            ':eid' => $eventId,
            ':uid' => $userId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
