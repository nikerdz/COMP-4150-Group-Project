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
