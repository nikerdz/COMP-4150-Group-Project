DELIMITER $$

CREATE PROCEDURE sp_assign_executive(
    IN p_user_id INT,
    IN p_club_id INT,
    IN p_role VARCHAR(50)
)
BEGIN
    INSERT INTO Executive (user_id, club_id, executive_role) VALUES (p_user_id, p_club_id, p_role);
END$$

DELIMITER ;
