DROP TRIGGER IF EXISTS trg_event_after_update_notify_registrants;
DELIMITER $$

CREATE TRIGGER trg_event_after_update_notify_registrants
AFTER UPDATE ON Event
FOR EACH ROW
BEGIN
    IF NEW.event_name <> OLD.event_name
       OR NEW.event_location <> OLD.event_location
       OR NEW.event_date <> OLD.event_date
       OR NEW.event_description <> OLD.event_description THEN

        INSERT IGNORE INTO Notification (user_id, event_id, notification_message, notification_type)
        SELECT r.user_id,
               NEW.event_id,
               CONCAT('The event "', NEW.event_name, '" has been updated.'),
               'update'
        FROM Registration r
        WHERE r.event_id = NEW.event_id;
    END IF;
END$$

DELIMITER ;
