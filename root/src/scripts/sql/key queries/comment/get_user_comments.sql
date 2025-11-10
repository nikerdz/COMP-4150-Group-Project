SELECT c.comment_id, e.event_name, c.comment_message, c.comment_date
FROM Comments c
JOIN Event e ON c.event_id = e.event_id
WHERE c.user_id = ?
ORDER BY c.comment_date DESC;
