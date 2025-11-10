SELECT c.*
FROM Club c
JOIN Club_Tags ct ON c.club_id = ct.club_id
JOIN Category cat ON ct.category_id = cat.category_id
WHERE cat.category_id = ?
ORDER BY c.club_name;