DELIMITER $$

CREATE TRIGGER trg_event_after_update_send_new_notification
AFTER UPDATE ON Event
FOR EACH ROW
BEGIN
    -- Only proceed if key details changed
    IF NEW.event_name <> OLD.event_name
       OR NEW.event_location <> OLD.event_location
       OR NEW.event_date <> OLD.event_date THEN

        -- Insert a notification for every user registered for the event
        INSERT INTO Notification (user_id, event_id, notification_message, notification_type)
        SELECT r.user_id,
               NEW.event_id,
               CONCAT('The event "', NEW.event_name, '" has been updated.'),
               'update'
        FROM Registration r
        WHERE r.event_id = NEW.event_id AND r.rsvp = TRUE;
    END IF;
END$$

DELIMITER ;