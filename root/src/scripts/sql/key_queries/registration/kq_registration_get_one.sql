SELECT *
FROM Registration
WHERE event_id = ?
  AND user_id = ?
LIMIT 1;
