SELECT *
FROM Notification
WHERE user_id = ?
ORDER BY notification_timestamp DESC;
