----------------------
-- USER SPs
----------------------
DROP PROCEDURE IF EXISTS sp_user_register;
DELIMITER $$

CREATE PROCEDURE sp_user_register(
    IN p_first_name      VARCHAR(50),
    IN p_last_name       VARCHAR(50),
    IN p_user_email      VARCHAR(100),
    IN p_user_password   VARCHAR(255),
    IN p_gender          VARCHAR(20),
    IN p_faculty         VARCHAR(100),
    IN p_level_of_study  ENUM('undergraduate','graduate'),
    IN p_year_of_study   INT
)
BEGIN
    INSERT INTO User (
        first_name,
        last_name,
        user_email,
        user_password,
        gender,
        faculty,
        level_of_study,
        year_of_study
    )
    VALUES (
        p_first_name,
        p_last_name,
        p_user_email,
        p_user_password,
        p_gender,
        p_faculty,
        p_level_of_study,
        p_year_of_study
    );

    SELECT LAST_INSERT_ID() AS user_id;
END $$

DELIMITER ;
