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
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare("CALL sp_club_create(?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $data['club_name'],
            $data['club_email'] ?? null,
            $data['club_description'] ?? null,
            date('Y-m-d'),
            $data['club_condition'] ?? 'none',
            $data['club_status'] ?? 'active',
        ]);

        // Stored procedure returns: SELECT LAST_INSERT_ID() AS club_id;
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['club_id'];
    }



    /* --------------------------
       Get one club by ID
       -------------------------- */
    public function findById(int $clubId): ?array
    {
        $sql = file_get_contents(KQ_URL . 'club/kq_club_find_by_id.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clubId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    /* --------------------------
       Get all categories (for filters)
       -------------------------- */
    public function getAllCategories(): array
    {
        $sql = file_get_contents(KQ_URL . 'club/kq_club_get_all_categories.sql');

        $stmt = $this->pdo->query($sql);
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
        $stmt = $this->pdo->prepare("CALL sp_club_search(?, ?, ?, ?, ?)");

        $stmt->execute([
            $search ?? '',
            $categoryId ?? null,
            $condition ?? 'any',
            $limit,
            $offset
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* --------------------------
       Get category IDs for a club
       -------------------------- */
    public function getClubCategoryIds(int $clubId): array
    {
        $sql = file_get_contents(KQ_URL . 'club/kq_club_get_category_ids.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clubId]);

        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'category_id');
    }


    /* --------------------------
       Update club information
       -------------------------- */
    public function updateClub(int $clubId, array $data): bool
    {
        try {
            // Update main club table
            $stmtMain = $this->pdo->prepare("CALL sp_club_update_main(?, ?, ?, ?, ?)");
            $stmtMain->execute([
                $clubId,
                $data['club_name'],
                $data['club_email'] ?? null,
                $data['club_description'] ?? null,
                $data['club_condition'] ?? 'none'
            ]);

            // Clear existing tags
            $stmtClear = $this->pdo->prepare("CALL sp_club_clear_tags(?)");
            $stmtClear->execute([$clubId]);

            // Insert new tags
            if (!empty($data['tags']) && is_array($data['tags'])) {
                $stmtAddTag = $this->pdo->prepare("CALL sp_club_add_tag(?, ?)");

                foreach ($data['tags'] as $catId) {
                    $stmtAddTag->execute([$clubId, (int)$catId]);
                }
            }

            return true;

        } catch (PDOException $e) {
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
            $stmt = $this->pdo->prepare("CALL sp_club_delete(?)");
            $stmt->execute([$clubId]);

            return true;

        } catch (PDOException $e) {
            error_log("Failed to delete club: " . $e->getMessage());
            return false;
        }
    }


    public function searchClubsAdmin(string $search = '', string $status = 'all'): array
    {
        $stmt = $this->pdo->prepare("CALL sp_club_search_admin(?, ?)");

        $stmt->execute([
            $search,
            $status
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function findVisibleById(int $clubId): ?array
    {
        $sql = file_get_contents(KQ_URL . 'club/kq_club_find_visible_by_id.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clubId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

}
