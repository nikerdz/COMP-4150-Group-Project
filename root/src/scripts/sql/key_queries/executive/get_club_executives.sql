SELECT u.user_id, u.first_name, u.last_name, e.executive_role
FROM Executive e
JOIN User u ON e.user_id = u.user_id
WHERE e.club_id = ?
ORDER BY e.executive_role, u.last_name, u.first_name;