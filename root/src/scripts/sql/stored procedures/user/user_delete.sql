DELIMITER $$

CREATE PROCEDURE sp_delete_user(IN p_user_id INT)
BEGIN
    DELETE FROM User WHERE user_id = p_user_id;
END$$

DELIMITER ;
