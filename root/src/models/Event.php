<?php
require_once(__DIR__ . '/../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');

class Event
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /* --------------------------
       Get one event by ID (admin can see everything)
       -------------------------- */
    public function findById(int $eventId): ?array
    {
        $sql = file_get_contents(KQ_URL . 'event/kq_event_find_by_id.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$eventId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    /* --------------------------
       Search events with filters (public / user-facing)
       - By default returns ONLY approved events
       - Hides events from inactive clubs
       -------------------------- */
    public function searchEvents(
        ?string $search,
        ?int $categoryId,
        ?string $condition,
        int $limit = 20,
        int $offset = 0,
        string $statusFilter = 'approved',
        bool $includeInactiveClubs = false
    ): array {

        $stmt = $this->pdo->prepare("CALL sp_event_search(?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $search ?? '',
            $categoryId,
            $condition ?? 'any',
            $limit,
            $offset,
            $statusFilter,
            $includeInactiveClubs ? 1 : 0
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* --------------------------
       Admin search for Manage Events page
       (shows pending/approved depending on filter)
       -------------------------- */
    public function searchEventsAdmin(string $search = '', string $status = 'all'): array
    {
        $stmt = $this->pdo->prepare("CALL sp_event_search_admin(?, ?)");

        $stmt->execute([
            $search,
            $status
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* --------------------------
       Create a new event
       (defaults to event_status = 'pending' from DB)
       -------------------------- */
    public function createEvent(array $data): int
    {
        $stmt = $this->pdo->prepare("CALL sp_event_create(?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $data['club_id'],
            $data['event_name'],
            $data['event_description'] ?? null,
            $data['event_location'] ?? null,
            $data['event_date'],
            $data['capacity'] ?? null,
            $data['event_condition'] ?? 'none',
            $data['event_fee'] ?? 0.00
        ]);

        // Fetch inserted ID returned by SP
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['event_id'];
    }


    /* --------------------------
       Update an existing event
       -------------------------- */
    public function updateEvent(int $eventId, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            CALL sp_event_update(?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $eventId,
            $data['event_name'],
            $data['event_description'],
            $data['event_location'],
            $data['event_date'],
            $data['capacity'],
            $data['event_condition'],
            $data['event_fee']
        ]);
    }


    /* --------------------------
       Update only the event_status
       Used by admin approve/unapprove handlers
       -------------------------- */
    public function updateStatus(int $eventId, string $status): bool
    {
        $allowed = ['pending', 'approved', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $stmt = $this->pdo->prepare("CALL sp_event_update_status(?, ?)");
        return $stmt->execute([$eventId, $status]);
    }


    /* --------------------------
       Visible event for normal users:
       - Club must be active
       - Event must be approved
       -------------------------- */
    public function findVisibleById(int $eventId): ?array
    {
        $sql = file_get_contents(KQ_URL . 'event/kq_event_find_visible_by_id.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$eventId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function deleteEvent(int $eventId): bool
    {
        try {
            $stmt = $this->pdo->prepare("CALL sp_event_delete(?)");
            $stmt->execute([$eventId]);
            return true;

        } catch (PDOException $e) {
            error_log("Failed to delete event: " . $e->getMessage());
            return false;
        }
    }




}
