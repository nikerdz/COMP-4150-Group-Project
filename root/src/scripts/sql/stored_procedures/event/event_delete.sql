DELIMITER $$

CREATE PROCEDURE sp_delete_event(IN p_event_id INT)
BEGIN
    DELETE FROM Event WHERE event_id = p_event_id;
END$$

DELIMITER ;
