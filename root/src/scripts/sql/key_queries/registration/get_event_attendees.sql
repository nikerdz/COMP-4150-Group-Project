SELECT u.user_id, u.first_name, u.last_name, r.rsvp
FROM Registration r
JOIN User u ON r.user_id = u.user_id
WHERE r.event_id = ?
ORDER BY u.last_name, u.first_name;