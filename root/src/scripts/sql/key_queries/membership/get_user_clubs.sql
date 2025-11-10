SELECT c.club_id, c.club_name
FROM Membership m
JOIN Club c ON m.club_id = c.club_id
WHERE m.user_id = ?
ORDER BY c.club_name;