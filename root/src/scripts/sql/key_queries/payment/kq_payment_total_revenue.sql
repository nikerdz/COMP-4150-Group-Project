SELECT SUM(amount) AS total_revenue
FROM Payment
WHERE payment_status = 'completed';
