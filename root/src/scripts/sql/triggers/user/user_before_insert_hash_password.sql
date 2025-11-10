DELIMITER $$

CREATE TRIGGER trg_user_before_insert_hash_password
BEFORE INSERT ON User
FOR EACH ROW
BEGIN
    -- Automatically hash password before insert
    SET NEW.password = SHA2(NEW.password, 256);
END$$

DELIMITER ;