SELECT 
    u.user_id, 
    u.first_name, 
    u.last_name, 
    COALESCE(e.executive_role, 'member') AS role,
    m.membership_date
FROM Membership m
JOIN User u 
    ON m.user_id = u.user_id
LEFT JOIN Executive e 
    ON m.user_id = e.user_id 
   AND m.club_id = e.club_id
WHERE m.club_id = ?
ORDER BY role DESC, u.first_name ASC;
