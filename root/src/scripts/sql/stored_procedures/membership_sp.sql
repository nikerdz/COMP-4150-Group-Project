
DROP PROCEDURE IF EXISTS sp_membership_join;
DELIMITER $$

CREATE PROCEDURE sp_membership_join(
    IN p_user_id INT,
    IN p_club_id INT
)
BEGIN
    INSERT INTO Membership (user_id, club_id)
    VALUES (p_user_id, p_club_id);
END $$

DELIMITER ;



DROP PROCEDURE IF EXISTS sp_membership_leave;
DELIMITER $$

CREATE PROCEDURE sp_membership_leave(
    IN p_user_id INT,
    IN p_club_id INT
)
BEGIN
    DELETE FROM Membership
    WHERE user_id = p_user_id
      AND club_id = p_club_id;
END $$

DELIMITER ;