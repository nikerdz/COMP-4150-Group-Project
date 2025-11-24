
DROP PROCEDURE IF EXISTS sp_registration_register;
DELIMITER $$

CREATE PROCEDURE sp_registration_register(
    IN p_user_id INT,
    IN p_event_id INT
)
BEGIN
    -- Insert registration (duplicate key automatically fails)
    INSERT INTO Registration (user_id, event_id)
    VALUES (p_user_id, p_event_id);
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_registration_unregister;
DELIMITER $$

CREATE PROCEDURE sp_registration_unregister(
    IN p_user_id INT,
    IN p_event_id INT
)
BEGIN
    DELETE FROM Registration
    WHERE user_id = p_user_id
      AND event_id = p_event_id;
END $$

DELIMITER ;