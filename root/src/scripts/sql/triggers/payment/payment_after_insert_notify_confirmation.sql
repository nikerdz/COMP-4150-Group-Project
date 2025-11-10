DELIMITER $$

CREATE TRIGGER payment_after_insert_notify_confirmation
AFTER INSERT ON Payment
FOR EACH ROW
BEGIN
    -- Insert a notification for the user who made the payment
    INSERT INTO Notification (user_id, event_id, notification_message, notification_type)
    SELECT R.user_id, R.event_id,
           CONCAT('Your payment of $', NEW.amount, ' has been received.'),
           'announcement'
    FROM Registration R
    WHERE R.registration_id = NEW.registration_id;
END$$

DELIMITER ;