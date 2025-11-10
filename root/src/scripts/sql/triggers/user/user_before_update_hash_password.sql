DELIMITER $$ 

CREATE TRIGGER trg_user_before_update_hash_password
BEFORE UPDATE ON User 
FOR EACH ROW 
BEGIN -- Only re-hash if the password actually changed 
    IF NEW.password <> OLD.password THEN 
    SET NEW.password = SHA2(NEW.password, 256); 
    END IF; 
END$$

DELIMITER ;