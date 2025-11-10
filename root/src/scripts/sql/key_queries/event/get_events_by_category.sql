SELECT e.*
FROM Event e
JOIN Club c ON e.club_id = c.club_id
JOIN Club_Tags ct ON c.club_id = ct.club_id
WHERE ct.category_id = ?;