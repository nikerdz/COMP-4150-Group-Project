DELIMITER $$

CREATE PROCEDURE sp_remove_club_tag(
    IN p_club_id INT,
    IN p_category_id INT
)
BEGIN
    DELETE FROM Club_Tags WHERE club_id = p_club_id AND category_id = p_category_id;
END$$

DELIMITER ;
