SELECT COUNT(*)
FROM Registration
WHERE user_id = ?
  AND event_id = ?;
