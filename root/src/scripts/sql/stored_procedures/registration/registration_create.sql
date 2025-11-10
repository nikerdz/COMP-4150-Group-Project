DELIMITER $$

CREATE PROCEDURE sp_create_registration(
    IN p_user_id INT,
    IN p_event_id INT,
    IN p_rsvp BOOLEAN
)
BEGIN
    INSERT INTO Registration (user_id, event_id, rsvp)
    VALUES (p_user_id, p_event_id, p_rsvp);
END$$

DELIMITER ;
