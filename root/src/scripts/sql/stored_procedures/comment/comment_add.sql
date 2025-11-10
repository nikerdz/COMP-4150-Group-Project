DELIMITER $$

CREATE PROCEDURE sp_add_comment(
    IN p_user_id INT,
    IN p_event_id INT,
    IN p_message TEXT
)
BEGIN
    INSERT INTO Comments (user_id, event_id, comment_message)
    VALUES (p_user_id, p_event_id, p_message);
END$$

DELIMITER ;
