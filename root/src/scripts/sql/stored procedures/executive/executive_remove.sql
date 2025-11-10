DELIMITER $$

CREATE PROCEDURE sp_remove_executive(
    IN p_user_id INT,
    IN p_club_id INT
)
BEGIN
    DELETE FROM Executive WHERE user_id = p_user_id AND club_id = p_club_id;
END$$

DELIMITER ;
