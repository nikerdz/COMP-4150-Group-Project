DELIMITER $$

CREATE PROCEDURE sp_get_payment_by_registration(IN p_registration_id INT)
BEGIN
    SELECT * FROM Payment WHERE registration_id = p_registration_id;
END$$

DELIMITER ;
