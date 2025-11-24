
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


DROP PROCEDURE IF EXISTS sp_user_update_profile;
DELIMITER $$

CREATE PROCEDURE sp_user_update_profile(
    IN p_user_id        INT,
    IN p_first_name     VARCHAR(50),
    IN p_last_name      VARCHAR(50),
    IN p_faculty        VARCHAR(100),
    IN p_level_of_study ENUM('undergraduate','graduate'),
    IN p_year_of_study  INT
)
BEGIN
    UPDATE User
    SET first_name     = p_first_name,
        last_name      = p_last_name,
        faculty        = p_faculty,
        level_of_study = p_level_of_study,
        year_of_study  = p_year_of_study
    WHERE user_id = p_user_id;
END $$

DELIMITER ;

DROP PROCEDURE IF EXISTS sp_user_update_password;
DELIMITER $$

CREATE PROCEDURE sp_user_update_password(
    IN p_user_id INT,
    IN p_hashed_password VARCHAR(255)
)
BEGIN
    UPDATE User
    SET user_password = p_hashed_password
    WHERE user_id = p_user_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_user_delete;
DELIMITER $$

CREATE PROCEDURE sp_user_delete(
    IN p_user_id INT
)
BEGIN
    DELETE FROM User
    WHERE user_id = p_user_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_user_save_interests;
DELIMITER $$

CREATE PROCEDURE sp_user_save_interests(
    IN p_user_id INT,
    IN p_category_id INT
)
BEGIN
    INSERT INTO User_Interests (user_id, category_id)
    VALUES (p_user_id, p_category_id);
END $$

DELIMITER ;



DROP PROCEDURE IF EXISTS sp_user_clear_interests;
DELIMITER $$

CREATE PROCEDURE sp_user_clear_interests(
    IN p_user_id INT
)
BEGIN
    DELETE FROM User_Interests
    WHERE user_id = p_user_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_user_search;
DELIMITER $$

CREATE PROCEDURE sp_user_search(
    IN p_search VARCHAR(100),
    IN p_status VARCHAR(20),
    IN p_exclude_user_id INT
)
BEGIN
    SELECT *
    FROM User
    WHERE 
        -- Exclude user if provided
        (p_exclude_user_id IS NULL OR user_id <> p_exclude_user_id)

        -- Match status if provided (otherwise allow all)
        AND (p_status = 'all' OR user_status = p_status)

        -- Search (if empty string = match all)
        AND (
            p_search = '' OR
            first_name LIKE CONCAT('%', p_search, '%') OR
            last_name LIKE CONCAT('%', p_search, '%') OR
            faculty LIKE CONCAT('%', p_search, '%') OR
            user_email LIKE CONCAT('%', p_search, '%')
        )
    ORDER BY first_name ASC;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_user_suspend;
DELIMITER $$

CREATE PROCEDURE sp_user_suspend(
    IN p_user_id INT
)
BEGIN
    UPDATE User
    SET user_status = 'suspended'
    WHERE user_id = p_user_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_user_activate;
DELIMITER $$

CREATE PROCEDURE sp_user_activate(
    IN p_user_id INT
)
BEGIN
    UPDATE User
    SET user_status = 'active'
    WHERE user_id = p_user_id;
END $$

DELIMITER ;
