DROP TRIGGER IF EXISTS trg_event_after_approval_notify_members;
DELIMITER $$

CREATE TRIGGER trg_event_after_approval_notify_members
AFTER UPDATE ON Event
FOR EACH ROW
BEGIN
    IF OLD.event_status = 'pending' AND NEW.event_status = 'approved' THEN

        INSERT IGNORE INTO Notification (
            user_id,
            event_id,
            notification_message,
            notification_type
        )
        SELECT 
            m.user_id,
            NEW.event_id,
            
            -- Custom message based on role (exec vs member)
            CASE 
                WHEN ex.user_id IS NOT NULL THEN 
                    CONCAT('A new event "', NEW.event_name, '" has been approved for your club.')
                ELSE
                    CONCAT(
                        (SELECT club_name FROM Club WHERE club_id = NEW.club_id),
                        ' has a new event: ',
                        NEW.event_name,
                        '. Check it out!'
                    )
            END AS notification_message,

            'new' AS notification_type

        FROM Membership m
        LEFT JOIN Executive ex
            ON ex.user_id = m.user_id 
            AND ex.club_id = m.club_id
        WHERE m.club_id = NEW.club_id;

    END IF;
END$$

DELIMITER ;
