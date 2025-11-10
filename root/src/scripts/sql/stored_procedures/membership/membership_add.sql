DELIMITER $$

CREATE PROCEDURE sp_add_membership(
    IN p_user_id INT,
    IN p_club_id INT
)
BEGIN
    INSERT INTO Membership (user_id, club_id) VALUES (p_user_id, p_club_id);
END$$

DELIMITER ;
