SELECT
    comment_id,
    user_id,
    event_id,
    comment_message,
    comment_date
FROM Comments
WHERE comment_id = ?
LIMIT 1;
