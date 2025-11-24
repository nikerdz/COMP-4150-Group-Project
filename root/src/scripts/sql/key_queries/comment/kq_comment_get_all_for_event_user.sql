SELECT
    c.comment_id,
    c.user_id,
    c.event_id,
    c.comment_message,
    c.comment_date,
    CONCAT(u.first_name, ' ', u.last_name) AS user_name,
    e.event_status,
    cl.club_status
FROM Comments c
JOIN User u ON c.user_id = u.user_id
JOIN Event e ON c.event_id = e.event_id
JOIN Club cl ON e.club_id = cl.club_id
WHERE 
    c.event_id = ?
    AND e.event_status = 'approved'
    AND cl.club_status = 'active'
ORDER BY c.comment_date DESC;
