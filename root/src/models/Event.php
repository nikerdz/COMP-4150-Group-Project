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

        $params = [];

        // Search by event name / description / club name
        if (!empty($search)) {
            $sql .= "
                AND (
                    e.event_name LIKE :search
                    OR e.event_description LIKE :search
                    OR c.club_name LIKE :search
                )
            ";
            $params[':search'] = '%' . $search . '%';
        }

        // Filter by category via Club_Tags
        if (!empty($categoryId)) {
            $sql .= " AND ct.category_id = :catId";
            $params[':catId'] = $categoryId;
        }

        // Filter by event_condition (ignore "any")
        if (!empty($condition) && $condition !== 'any') {
            $sql .= " AND e.event_condition = :cond";
            $params[':cond'] = $condition;
        }

        $sql .= "
            GROUP BY e.event_id
            ORDER BY e.event_date ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* --------------------------
       Upcoming events user is registered for
       -------------------------- */
    public function getUpcomingEventsForUser(int $userId, int $limit = 6): array
    {
        $sql = "
            SELECT
                e.*,
                c.club_name,
                r.registration_date
            FROM Registration r
            JOIN Event e ON r.event_id = e.event_id
            JOIN Club c  ON e.club_id = c.club_id
            WHERE r.user_id = :uid
              AND (e.event_status <> 'cancelled')
              AND (e.event_date IS NULL OR e.event_date >= NOW())
            ORDER BY e.event_date ASC
            LIMIT :limit
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
