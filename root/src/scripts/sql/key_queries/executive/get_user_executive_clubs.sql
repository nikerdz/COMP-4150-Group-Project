SELECT c.club_id, c.club_name, e.executive_role
FROM Executive e
JOIN Club c ON e.club_id = c.club_id
WHERE e.user_id = ?
ORDER BY c.club_name;