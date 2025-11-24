SELECT *
FROM Payment
WHERE registration_id = :rid
LIMIT 1;