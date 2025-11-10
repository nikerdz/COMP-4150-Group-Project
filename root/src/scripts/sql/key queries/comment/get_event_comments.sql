SELECT c.comment_id, u.first_name, u.last_name, c.comment_message, c.comment_date
FROM Comments c
JOIN User u ON c.user_id = u.user_id
WHERE c.event_id = ?
ORDER BY c.comment_date ASC;