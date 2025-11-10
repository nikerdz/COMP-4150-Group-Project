DELIMITER $$

CREATE PROCEDURE sp_get_registrations_by_user(IN p_user_id INT)
BEGIN
    SELECT * FROM Registration WHERE user_id = p_user_id;
END$$

DELIMITER ;
