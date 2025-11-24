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
        // Fetch user and club info for PHP-side restriction logic
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
            return false;
        }

        // Restriction logic stays in PHP
        switch ($data['club_condition']) {
            case 'women_only':
                if ($data['gender'] !== 'F') {
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
                break;
        }

        // Use stored procedure to insert membership
        try {
            $stmtInsert = $this->pdo->prepare("CALL sp_membership_join(?, ?)");
            return $stmtInsert->execute([$userId, $clubId]);

        } catch (PDOException $e) {
            return false; // already joined or DB error
        }
    }


    public function leave(int $userId, int $clubId): bool
    {
        try {
            $stmt = $this->pdo->prepare("CALL sp_membership_leave(?, ?)");
            return $stmt->execute([$userId, $clubId]);

        } catch (PDOException $e) {
            return false;
        }
    }


    public function getClubsForUser(int $userId): array
    {
        // Load key query
        $sql = file_get_contents(KQ_URL . 'membership/kq_membership_get_clubs_for_user.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* --------------------------
       Get a user's membership in a club
       Returns null if not a member
    -------------------------- */
    public function getMembership(int $clubId, int $userId): ?array
    {
        // Load KQ SQL file
        $sql = file_get_contents(KQ_URL . 'membership/kq_membership_get_membership.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clubId, $userId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    /* --------------------------
       Get all members of a club
       Returns array of users with their role (member/exec) and join date
    -------------------------- */
    public function getClubMembers(int $clubId): array
    {
        $sql = file_get_contents(KQ_URL . 'membership/kq_membership_get_club_members.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clubId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
