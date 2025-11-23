<?php
require_once(__DIR__ . '/../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');

class User
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /* --------------------------
       Register a new user
       -------------------------- */
    public function register(array $data): int
    {
        $stmt = $this->pdo->prepare("CALL sp_user_register(?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password'],
            $data['gender'],
            $data['faculty'],
            $data['level_of_study'],
            $data['year_of_study']
        ]);

        // Stored procedure returns a result set containing LAST_INSERT_ID()
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)$row['user_id'];
    }

    /* --------------------------
         Find user by email
       -------------------------- */
    public function findByEmail(string $email): ?array
    {
        // Load Key Query SQL file
        $sql = file_get_contents(KQ_URL . 'user/kq_user_find_by_email.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* --------------------------
       Find user by ID
       -------------------------- */
    public function findById(int $userId): ?array
    {
        // Load key query SQL
        $sql = file_get_contents(KQ_URL . 'user/kq_user_find_by_id.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* --------------------------
       Update basic profile fields
       -------------------------- */
    public function updateProfile(int $userId, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            CALL sp_user_update_profile(?, ?, ?, ?, ?, ?)
        ");

        // Convert null year_of_study properly for SP
        $year = $data['year_of_study'] !== null
            ? (int)$data['year_of_study']
            : null;

        return $stmt->execute([
            $userId,
            $data['first_name'],
            $data['last_name'],
            $data['faculty'],
            $data['level_of_study'],
            $year
        ]);
    }

    /* --------------------------
       Update password
       -------------------------- */
    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        $stmt = $this->pdo->prepare("CALL sp_user_update_password(?, ?)");
        return $stmt->execute([$userId, $hashedPassword]);
    }

    /* --------------------------
       Delete user (ON DELETE CASCADE
       will clean related rows)
       -------------------------- */
    public function deleteUser(int $userId): bool
    {
        $stmt = $this->pdo->prepare("CALL sp_user_delete(?)");
        return $stmt->execute([$userId]);
    }


    /* --------------------------
       Get interest category IDs
       for a user
       -------------------------- */
    public function getInterestCategoryIds(int $userId): array
    {
        // Load Key Query SQL file
        $sql = file_get_contents(KQ_URL . 'user/kq_user_get_interest_category_ids.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }


    /* --------------------------
       Get interest names for user
       -------------------------- */
    public function getInterestNames(int $userId): array
    {
        // Load Key Query SQL
        $sql = file_get_contents(KQ_URL . 'user/kq_user_get_interest_names.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }


    public function saveUserInterests(int $userId, array $categoryIds): void
    {
        if (empty($categoryIds)) return;

        $stmt = $this->pdo->prepare("CALL sp_user_save_interests(?, ?)");

        foreach ($categoryIds as $catId) {
            $catId = (int)$catId;
            if ($catId > 0) {
                $stmt->execute([$userId, $catId]);
            }
        }
    }


    /* --------------------------
       Replace user's interests with
       a new set
       -------------------------- */
    public function updateInterests(int $userId, array $categoryIds): void
    {
        // Clear existing interests
        $stmtClear = $this->pdo->prepare("CALL sp_user_clear_interests(?)");
        $stmtClear->execute([$userId]);

        // Re-add new interests
        if (!empty($categoryIds)) {
            $stmtInsert = $this->pdo->prepare("CALL sp_user_save_interests(?, ?)");

            foreach ($categoryIds as $catId) {
                $catId = (int)$catId;
                if ($catId > 0) {
                    $stmtInsert->execute([$userId, $catId]);
                }
            }
        }
    }


    public function getAllUsers(): array
    {
        $sql = file_get_contents(KQ_URL . 'user/kq_user_get_all_users.sql');

        return $this->pdo
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }


    public function searchUsers(string $search = '', string $status = 'all', ?int $excludeUserId = null): array
    {
        $stmt = $this->pdo->prepare("CALL sp_user_search(?, ?, ?)");

        $stmt->execute([
            $search,
            $status,
            $excludeUserId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function suspendUser(int $userId): bool
    {
        $stmt = $this->pdo->prepare("CALL sp_user_suspend(?)");
        return $stmt->execute([$userId]);
    }


    public function activateUser(int $userId): bool
    {
        $stmt = $this->pdo->prepare("CALL sp_user_activate(?)");
        return $stmt->execute([$userId]);
    }

}