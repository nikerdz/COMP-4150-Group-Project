SELECT 
    u.user_id,
    u.first_name,
    u.last_name,
    COALESCE(e.executive_role, 'member') AS role
FROM Registration r
JOIN User u ON r.user_id = u.user_id
JOIN Event ev ON r.event_id = ev.event_id
LEFT JOIN Executive e 
  ON u.user_id = e.user_id 
 AND ev.club_id = e.club_id
WHERE r.event_id = ?
ORDER BY 
    CASE
        WHEN LOWER(COALESCE(e.executive_role, 'member')) IN ('admin','administrator','president','owner')
            THEN 0
        WHEN LOWER(COALESCE(e.executive_role, 'member')) <> 'member'
            THEN 1
        ELSE 2
    END,
    u.first_name ASC;
