DROP PROCEDURE IF EXISTS sp_notification_create;
DELIMITER $$

CREATE PROCEDURE sp_notification_create(
    IN p_user_id INT,
    IN p_event_id INT,
    IN p_message TEXT,
    IN p_type ENUM('reminder','new','update')
)
BEGIN
    INSERT INTO Notification (
        user_id,
        event_id,
        notification_message,
        notification_type
    )
    VALUES (
        p_user_id,
        p_event_id,
        p_message,
        p_type
    );
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_notification_mark_read;
DELIMITER $$

CREATE PROCEDURE sp_notification_mark_read(
    IN p_notif_id INT
)
BEGIN
    UPDATE Notification
    SET notification_status = 'read'
    WHERE notification_id = p_notif_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_notification_mark_all_read;
DELIMITER $$

CREATE PROCEDURE sp_notification_mark_all_read(
    IN p_user_id INT
)
BEGIN
    UPDATE Notification
    SET notification_status = 'read'
    WHERE user_id = p_user_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_notification_mark_unread;
DELIMITER $$

CREATE PROCEDURE sp_notification_mark_unread(
    IN p_notif_id INT
)
BEGIN
    UPDATE Notification
    SET notification_status = 'unread'
    WHERE notification_id = p_notif_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_notification_set_status;
DELIMITER $$

CREATE PROCEDURE sp_notification_set_status(
    IN p_notif_id INT,
    IN p_status   ENUM('read', 'unread')
)
BEGIN
    UPDATE Notification
    SET notification_status = p_status
    WHERE notification_id = p_notif_id;
END $$

DELIMITER ;
