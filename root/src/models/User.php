<?php
require_once(__DIR__ . '/../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');

class User
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /* --------------------------
       Register a new user
       -------------------------- */
    public function register($data)
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
    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE user_email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
       Update user profile fields
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

        if ($data['year_of_study'] === null || $data['year_of_study'] === '') {
            $stmt->bindValue(':year_of_study', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':year_of_study', (int)$data['year_of_study'], PDO::PARAM_INT);
        }

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}