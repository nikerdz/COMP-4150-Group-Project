DELIMITER $$

CREATE PROCEDURE sp_update_club(
    IN p_club_id INT,
    IN p_club_name VARCHAR(100),
    IN p_club_email VARCHAR(100),
    IN p_club_description TEXT,
    IN p_club_condition ENUM('none','women_only','undergrad_only'),
    IN p_club_status ENUM('active','inactive')
)
BEGIN
    UPDATE Club
    SET club_name = p_club_name,
        club_email = p_club_email,
        club_description = p_club_description,
        club_condition = p_club_condition,
        club_status = p_club_status
    WHERE club_id = p_club_id;
END$$

DELIMITER ;
