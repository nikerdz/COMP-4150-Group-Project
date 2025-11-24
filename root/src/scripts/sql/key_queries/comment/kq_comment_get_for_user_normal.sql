SELECT 
    c.comment_id,
    c.comment_message,
    c.comment_date,
    c.event_id,
    e.event_name,
    e.event_status,
    cl.club_status
FROM Comments c
JOIN Event e ON c.event_id = e.event_id
JOIN Club cl ON e.club_id = cl.club_id
WHERE c.user_id = ?
  AND e.event_status = 'approved'
  AND cl.club_status = 'active'
ORDER BY c.comment_date DESC
LIMIT ?;
