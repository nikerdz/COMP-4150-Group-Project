DELIMITER $$

CREATE TRIGGER trg_registration_after_delete_reopen_event_if_not_full
AFTER UPDATE ON Registration
FOR EACH ROW
BEGIN
    DECLARE reg_count INT;
    DECLARE event_cap INT;

    -- Only run if RSVP changed from TRUE to FALSE
    IF OLD.rsvp = TRUE AND NEW.rsvp = FALSE THEN
        -- Count current RSVPs
        SELECT COUNT(*) INTO reg_count
        FROM Registration
        WHERE event_id = OLD.event_id AND rsvp = TRUE;

        -- Get event capacity
        SELECT capacity INTO event_cap
        FROM Event
        WHERE event_id = OLD.event_id;

        -- Reopen registration if space available
        IF reg_count < event_cap THEN
            UPDATE Event
            SET registration_open = TRUE
            WHERE event_id = OLD.event_id;
        END IF;
    END IF;
END$$

DELIMITER ;