SELECT 
    c.*,
    m.membership_date,
    COALESCE(e.executive_role, 'member') AS user_role,
    CASE
        WHEN LOWER(COALESCE(e.executive_role, 'member')) IN ('admin','administrator','president','owner')
            THEN 0
        WHEN LOWER(COALESCE(e.executive_role, 'member')) <> 'member'
            THEN 1
        ELSE 2
    END AS role_priority
FROM Membership m
JOIN Club c 
    ON m.club_id = c.club_id
LEFT JOIN Executive e
    ON m.user_id = e.user_id 
   AND m.club_id = e.club_id
WHERE m.user_id = ?
  AND c.club_status = 'active'
ORDER BY role_priority ASC, c.club_name ASC;
