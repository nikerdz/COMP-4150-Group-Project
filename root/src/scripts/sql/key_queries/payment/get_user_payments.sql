SELECT p.*, e.event_name
FROM Payment p
JOIN Registration r ON p.registration_id = r.registration_id
JOIN Event e ON r.event_id = e.event_id
WHERE r.user_id = ?
ORDER BY p.payment_date DESC;