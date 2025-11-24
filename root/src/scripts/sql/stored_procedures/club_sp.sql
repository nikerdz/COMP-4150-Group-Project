
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