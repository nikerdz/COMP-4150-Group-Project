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
        try {
            $stmt = $this->pdo->prepare("CALL sp_registration_register(?, ?)");
            return $stmt->execute([$userId, $eventId]);

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
        $stmt = $this->pdo->prepare("CALL sp_registration_unregister(?, ?)");
        return $stmt->execute([$userId, $eventId]);
    }


    /* ---------------------------------------------------
       Check if a user is registered
       --------------------------------------------------- */
    public function isRegistered(int $userId, int $eventId): bool
    {
        $sql = file_get_contents(KQ_URL . 'registration/kq_registration_is_registered.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $eventId]);

        return $stmt->fetchColumn() > 0;
    }


    /* ---------------------------------------------------
       Get all event IDs a user is registered for
       --------------------------------------------------- */
    public function getEventIdsForUser(int $userId): array
    {
        $sql = file_get_contents(KQ_URL . 'registration/kq_registration_get_event_ids_for_user.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }


    /* ---------------------------------------------------
       Get full event details for upcoming registered events
       Only approved events from active clubs
       --------------------------------------------------- */
    public function getUpcomingEventsForUser(int $userId, int $limit = 6): array
    {
        $sql = file_get_contents(KQ_URL . 'registration/kq_registration_get_upcoming_events_for_user.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* ---------------------------------------------------
       Get all users registered for an event
       (used for the registrations section)
       --------------------------------------------------- */
    public function getUsersForEvent(int $eventId): array
    {
        $sql = file_get_contents(KQ_URL . 'registration/kq_registration_get_users_for_event.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$eventId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* ---------------------------------------------------
       Count number of people registered for event
       --------------------------------------------------- */
    public function countRegistrations(int $eventId): int
    {
        $sql = file_get_contents(KQ_URL . 'registration/kq_registration_count.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$eventId]);

        return (int)$stmt->fetchColumn();
    }


    /* ---------------------------------------------------
       Get a single registration record (or null)
       --------------------------------------------------- */
    public function getRegistration(int $eventId, int $userId): ?array
    {
        $sql = file_get_contents(KQ_URL . 'registration/kq_registration_get_one.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$eventId, $userId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

}
