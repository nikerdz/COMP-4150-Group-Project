SELECT 
    e.*,
    c.club_name
FROM Event e
JOIN Club c ON e.club_id = c.club_id
WHERE e.event_id = ?;
