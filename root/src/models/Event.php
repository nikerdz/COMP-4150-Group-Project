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
        $stmt = $this->pdo->prepare("
            SELECT 
                e.*,
                c.club_name
            FROM Event e
            JOIN Club c ON e.club_id = c.club_id
            WHERE e.event_id = :id
        ");
        $stmt->execute([':id' => $eventId]);
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
        string $statusFilter = 'approved',   // 'approved' | 'pending' | 'cancelled' | 'any'
        bool $includeInactiveClubs = false   // usually false for users, true for admin tools
    ): array {
        $sql = "
            SELECT
                e.*,
                c.club_name,
                GROUP_CONCAT(DISTINCT cat.category_name) AS categories
            FROM Event e
            JOIN Club c ON e.club_id = c.club_id
            LEFT JOIN Club_Tags ct ON c.club_id = ct.club_id
            LEFT JOIN Category cat ON ct.category_id = cat.category_id
            WHERE 1=1
        ";

        // --------------------
        // Status filter
        // --------------------
        if ($statusFilter === 'approved') {
            $sql .= " AND e.event_status = 'approved'";
        } elseif ($statusFilter === 'pending') {
            $sql .= " AND e.event_status = 'pending'";
        } elseif ($statusFilter === 'cancelled') {
            $sql .= " AND e.event_status = 'cancelled'";
        } elseif ($statusFilter === 'any') {
            // show all statuses, no extra WHERE on event_status
        } else {
            // Fallback: approved-only
            $sql .= " AND e.event_status = 'approved'";
        }

        // --------------------
        // Club visibility
        // --------------------
        if (!$includeInactiveClubs) {
            $sql .= " AND c.club_status = 'active'";
        }

        // --------------------
        // Dynamic filters
        // --------------------
        if (!empty($search)) {
            $sql .= "
                AND (
                    e.event_name        LIKE :search_event_name
                    OR e.event_description LIKE :search_event_desc
                    OR c.club_name      LIKE :search_club_name
                )
            ";
        }

        if (!empty($categoryId)) {
            $sql .= " AND ct.category_id = :catId";
        }

        if (!empty($condition) && $condition !== 'any') {
            $sql .= " AND e.event_condition = :cond";
        }

        $sql .= "
            GROUP BY e.event_id
            ORDER BY e.event_date ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);

        // --------------------
        // Bind parameters
        // --------------------
        if (!empty($search)) {
            $like = '%' . $search . '%';
            $stmt->bindValue(':search_event_name', $like, PDO::PARAM_STR);
            $stmt->bindValue(':search_event_desc', $like, PDO::PARAM_STR);
            $stmt->bindValue(':search_club_name',  $like, PDO::PARAM_STR);
        }

        if (!empty($categoryId)) {
            $stmt->bindValue(':catId', (int)$categoryId, PDO::PARAM_INT);
        }

        if (!empty($condition) && $condition !== 'any') {
            $stmt->bindValue(':cond', $condition, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* --------------------------
       Admin search for Manage Events page
       (shows pending/approved depending on filter)
       -------------------------- */
    public function searchEventsAdmin(string $search = '', string $status = 'approved'): array
    {
        $sql = "
            SELECT
                e.*,
                c.club_name
            FROM Event e
            JOIN Club c ON e.club_id = c.club_id
            WHERE 1=1
        ";

        $params = [];

        if (in_array($status, ['pending', 'approved', 'cancelled'], true)) {
            $sql .= " AND e.event_status = :status";
            $params[':status'] = $status;
        }

        if ($search !== '') {
            $sql .= " AND (
                e.event_name        LIKE :q
                OR e.event_description LIKE :q
                OR c.club_name      LIKE :q
            )";
            $params[':q'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY e.event_date ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* --------------------------
       Create a new event
       (defaults to event_status = 'pending' from DB)
       -------------------------- */
    public function createEvent(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO Event (
                club_id, event_name, event_description, event_location, 
                event_date, capacity, event_condition, event_fee
            ) VALUES (
                :club_id, :event_name, :event_description, :event_location, 
                :event_date, :capacity, :event_condition, :event_fee
            )
        ");

        return $stmt->execute([
            ':club_id'          => $data['club_id'],
            ':event_name'       => $data['event_name'],
            ':event_description'=> $data['event_description'] ?? null,
            ':event_location'   => $data['event_location'] ?? null,
            ':event_date'       => $data['event_date'],
            ':capacity'         => $data['capacity'] ?? null,
            ':event_condition'  => $data['event_condition'] ?? 'none',
            ':event_fee'        => $data['event_fee'] ?? 0.00
        ]);
    }

    /* --------------------------
       Update an existing event
       -------------------------- */
    public function updateEvent(int $eventId, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE Event
            SET
                event_name        = :event_name,
                event_description = :event_description,
                event_location    = :event_location,
                event_date        = :event_date,
                capacity          = :capacity,
                event_condition   = :event_condition,
                event_fee         = :event_fee
            WHERE event_id = :event_id
        ");

        return $stmt->execute([
            ':event_name'        => $data['event_name'],
            ':event_description' => $data['event_description'],
            ':event_location'    => $data['event_location'],
            ':event_date'        => $data['event_date'],
            ':capacity'          => $data['capacity'],
            ':event_condition'   => $data['event_condition'],
            ':event_fee'         => $data['event_fee'],
            ':event_id'          => $eventId,
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

        $stmt = $this->pdo->prepare("
            UPDATE Event
            SET event_status = :status
            WHERE event_id = :id
        ");

        return $stmt->execute([
            ':status' => $status,
            ':id'     => $eventId
        ]);
    }

    /* --------------------------
       Visible event for normal users:
       - Club must be active
       - Event must be approved
       -------------------------- */
    public function findVisibleById(int $eventId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT e.*, c.club_name
            FROM Event e
            JOIN Club c ON e.club_id = c.club_id
            WHERE e.event_id = :id
              AND c.club_status = 'active'
              AND e.event_status = 'approved'
        ");
        $stmt->execute([':id' => $eventId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

}
