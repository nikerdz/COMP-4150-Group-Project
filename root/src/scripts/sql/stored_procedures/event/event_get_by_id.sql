DELIMITER $$

CREATE PROCEDURE sp_get_event_by_id(IN p_event_id INT)
BEGIN
    SELECT * FROM Event WHERE event_id = p_event_id;
END$$

DELIMITER ;
