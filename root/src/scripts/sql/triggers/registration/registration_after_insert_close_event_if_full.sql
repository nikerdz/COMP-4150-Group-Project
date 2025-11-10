DELIMITER $$

CREATE TRIGGER trg_registration_after_insert_close_event_if_full
AFTER INSERT ON Registration
FOR EACH ROW
BEGIN
    DECLARE reg_count INT;
    DECLARE event_cap INT;

    -- Count current RSVPs for this event
    SELECT COUNT(*) INTO reg_count
    FROM Registration
    WHERE event_id = NEW.event_id AND rsvp = TRUE;

    -- Get event capacity
    SELECT capacity INTO event_cap
    FROM Event
    WHERE event_id = NEW.event_id;

    -- Close registration if full
    IF reg_count >= event_cap THEN
        UPDATE Event
        SET registration_open = FALSE
        WHERE event_id = NEW.event_id;
    END IF;
END$$

DELIMITER ;