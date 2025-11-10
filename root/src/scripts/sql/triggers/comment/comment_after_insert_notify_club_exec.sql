CREATE TRIGGER trg_comment_after_insert_notify_club_exec
AFTER INSERT ON Comments
FOR EACH ROW
INSERT INTO Notification (user_id, event_id, notification_message, notification_type)
SELECT E.club_id, NEW.event_id, CONCAT('New comment on event ID ', NEW.event_id), 'update'
FROM Event E
WHERE E.event_id = NEW.event_id;