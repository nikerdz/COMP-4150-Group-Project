<?php
/*
 Handles:
 - create pending payment
 - update payment status
 - get payment info by registration
 - delete payment
*/

require_once(__DIR__ . '/../config/constants.php');
require_once(CONFIG_PATH . 'db_config.php');

class Payment
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /* ----------------------------------------------------
       Create a pending payment for a registration
       ---------------------------------------------------- */
    public function createPending(int $registrationId, float $amount, string $method = 'cash'): bool
    {
        $sql = "
            INSERT INTO Payment (registration_id, amount, payment_status, payment_method)
            VALUES (:rid, :amount, 'pending', :method)
        ";

        $stmt = $this->pdo->prepare($sql);

        try {
            return $stmt->execute([
                ':rid'    => $registrationId,
                ':amount' => $amount,
                ':method' => $method
            ]);
        } catch (PDOException $e) {
            // Avoid duplicate payments
            return false;
        }
    }

    /* ----------------------------------------------------
       Mark payment as completed
       ---------------------------------------------------- */
    public function markCompleted(int $registrationId): bool
    {
        $sql = "
            UPDATE Payment
            SET payment_status = 'completed', payment_date = NOW()
            WHERE registration_id = :rid
        ";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':rid' => $registrationId]);
    }

    /* ----------------------------------------------------
       Mark payment as refunded
       ---------------------------------------------------- */
    public function refund(int $registrationId): bool
    {
        $sql = "
            UPDATE Payment
            SET payment_status = 'refunded', payment_date = NOW()
            WHERE registration_id = :rid
        ";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':rid' => $registrationId]);
    }

    /* ----------------------------------------------------
       Get payment record for a registration
       ---------------------------------------------------- */
    public function getPaymentByRegistration(int $registrationId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM Payment
            WHERE registration_id = :rid
            LIMIT 1
        ");
        $stmt->execute([':rid' => $registrationId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* ----------------------------------------------------
       Delete payment when unregistering (if ever needed)
       ---------------------------------------------------- */
    public function deletePaymentByRegistration(int $registrationId): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM Payment
            WHERE registration_id = :rid
        ");

        return $stmt->execute([':rid' => $registrationId]);
    }
}
