DELIMITER $$

CREATE PROCEDURE sp_update_user(
    IN p_user_id INT,
    IN p_first_name VARCHAR(50),
    IN p_last_name VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_gender VARCHAR(20),
    IN p_faculty VARCHAR(100),
    IN p_level_of_study ENUM('undergraduate', 'graduate'),
    IN p_year_of_study INT
)
BEGIN
    UPDATE User
    SET first_name = p_first_name,
        last_name = p_last_name,
        user_email = p_email,
        gender = p_gender,
        faculty = p_faculty,
        level_of_study = p_level_of_study,
        year_of_study = p_year_of_study
    WHERE user_id = p_user_id;
END$$

DELIMITER ;
