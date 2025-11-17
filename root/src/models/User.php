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
}
