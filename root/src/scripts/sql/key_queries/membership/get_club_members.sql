SELECT u.user_id, u.first_name, u.last_name
FROM Membership m
JOIN User u ON m.user_id = u.user_id
WHERE m.club_id = ?
ORDER BY u.last_name, u.first_name;