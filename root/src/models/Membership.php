<?php
/*
Handles:
join club
leave club
check membership
get clubs user belongs to
*/

class Membership
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function join(int $userId, int $clubId): bool
    {
        // Fetch user and club info
        $stmt = $this->pdo->prepare("
            SELECT u.gender, u.level_of_study, u.year_of_study, c.club_condition
            FROM User u
            CROSS JOIN Club c
            WHERE u.user_id = :uid AND c.club_id = :cid
            LIMIT 1
        ");
        $stmt->execute([':uid' => $userId, ':cid' => $clubId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return false; // user or club not found
        }

        $condition = $data['club_condition'];

        // Check restrictions
        switch ($condition) {
            case 'women_only':
                if (strtolower($data['gender']) !== 'female') {
                    return false;
                }
                break;
            case 'undergrad_only':
                if (strtolower($data['level_of_study']) !== 'undergraduate') {
                    return false;
                }
                break;
            case 'first_year_only':
                if ((int)$data['year_of_study'] !== 1) {
                    return false;
                }
                break;
            case 'none':
            default:
                // No restriction
                break;
        }

        // Insert membership
        $stmt = $this->pdo->prepare("
            INSERT INTO Membership (user_id, club_id)
            VALUES (:uid, :cid)
        ");

        try {
            return $stmt->execute([':uid' => $userId, ':cid' => $clubId]);
        } catch (PDOException $e) {
            return false; // Already joined or DB error
        }
    }

    public function leave(int $userId, int $clubId): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM Membership 
            WHERE user_id = :uid AND club_id = :cid
        ");
        return $stmt->execute([':uid' => $userId, ':cid' => $clubId]);
    }

    public function getClubsForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                c.*, 
                m.membership_date 
            FROM Membership m
            JOIN Club c ON m.club_id = c.club_id
            WHERE m.user_id = :uid
        ");

        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* --------------------------
       Get a user's membership in a club
       Returns null if not a member
    -------------------------- */
    public function getMembership(int $clubId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT m.user_id, m.club_id, m.membership_date,
                   COALESCE(e.executive_role, 'member') AS role
            FROM Membership m
            LEFT JOIN Executive e 
              ON m.user_id = e.user_id AND m.club_id = e.club_id
            WHERE m.club_id = :clubId AND m.user_id = :userId
            LIMIT 1
        ");
        $stmt->execute([
            ':clubId' => $clubId,
            ':userId' => $userId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* --------------------------
       Get all members of a club
       Returns array of users with their role (member/exec) and join date
    -------------------------- */
    public function getClubMembers(int $clubId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT u.user_id, u.first_name, u.last_name, 
                   COALESCE(e.executive_role, 'member') AS role,
                   m.membership_date
            FROM Membership m
            JOIN User u ON m.user_id = u.user_id
            LEFT JOIN Executive e 
              ON m.user_id = e.user_id AND m.club_id = e.club_id
            WHERE m.club_id = :clubId
            ORDER BY role DESC, u.first_name ASC
        ");
        $stmt->execute([':clubId' => $clubId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
