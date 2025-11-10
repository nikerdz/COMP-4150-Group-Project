DELIMITER $$

CREATE PROCEDURE sp_get_club_by_id(IN p_club_id INT)
BEGIN
    SELECT * FROM Club WHERE club_id = p_club_id;
END$$

DELIMITER ;
