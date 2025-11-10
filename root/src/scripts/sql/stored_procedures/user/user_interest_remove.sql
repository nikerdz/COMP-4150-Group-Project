DELIMITER $$

CREATE PROCEDURE sp_remove_user_interest(
    IN p_user_id INT,
    IN p_category_id INT
)
BEGIN
    DELETE FROM User_Interests WHERE user_id = p_user_id AND category_id = p_category_id;
END$$

DELIMITER ;
