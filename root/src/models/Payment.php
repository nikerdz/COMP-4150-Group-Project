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
        try {
            $stmt = $this->pdo->prepare("CALL sp_payment_create_pending(:rid, :amount, :method)");

            return $stmt->execute([
                ':rid'    => $registrationId,
                ':amount' => $amount,
                ':method' => $method
            ]);

        } catch (PDOException $e) {
            return false; // duplicate or failure
        }
    }


    /* ----------------------------------------------------
       Mark payment as completed
       ---------------------------------------------------- */
    public function markCompleted(int $registrationId): bool
    {
        try {
            $stmt = $this->pdo->prepare("CALL sp_payment_mark_completed(:rid)");
            return $stmt->execute([':rid' => $registrationId]);
        } catch (PDOException $e) {
            return false;
        }
    }


    /* ----------------------------------------------------
       Mark payment as refunded
       ---------------------------------------------------- */
    public function refund(int $registrationId): bool
    {
        try {
            $stmt = $this->pdo->prepare("CALL sp_payment_refund(:rid)");
            return $stmt->execute([':rid' => $registrationId]);
        } catch (PDOException $e) {
            return false;
        }
    }


    /* ----------------------------------------------------
       Get payment record for a registration
       ---------------------------------------------------- */
    public function getPaymentByRegistration(int $registrationId): ?array
    {
        $sql = file_get_contents(KQ_URL . 'payment/kq_payment_get_by_registration.sql');
        $stmt = $this->pdo->prepare($sql);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* ----------------------------------------------------
       Delete payment when unregistering (if ever needed)
       ---------------------------------------------------- */
    public function deletePaymentByRegistration(int $registrationId): bool
    {
        try {
            $stmt = $this->pdo->prepare("CALL sp_payment_delete_by_registration(:rid)");
            return $stmt->execute([':rid' => $registrationId]);
        } catch (PDOException $e) {
            error_log("SP payment delete error: " . $e->getMessage());
            return false;
        }
    }


    public function countCompletedPayments(): int
    {
        $sql = file_get_contents(KQ_URL . 'payment/kq_payment_count_completed.sql');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }


    public function getTotalRevenue() {
        $sql = file_get_contents(KQ_URL . 'payment/kq_payment_total_revenue.sql');
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return (float)$stmt->fetchColumn();
    }

}
