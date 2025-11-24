SELECT 
    e.*, 
    c.club_name
FROM Event e
JOIN Club c ON e.club_id = c.club_id
WHERE e.event_id = ?
  AND c.club_status = 'active'
  AND e.event_status = 'approved';
