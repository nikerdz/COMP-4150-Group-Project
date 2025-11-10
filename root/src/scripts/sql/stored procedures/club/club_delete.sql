DELIMITER $$

CREATE PROCEDURE sp_delete_club(IN p_club_id INT)
BEGIN
    DELETE FROM Club WHERE club_id = p_club_id;
END$$

DELIMITER ;
