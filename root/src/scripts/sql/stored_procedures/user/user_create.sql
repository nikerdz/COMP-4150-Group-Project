DELIMITER $$

CREATE PROCEDURE sp_create_user(
    IN p_first_name VARCHAR(50),
    IN p_last_name VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_password VARCHAR(255),
    IN p_gender VARCHAR(20),
    IN p_faculty VARCHAR(100),
    IN p_level_of_study ENUM('undergraduate', 'graduate'),
    IN p_year_of_study INT
)
BEGIN
    INSERT INTO User (first_name, last_name, user_email, user_password, gender, faculty, level_of_study, year_of_study)
    VALUES (p_first_name, p_last_name, p_email, p_password, p_gender, p_faculty, p_level_of_study, p_year_of_study);
END$$

DELIMITER ;
