DELIMITER $$

CREATE PROCEDURE sp_get_user_by_id(IN p_user_id INT)
BEGIN
    SELECT * FROM User WHERE user_id = p_user_id;
END$$

DELIMITER ;
