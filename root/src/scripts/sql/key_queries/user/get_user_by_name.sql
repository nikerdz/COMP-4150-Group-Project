SELECT * FROM User
WHERE first_name LIKE ? OR last_name LIKE ?;
ORDER BY last_name, first_name;