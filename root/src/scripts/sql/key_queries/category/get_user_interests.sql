SELECT cat.category_id, cat.category_name
FROM User_Interests ui
JOIN Category cat ON ui.category_id = cat.category_id
WHERE ui.user_id = ?
ORDER BY cat.category_name ASC;