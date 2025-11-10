SELECT * FROM Notification
WHERE user_id = ? AND notification_status = 'unread'
ORDER BY notification_timestamp DESC;