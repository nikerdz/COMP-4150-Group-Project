SELECT 
    e.*, 
    c.club_name,
    r.registration_date
FROM Registration r
JOIN Event e ON r.event_id = e.event_id
JOIN Club c ON e.club_id = c.club_id
WHERE r.user_id = ?
  AND e.event_status = 'approved'
  AND c.club_status = 'active'
  AND (e.event_date >= NOW() OR e.event_date IS NULL)
ORDER BY e.event_date ASC
LIMIT ?;
