DROP TRIGGER IF EXISTS trg_comment_after_insert_notify_execs;
DELIMITER $$

CREATE TRIGGER trg_comment_after_insert_notify_execs
AFTER INSERT ON Comments
FOR EACH ROW
BEGIN
    INSERT IGNORE INTO Notification (user_id, event_id, notification_message, notification_type)
    SELECT e.user_id,
           NEW.event_id,
           CONCAT('New comment on "', ev.event_name, '"'),
           'update'
    FROM Executive e
    JOIN Event ev ON ev.club_id = e.club_id
    WHERE ev.event_id = NEW.event_id;
END$$

DELIMITER ;

