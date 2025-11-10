DELIMITER $$

CREATE PROCEDURE sp_update_event(
    IN p_event_id INT,
    IN p_event_name VARCHAR(100),
    IN p_event_description TEXT,
    IN p_event_location VARCHAR(255),
    IN p_event_date DATETIME,
    IN p_capacity INT,
    IN p_event_status ENUM('pending','approved','cancelled'),
    IN p_event_condition ENUM('none','women_only','undergrad_only','first_year_only'),
    IN p_event_fee DECIMAL(8,2)
)
BEGIN
    UPDATE Event
    SET event_name = p_event_name,
        event_description = p_event_description,
        event_location = p_event_location,
        event_date = p_event_date,
        capacity = p_capacity,
        event_status = p_event_status,
        event_condition = p_event_condition,
        event_fee = p_event_fee
    WHERE event_id = p_event_id;
END$$

DELIMITER ;
