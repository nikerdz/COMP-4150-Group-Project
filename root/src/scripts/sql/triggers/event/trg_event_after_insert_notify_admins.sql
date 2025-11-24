DROP TRIGGER IF EXISTS trg_event_after_insert_notify_admins;
DELIMITER $$

CREATE TRIGGER trg_event_after_insert_notify_admins
AFTER INSERT ON Event
FOR EACH ROW
BEGIN
    -- Only notify admins if the event is pending approval
    IF NEW.event_status = 'pending' THEN

        INSERT IGNORE INTO Notification (
            user_id, 
            event_id, 
            notification_message, 
            notification_type
        )
        SELECT 
            u.user_id,
            NEW.event_id,
            CONCAT(
                'A new event "', NEW.event_name, 
                '" requires your approval.'
            ),
            'new'
        FROM User u
        WHERE u.user_type = 'admin';

    END IF;
END$$

DELIMITER ;
