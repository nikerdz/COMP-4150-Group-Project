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



DROP TRIGGER IF EXISTS trg_payment_after_insert_notify_user;
DELIMITER $$

CREATE TRIGGER trg_payment_after_insert_notify_user
AFTER INSERT ON Payment
FOR EACH ROW
BEGIN
    INSERT IGNORE INTO Notification (user_id, event_id, notification_message, notification_type)
    SELECT r.user_id,
           r.event_id,
           CONCAT('Your payment of $', NEW.amount, ' has been received.'),
           'announcement'
    FROM Registration r
    WHERE r.registration_id = NEW.registration_id;
END$$

DELIMITER ;
