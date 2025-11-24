DROP PROCEDURE IF EXISTS sp_payment_create_pending;
DELIMITER $$

CREATE PROCEDURE sp_payment_create_pending(
    IN p_registration_id INT,
    IN p_amount          DECIMAL(8,2),
    IN p_method          VARCHAR(20)
)
BEGIN
    INSERT INTO Payment (registration_id, amount, payment_status, payment_method)
    VALUES (p_registration_id, p_amount, 'pending', p_method);
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_payment_mark_completed;
DELIMITER $$

CREATE PROCEDURE sp_payment_mark_completed(
    IN p_registration_id INT
)
BEGIN
    UPDATE Payment
    SET 
        payment_status = 'completed',
        payment_date = NOW()
    WHERE registration_id = p_registration_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_payment_refund;
DELIMITER $$

CREATE PROCEDURE sp_payment_refund(
    IN p_registration_id INT
)
BEGIN
    UPDATE Payment
    SET 
        payment_status = 'refunded',
        payment_date = NOW()
    WHERE registration_id = p_registration_id;
END $$

DELIMITER ;


DROP PROCEDURE IF EXISTS sp_payment_delete_by_registration;
DELIMITER $$

CREATE PROCEDURE sp_payment_delete_by_registration(
    IN p_registration_id INT
)
BEGIN
    DELETE FROM Payment
    WHERE registration_id = p_registration_id;
END $$

DELIMITER ;