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
    public function register($data): bool
    {
        $sql = "INSERT INTO User 
                (first_name, last_name, user_email, user_password, faculty, year_of_study)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password'],
            $data['faculty'],
            $data['year_of_study']
        ]);
    }

    /* --------------------------
         Find user by email
       -------------------------- */
    public function findByEmail($email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE user_email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* --------------------------
       Find user by ID
       -------------------------- */
    public function findById(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE user_id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* --------------------------
       Update basic profile fields
       -------------------------- */
    public function updateProfile(int $userId, array $data): bool
    {
        $sql = "UPDATE User
                SET first_name      = :first_name,
                    last_name       = :last_name,
                    faculty         = :faculty,
                    level_of_study  = :level_of_study,
                    year_of_study   = :year_of_study
                WHERE user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':first_name', $data['first_name']);
        $stmt->bindValue(':last_name',  $data['last_name']);
        $stmt->bindValue(':faculty',    $data['faculty']);
        $stmt->bindValue(':level_of_study', $data['level_of_study']);

        if (array_key_exists('year_of_study', $data) && $data['year_of_study'] !== null) {
            $stmt->bindValue(':year_of_study', (int)$data['year_of_study'], PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':year_of_study', null, PDO::PARAM_NULL);
        }

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /* --------------------------
       Get interest category IDs for a user
       -------------------------- */
    public function getInterestCategoryIds(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT category_id
            FROM User_Interests
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);

        // Returns array of ints like [1, 3, 4]
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /* --------------------------
       Get interest names (Category.category_name) for a user
       -------------------------- */
    public function getInterestNames(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT c.category_name
            FROM User_Interests ui
            JOIN Category c ON ui.category_id = c.category_id
            WHERE ui.user_id = ?
            ORDER BY c.category_name ASC
        ");
        $stmt->execute([$userId]);

        // Returns array of strings like ["Academic", "Sports"]
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /* --------------------------
       Replace user's interests with a new set
       -------------------------- */
    public function updateInterests(int $userId, array $categoryIds): void
    {
        $this->pdo->beginTransaction();

        // Remove old interests
        $del = $this->pdo->prepare("DELETE FROM User_Interests WHERE user_id = ?");
        $del->execute([$userId]);

        // Insert new ones (if any)
        if (!empty($categoryIds)) {
            $ins = $this->pdo->prepare("
                INSERT INTO User_Interests (user_id, category_id)
                VALUES (?, ?)
            ");

            foreach ($categoryIds as $catId) {
                $catId = (int)$catId;
                if ($catId > 0) {
                    $ins->execute([$userId, $catId]);
                }
            }
        }

        $this->pdo->commit();
    }
}
