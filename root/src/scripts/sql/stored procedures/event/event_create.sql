DELIMITER $$

CREATE PROCEDURE sp_create_event(
    IN p_club_id INT,
    IN p_event_name VARCHAR(100),
    IN p_event_description TEXT,
    IN p_event_location VARCHAR(255),
    IN p_event_date DATETIME,
    IN p_capacity INT,
    IN p_event_condition ENUM('none','women_only','undergrad_only','first_year_only'),
    IN p_event_fee DECIMAL(8,2)
)
BEGIN
    INSERT INTO Event (club_id, event_name, event_description, event_location, event_date, capacity, event_condition, event_fee)
    VALUES (p_club_id, p_event_name, p_event_description, p_event_location, p_event_date, p_capacity, p_event_condition, p_event_fee);
END$$

DELIMITER ;
