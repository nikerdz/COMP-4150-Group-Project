DELIMITER $$

CREATE PROCEDURE sp_create_payment(
    IN p_registration_id INT,
    IN p_payment_status ENUM('pending','completed','refunded'),
    IN p_payment_method ENUM('credit_card','debit','paypal','cash'),
    IN p_amount DECIMAL(8,2)
)
BEGIN
    INSERT INTO Payment (registration_id, payment_status, payment_method, amount)
    VALUES (p_registration_id, p_payment_status, p_payment_method, p_amount);
END$$

DELIMITER ;
