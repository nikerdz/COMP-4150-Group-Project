SELECT COUNT(*) AS completed_count
FROM Payment
WHERE payment_status = 'completed';
