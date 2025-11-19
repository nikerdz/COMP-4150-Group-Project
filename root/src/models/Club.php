<?php
require_once(__DIR__ . '/../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');

class Club
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /* --------------------------
       Register a new club (for future use)
       -------------------------- */
    public function create(array $data): bool
    {
        $sql = "INSERT INTO Club 
                (club_name, club_email, club_description, creation_date, club_condition, club_status)
                VALUES (:name, :email, :description, :creation_date, :condition, :status)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':name'          => $data['club_name'],
            ':email'         => $data['club_email'] ?? null,
            ':description'   => $data['club_description'] ?? null,
            ':creation_date' => $data['creation_date'] ?? date('Y-m-d'),
            ':condition'     => $data['club_condition'] ?? 'none',
            ':status'        => $data['club_status'] ?? 'active',
        ]);
    }

    /* --------------------------
       Get one club by ID
       -------------------------- */
    public function findById(int $clubId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT c.*, GROUP_CONCAT(cat.category_name) AS categories
            FROM Club c
            LEFT JOIN Club_Tags ct ON c.club_id = ct.club_id
            LEFT JOIN Category cat ON ct.category_id = cat.category_id
            WHERE c.club_id = :id
            GROUP BY c.club_id
        ");
        $stmt->execute([':id' => $clubId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* --------------------------
       Get all categories (for filters)
       -------------------------- */
    public function getAllCategories(): array
    {
        $stmt = $this->pdo->query("
            SELECT category_id, category_name
            FROM Category
            ORDER BY category_name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* --------------------------
       Search clubs with filters
       -------------------------- */
    public function searchClubs(
        ?string $search,
        ?int $categoryId,
        ?string $condition,
        int $limit = 20,
        int $offset = 0
    ): array {
        $sql = "
            SELECT 
                c.*,
                GROUP_CONCAT(DISTINCT cat.category_name) AS categories
            FROM Club c
            LEFT JOIN Club_Tags ct ON c.club_id = ct.club_id
            LEFT JOIN Category cat ON ct.category_id = cat.category_id
            WHERE c.club_status = 'active'
        ";

        $params = [];

        // Search by name/description
        if (!empty($search)) {
            $sql .= " AND (c.club_name LIKE :search OR c.club_description LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        // Filter by category
        if (!empty($categoryId)) {
            $sql .= " AND ct.category_id = :catId";
            $params[':catId'] = $categoryId;
        }

        // Filter by club_condition (ignore "any")
        if (!empty($condition) && $condition !== 'any') {
            $sql .= " AND c.club_condition = :cond";
            $params[':cond'] = $condition;
        }

        $sql .= "
            GROUP BY c.club_id
            ORDER BY c.club_name ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);

        // Bind dynamic params
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // Bind limit/offset as ints (named placeholders)
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* --------------------------
       Get all clubs a user is a member of
       -------------------------- */
    public function getClubsForUser(int $userId): array
    {
        $sql = "
            SELECT
                c.*,
                m.membership_date,
                GROUP_CONCAT(DISTINCT cat.category_name) AS categories
            FROM Membership m
            JOIN Club c ON m.club_id = c.club_id
            LEFT JOIN Club_Tags ct ON c.club_id = ct.club_id
            LEFT JOIN Category cat ON ct.category_id = cat.category_id
            WHERE m.user_id = :uid
            GROUP BY c.club_id, m.membership_date
            ORDER BY c.club_name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
