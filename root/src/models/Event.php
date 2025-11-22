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
       Get one event by ID
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
       Search events with filters
       -------------------------- */
    public function searchEvents(
        ?string $search,
        ?int $categoryId,
        ?string $condition,
        int $limit = 20,
        int $offset = 0
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
        WHERE e.event_status <> 'cancelled'
    ";

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
       Create a new event
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
}
