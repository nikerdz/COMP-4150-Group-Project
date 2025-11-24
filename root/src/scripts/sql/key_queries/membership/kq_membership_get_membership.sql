SELECT 
    m.user_id, 
    m.club_id, 
    m.membership_date,
    COALESCE(e.executive_role, 'member') AS role
FROM Membership m
LEFT JOIN Executive e 
    ON m.user_id = e.user_id 
   AND m.club_id = e.club_id
WHERE m.club_id = ?
  AND m.user_id = ?
LIMIT 1;
