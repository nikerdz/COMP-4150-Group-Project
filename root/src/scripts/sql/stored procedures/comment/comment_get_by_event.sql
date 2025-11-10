DELIMITER $$

CREATE PROCEDURE sp_get_comments_by_event(IN p_event_id INT)
BEGIN
    SELECT * FROM Comments WHERE event_id = p_event_id;
END$$

DELIMITER ;
