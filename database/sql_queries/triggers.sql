use blog;


DELIMITER $$

CREATE TRIGGER check_admin_before_insert_post
BEFORE INSERT ON post
FOR EACH ROW
BEGIN
    DECLARE admin_status BOOLEAN;

    -- Get is_admin status of the user trying to insert the post
    SELECT is_admin INTO admin_status
    FROM user
    WHERE id = NEW.user_id;

    IF admin_status IS FALSE THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Only admin users can create posts';
    END IF;
END$$

DELIMITER ;
COMMIT;
