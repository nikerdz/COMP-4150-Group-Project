SELECT DISTINCT c.club_id, c.club_name
FROM User_Interests ui
JOIN Club_Tags ct ON ui.category_id = ct.category_id
JOIN Club c ON ct.club_id = c.club_id
WHERE ui.user_id = ?
ORDER BY c.club_name ASC;