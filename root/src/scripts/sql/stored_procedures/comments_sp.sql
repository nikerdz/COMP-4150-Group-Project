
DROP PROCEDURE IF EXISTS sp_comment_add;
DELIMITER $$

CREATE PROCEDURE sp_comment_add(
    IN p_user_id INT,
    IN p_event_id INT,
    IN p_message TEXT
)
BEGIN
    INSERT INTO Comments (user_id, event_id, comment_message)
    VALUES (p_user_id, p_event_id, p_message);
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_comment_delete;
DELIMITER $$

CREATE PROCEDURE sp_comment_delete(IN p_comment_id INT)
BEGIN
    DELETE FROM Comments
    WHERE comment_id = p_comment_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_comment_delete_owned;
DELIMITER $$

CREATE PROCEDURE sp_comment_delete_owned(
    IN p_comment_id INT,
    IN p_user_id INT
)
BEGIN
    DELETE FROM Comments
    WHERE comment_id = p_comment_id
      AND user_id = p_user_id;
END $$

DELIMITER ;
