
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










DROP PROCEDURE IF EXISTS sp_club_create;
DELIMITER $$

CREATE PROCEDURE sp_club_create(
    IN p_club_name        VARCHAR(100),
    IN p_club_email       VARCHAR(100),
    IN p_club_description TEXT,
    IN p_creation_date    DATE,
    IN p_club_condition   ENUM('none','women_only','undergrad_only','first_year_only'),
    IN p_club_status      ENUM('active','inactive')
)
BEGIN
    INSERT INTO Club (
        club_name,
        club_email,
        club_description,
        creation_date,
        club_condition,
        club_status
    )
    VALUES (
        p_club_name,
        p_club_email,
        p_club_description,
        p_creation_date,
        p_club_condition,
        p_club_status
    );

    SELECT LAST_INSERT_ID() AS club_id;

END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_club_search;
DELIMITER $$

CREATE PROCEDURE sp_club_search(
    IN p_search VARCHAR(255),
    IN p_category_id INT,
    IN p_condition VARCHAR(50),
    IN p_limit INT,
    IN p_offset INT
)
BEGIN
    SELECT 
        c.*,
        GROUP_CONCAT(DISTINCT cat.category_name) AS categories
    FROM Club c
    LEFT JOIN Club_Tags ct ON c.club_id = ct.club_id
    LEFT JOIN Category cat ON ct.category_id = cat.category_id
    WHERE 
        c.club_status = 'active'

        -- Search filter
        AND (
            p_search IS NULL
            OR p_search = ''
            OR c.club_name LIKE CONCAT('%', p_search, '%')
            OR c.club_description LIKE CONCAT('%', p_search, '%')
        )

        -- Category filter
        AND (
            p_category_id IS NULL
            OR p_category_id = 0
            OR ct.category_id = p_category_id
        )

        -- Condition filter
        AND (
            p_condition = 'any'
            OR p_condition = ''
            OR p_condition IS NULL
            OR c.club_condition = p_condition
        )

    GROUP BY c.club_id
    ORDER BY c.club_name ASC
    LIMIT p_limit OFFSET p_offset;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_club_update_main;
DELIMITER $$

CREATE PROCEDURE sp_club_update_main(
    IN p_club_id INT,
    IN p_club_name VARCHAR(100),
    IN p_club_email VARCHAR(100),
    IN p_club_description TEXT,
    IN p_club_condition ENUM('none','women_only','undergrad_only','first_year_only')
)
BEGIN
    UPDATE Club
    SET 
        club_name = p_club_name,
        club_email = p_club_email,
        club_description = p_club_description,
        club_condition = p_club_condition
    WHERE club_id = p_club_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_club_clear_tags;
DELIMITER $$

CREATE PROCEDURE sp_club_clear_tags(
    IN p_club_id INT
)
BEGIN
    DELETE FROM Club_Tags
    WHERE club_id = p_club_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_club_add_tag;
DELIMITER $$

CREATE PROCEDURE sp_club_add_tag(
    IN p_club_id INT,
    IN p_category_id INT
)
BEGIN
    INSERT INTO Club_Tags (club_id, category_id)
    VALUES (p_club_id, p_category_id);
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_club_delete;
DELIMITER $$

CREATE PROCEDURE sp_club_delete(
    IN p_club_id INT
)
BEGIN
    DECLARE exit handler for SQLEXCEPTION 
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    -- Delete related tags
    DELETE FROM Club_Tags
    WHERE club_id = p_club_id;

    -- Delete related memberships
    DELETE FROM Membership
    WHERE club_id = p_club_id;

    -- Delete related events
    DELETE FROM Event
    WHERE club_id = p_club_id;

    -- Finally delete the club
    DELETE FROM Club
    WHERE club_id = p_club_id;

    COMMIT;

END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_club_search_admin;
DELIMITER $$

CREATE PROCEDURE sp_club_search_admin(
    IN p_search VARCHAR(255),
    IN p_status VARCHAR(20)
)
BEGIN
    SELECT *
    FROM Club
    WHERE 
        -- Status filter
        (p_status = 'all' OR club_status = p_status)

        -- Search filter
        AND (
            p_search = '' 
            OR club_name LIKE CONCAT('%', p_search, '%')
            OR club_description LIKE CONCAT('%', p_search, '%')
        )

    ORDER BY club_name ASC;
END $$

DELIMITER ;








DROP PROCEDURE IF EXISTS sp_event_search;
DELIMITER $$

CREATE PROCEDURE sp_event_search(
    IN p_search VARCHAR(255),
    IN p_category_id INT,
    IN p_condition VARCHAR(50),
    IN p_limit INT,
    IN p_offset INT,
    IN p_status_filter VARCHAR(20),       -- 'approved','pending','cancelled','any'
    IN p_include_inactive_clubs BOOLEAN   -- 0 or 1
)
BEGIN
    SELECT
        e.*,
        c.club_name,
        GROUP_CONCAT(DISTINCT cat.category_name) AS categories
    FROM Event e
    JOIN Club c ON e.club_id = c.club_id
    LEFT JOIN Club_Tags ct ON c.club_id = ct.club_id
    LEFT JOIN Category cat ON ct.category_id = cat.category_id
    WHERE 1=1

        -- Status filter
        AND (
            (p_status_filter = 'approved'  AND e.event_status = 'approved')
         OR (p_status_filter = 'pending'   AND e.event_status = 'pending')
         OR (p_status_filter = 'cancelled' AND e.event_status = 'cancelled')
         OR (p_status_filter = 'any')
        )

        -- Club visibility filter
        AND (
            p_include_inactive_clubs = 1
            OR c.club_status = 'active'
        )

        -- Search filter
        AND (
            p_search = ''
            OR e.event_name        LIKE CONCAT('%', p_search, '%')
            OR e.event_description LIKE CONCAT('%', p_search, '%')
            OR c.club_name         LIKE CONCAT('%', p_search, '%')
        )

        -- Category filter
        AND (
            p_category_id IS NULL
            OR ct.category_id = p_category_id
        )

        -- Condition filter
        AND (
            p_condition = 'any'
            OR e.event_condition = p_condition
        )

    GROUP BY e.event_id
    ORDER BY e.event_date ASC
    LIMIT p_limit OFFSET p_offset;
END $$

DELIMITER ;



DROP PROCEDURE IF EXISTS sp_event_search_admin;
DELIMITER $$

CREATE PROCEDURE sp_event_search_admin(
    IN p_search VARCHAR(255),
    IN p_status VARCHAR(20)
)
BEGIN
    SELECT
        e.*,
        c.club_name
    FROM Event e
    JOIN Club c ON e.club_id = c.club_id
    WHERE 1=1

        -- Status filter
        AND (
            p_status = 'all'
            OR e.event_status = p_status
        )

        -- Search filter
        AND (
            p_search = ''
            OR e.event_name        LIKE CONCAT('%', p_search, '%')
            OR e.event_description LIKE CONCAT('%', p_search, '%')
            OR c.club_name         LIKE CONCAT('%', p_search, '%')
        )

    ORDER BY e.event_date ASC;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_event_create;
DELIMITER $$

CREATE PROCEDURE sp_event_create(
    IN p_club_id INT,
    IN p_event_name VARCHAR(255),
    IN p_event_description TEXT,
    IN p_event_location VARCHAR(255),
    IN p_event_date DATE,
    IN p_capacity INT,
    IN p_event_condition VARCHAR(50),
    IN p_event_fee DECIMAL(10,2)
)
BEGIN
    INSERT INTO Event (
        club_id,
        event_name,
        event_description,
        event_location,
        event_date,
        capacity,
        event_condition,
        event_fee
    )
    VALUES (
        p_club_id,
        p_event_name,
        p_event_description,
        p_event_location,
        p_event_date,
        p_capacity,
        p_event_condition,
        p_event_fee
    );

    SELECT LAST_INSERT_ID() AS event_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_event_update;
DELIMITER $$

CREATE PROCEDURE sp_event_update(
    IN p_event_id INT,
    IN p_event_name VARCHAR(255),
    IN p_event_description TEXT,
    IN p_event_location VARCHAR(255),
    IN p_event_date DATE,
    IN p_capacity INT,
    IN p_event_condition VARCHAR(50),
    IN p_event_fee DECIMAL(10,2)
)
BEGIN
    UPDATE Event
    SET
        event_name        = p_event_name,
        event_description = p_event_description,
        event_location    = p_event_location,
        event_date        = p_event_date,
        capacity          = p_capacity,
        event_condition   = p_event_condition,
        event_fee         = p_event_fee
    WHERE event_id = p_event_id;
END $$

DELIMITER ;



DROP PROCEDURE IF EXISTS sp_event_update_status;
DELIMITER $$

CREATE PROCEDURE sp_event_update_status(
    IN p_event_id INT,
    IN p_status VARCHAR(20)
)
BEGIN
    UPDATE Event
    SET event_status = p_status
    WHERE event_id = p_event_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_event_delete;
DELIMITER $$

CREATE PROCEDURE sp_event_delete(
    IN p_event_id INT
)
BEGIN
    DECLARE exit handler for SQLEXCEPTION 
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM Registration WHERE event_id = p_event_id;
    DELETE FROM Comments     WHERE event_id = p_event_id;
    DELETE FROM Payment      WHERE registration_id IN (
        SELECT registration_id FROM Registration WHERE event_id = p_event_id
    );

    DELETE FROM Event WHERE event_id = p_event_id;

    COMMIT;
END $$

DELIMITER ;









DROP PROCEDURE IF EXISTS sp_membership_join;
DELIMITER $$

CREATE PROCEDURE sp_membership_join(
    IN p_user_id INT,
    IN p_club_id INT
)
BEGIN
    INSERT INTO Membership (user_id, club_id)
    VALUES (p_user_id, p_club_id);
END $$

DELIMITER ;



DROP PROCEDURE IF EXISTS sp_membership_leave;
DELIMITER $$

CREATE PROCEDURE sp_membership_leave(
    IN p_user_id INT,
    IN p_club_id INT
)
BEGIN
    DELETE FROM Membership
    WHERE user_id = p_user_id
      AND club_id = p_club_id;
END $$

DELIMITER ;












DROP PROCEDURE IF EXISTS sp_registration_register;
DELIMITER $$

CREATE PROCEDURE sp_registration_register(
    IN p_user_id INT,
    IN p_event_id INT
)
BEGIN
    -- Insert registration (duplicate key automatically fails)
    INSERT INTO Registration (user_id, event_id)
    VALUES (p_user_id, p_event_id);
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_registration_unregister;
DELIMITER $$

CREATE PROCEDURE sp_registration_unregister(
    IN p_user_id INT,
    IN p_event_id INT
)
BEGIN
    DELETE FROM Registration
    WHERE user_id = p_user_id
      AND event_id = p_event_id;
END $$

DELIMITER ;


