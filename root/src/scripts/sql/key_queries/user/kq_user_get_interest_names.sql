SELECT c.category_name
FROM User_Interests ui
JOIN Category c ON ui.category_id = c.category_id
WHERE ui.user_id = ?
ORDER BY c.category_name ASC;
