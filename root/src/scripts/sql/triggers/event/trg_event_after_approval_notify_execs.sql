DROP TRIGGER IF EXISTS trg_event_after_approval_notify_execs;
DELIMITER $$

CREATE TRIGGER trg_event_after_approval_notify_execs
AFTER UPDATE ON Event
FOR EACH ROW
BEGIN
    -- Only notify execs when event goes from pending → approved
    IF OLD.event_status = 'pending' AND NEW.event_status = 'approved' THEN
        
        INSERT IGNORE INTO Notification (
            user_id,
            event_id,
            notification_message,
            notification_type
        )
        SELECT ex.user_id,
               NEW.event_id,
               CONCAT('Your club event "', NEW.event_name, '" has been approved by an administrator.'),
               'new'
        FROM Executive ex
        WHERE ex.club_id = NEW.club_id;

    END IF;
END$$

DELIMITER ;
