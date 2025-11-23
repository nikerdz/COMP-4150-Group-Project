SELECT 
    c.*, 
    GROUP_CONCAT(cat.category_name) AS categories
FROM Club c
LEFT JOIN Club_Tags ct ON c.club_id = ct.club_id
LEFT JOIN Category cat ON ct.category_id = cat.category_id
WHERE c.club_id = ?
  AND c.club_status = 'active'
GROUP BY c.club_id;
