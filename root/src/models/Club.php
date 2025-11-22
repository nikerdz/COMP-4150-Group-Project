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
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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

        // --------------------
        // Dynamic filters
        // --------------------
        if (!empty($search)) {
            $sql .= " AND (c.club_name LIKE :search_name OR c.club_description LIKE :search_desc)";
        }

        if (!empty($categoryId)) {
            $sql .= " AND ct.category_id = :catId";
        }

        if (!empty($condition) && $condition !== 'any') {
            $sql .= " AND c.club_condition = :cond";
        }

        $sql .= "
            GROUP BY c.club_id
            ORDER BY c.club_name ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);

        // --------------------
        // Bind parameters
        // --------------------
        if (!empty($search)) {
            $like = '%' . $search . '%';
            $stmt->bindValue(':search_name', $like, PDO::PARAM_STR);
            $stmt->bindValue(':search_desc', $like, PDO::PARAM_STR);
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
       Get category IDs for a club
       -------------------------- */
    public function getClubCategoryIds(int $clubId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT category_id
            FROM Club_Tags
            WHERE club_id = :id
        ");
        $stmt->execute([':id' => $clubId]);

        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'category_id');
    }

    /* --------------------------
       Update club information
       -------------------------- */
    public function updateClub(int $clubId, array $data): bool
    {
        try {
            // Begin transaction
            $this->pdo->beginTransaction();

            // --------------------
            // Update main club table
            // --------------------
            $sql = "
                UPDATE Club
                SET club_name = :name,
                    club_email = :email,
                    club_description = :description,
                    club_condition = :condition
                WHERE club_id = :id
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':name'        => $data['club_name'],
                ':email'       => $data['club_email'] ?? null,
                ':description' => $data['club_description'] ?? null,
                ':condition'   => $data['club_condition'] ?? 'none',
                ':id'          => $clubId,
            ]);

            // --------------------
            // Update tags
            // --------------------
            if (isset($data['tags']) && is_array($data['tags'])) {
                // Delete old tags
                $delStmt = $this->pdo->prepare("DELETE FROM Club_Tags WHERE club_id = :id");
                $delStmt->execute([':id' => $clubId]);

                // Insert new tags
                if (!empty($data['tags'])) {
                    $insertStmt = $this->pdo->prepare("
                        INSERT INTO Club_Tags (club_id, category_id) VALUES (:club_id, :cat_id)
                    ");
                    foreach ($data['tags'] as $catId) {
                        $insertStmt->execute([
                            ':club_id' => $clubId,
                            ':cat_id'  => $catId,
                        ]);
                    }
                }
            }

            // Commit transaction
            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Failed to update club: " . $e->getMessage());
            return false;
        }
    }

    /* --------------------------
       Delete a club completely
       -------------------------- */
    public function deleteClub(int $clubId): bool
    {
        try {
            // Begin transaction for safety
            $this->pdo->beginTransaction();

            // Delete related tags
            $stmt = $this->pdo->prepare("DELETE FROM Club_Tags WHERE club_id = :id");
            $stmt->execute([':id' => $clubId]);

            // Delete related memberships
            $stmt = $this->pdo->prepare("DELETE FROM Membership WHERE club_id = :id");
            $stmt->execute([':id' => $clubId]);

            // Delete related events (correct table name: Event)
            $stmt = $this->pdo->prepare("DELETE FROM Event WHERE club_id = :id");
            $stmt->execute([':id' => $clubId]);

            // Finally delete the club itself
            $stmt = $this->pdo->prepare("DELETE FROM Club WHERE club_id = :id");
            $stmt->execute([':id' => $clubId]);

            // Commit all changes
            $this->pdo->commit();

            // If at least one club row was deleted, consider it success
            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            // Rollback on error
            $this->pdo->rollBack();
            error_log("Failed to delete club: " . $e->getMessage());
            return false;
        }
    }

    public function searchClubsAdmin(string $search = '', string $status = 'active'): array
    {
        $sql = "
            SELECT *
            FROM Club
            WHERE club_status = :status
        ";

        $params = [':status' => $status];

        if ($search !== '') {
            $sql .= " AND (
                club_name LIKE :q OR
                club_description LIKE :q
            )";
            $params[':q'] = "%$search%";
        }

        $sql .= " ORDER BY club_name ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
