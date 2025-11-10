DELIMITER $$

CREATE PROCEDURE sp_add_club_tag(
    IN p_club_id INT,
    IN p_category_id INT
)
BEGIN
    INSERT INTO Club_Tags (club_id, category_id) VALUES (p_club_id, p_category_id);
END$$

DELIMITER ;
