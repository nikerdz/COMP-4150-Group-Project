DELIMITER $$

CREATE TRIGGER event_after_insert_notify_members
AFTER INSERT ON Event
FOR EACH ROW
BEGIN
    -- Insert a notification for each member of the club
    INSERT INTO Notification (user_id, event_id, notification_message, notification_type)
    SELECT M.user_id, NEW.event_id,
           CONCAT('A new event "', NEW.event_name, '" has been created for your club.'),
           'announcement'
    FROM Membership M
    WHERE M.club_id = NEW.club_id;
END$$

DELIMITER ;