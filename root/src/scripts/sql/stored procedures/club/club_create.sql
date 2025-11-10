DELIMITER $$

CREATE PROCEDURE sp_create_club(
    IN p_club_name VARCHAR(100),
    IN p_club_email VARCHAR(100),
    IN p_club_description TEXT,
    IN p_club_condition ENUM('none','women_only','undergrad_only')
)
BEGIN
    INSERT INTO Club (club_name, club_email, club_description, club_condition)
    VALUES (p_club_name, p_club_email, p_club_description, p_club_condition);
END$$

DELIMITER ;
