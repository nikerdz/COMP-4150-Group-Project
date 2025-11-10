DELIMITER $$

CREATE PROCEDURE sp_add_user_interest(
    IN p_user_id INT,
    IN p_category_id INT
)
BEGIN
    INSERT INTO User_Interests (user_id, category_id) VALUES (p_user_id, p_category_id);
END$$

DELIMITER ;
