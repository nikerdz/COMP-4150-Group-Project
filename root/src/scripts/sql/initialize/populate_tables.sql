-- -------------------------
-- Users
-- -------------------------
INSERT INTO `User` (first_name, last_name, user_email, user_password, user_status, user_type, gender, faculty, level_of_study, year_of_study)
VALUES
('Anika', 'Khan', 'anika.khan@example.com', 'hashed_pw1', 'active', 'student', 'F', 'Science', 'undergraduate', 4)
ON DUPLICATE KEY UPDATE user_email = user_email;

INSERT INTO `User` (first_name, last_name, user_email, user_password, user_status, user_type, gender, faculty, level_of_study, year_of_study)
VALUES
('Shameer', 'Sheikh', 'shameer.sheikh@example.com', 'hashed_pw2', 'active', 'student', 'M', 'Engineering', 'undergraduate', 3)
ON DUPLICATE KEY UPDATE user_email = user_email;

INSERT INTO `User` (first_name, last_name, user_email, user_password, user_status, user_type, gender, faculty, level_of_study, year_of_study)
VALUES
('Admin', 'User', 'admin@example.com', 'hashed_pw3', 'active', 'admin', 'M', 'Science', 'graduate', 1)
ON DUPLICATE KEY UPDATE user_email = user_email;

-- -------------------------
-- Categories
-- -------------------------
INSERT INTO `Category` (category_name) VALUES ('Sports') ON DUPLICATE KEY UPDATE category_name = category_name;
INSERT INTO `Category` (category_name) VALUES ('Cultural') ON DUPLICATE KEY UPDATE category_name = category_name;
INSERT INTO `Category` (category_name) VALUES ('Academic') ON DUPLICATE KEY UPDATE category_name = category_name;
INSERT INTO `Category` (category_name) VALUES ('Social') ON DUPLICATE KEY UPDATE category_name = category_name;

-- -------------------------
-- Clubs
-- -------------------------
INSERT INTO `Club` (club_name, club_email, club_description, creation_date, club_condition, club_status)
VALUES
('Chess Club', 'chess@example.com', 'For chess enthusiasts', '2024-09-01', 'undergrad_only', 'active')
ON DUPLICATE KEY UPDATE club_name = club_name;

INSERT INTO `Club` (club_name, club_email, club_description, creation_date, club_condition, club_status)
VALUES
('Drama Club', 'drama@example.com', 'Theater and acting club', '2023-01-15', 'women_only', 'active')
ON DUPLICATE KEY UPDATE club_name = club_name;

-- -------------------------
-- User_Interests
-- -------------------------
INSERT IGNORE INTO `User_Interests` (user_id, category_id) VALUES (1,1);
INSERT IGNORE INTO `User_Interests` (user_id, category_id) VALUES (1,3);
INSERT IGNORE INTO `User_Interests` (user_id, category_id) VALUES (2,2);
INSERT IGNORE INTO `User_Interests` (user_id, category_id) VALUES (2,4);

-- -------------------------
-- Club_Tags
-- -------------------------
INSERT IGNORE INTO `Club_Tags` (club_id, category_id) VALUES (1,1);
INSERT IGNORE INTO `Club_Tags` (club_id, category_id) VALUES (1,3);
INSERT IGNORE INTO `Club_Tags` (club_id, category_id) VALUES (2,2);
INSERT IGNORE INTO `Club_Tags` (club_id, category_id) VALUES (2,4);

-- -------------------------
-- Membership
-- -------------------------
INSERT IGNORE INTO `Membership` (user_id, club_id, membership_date) VALUES (1,1,'2024-09-05');
INSERT IGNORE INTO `Membership` (user_id, club_id, membership_date) VALUES (2,2,'2024-09-06');

-- -------------------------
-- Executive
-- -------------------------
INSERT IGNORE INTO `Executive` (user_id, club_id, executive_role) VALUES (1,1,'President');

-- -------------------------
-- Events
-- -------------------------
INSERT IGNORE INTO `Event` (club_id, event_name, event_description, event_location, event_date, capacity, event_status, event_condition, event_fee)
VALUES (1,'Chess Tournament','Campus-wide chess competition','Room 101','2024-11-15 10:00:00',50,'pending','undergrad_only',0.00);

INSERT IGNORE INTO `Event` (club_id, event_name, event_description, event_location, event_date, capacity, event_status, event_condition, event_fee)
VALUES (2,'Drama Night','Evening performance by club members','Auditorium','2024-11-20 19:00:00',100,'pending','women_only',10.00);

-- -------------------------
-- Registration
-- -------------------------
INSERT IGNORE INTO `Registration` (user_id, event_id, registration_date) VALUES (1,1,TRUE,'2024-11-01 12:00:00');
INSERT IGNORE INTO `Registration` (user_id, event_id, registration_date) VALUES (2,2,TRUE,'2024-11-02 12:00:00');

-- -------------------------
-- Payment
-- -------------------------
INSERT IGNORE INTO `Payment` (registration_id, payment_status, payment_method, payment_date, amount)
VALUES (1,'completed','credit_card','2024-11-01 12:30:00',0.00);

INSERT IGNORE INTO `Payment` (registration_id, payment_status, payment_method, payment_date, amount)
VALUES (2,'completed','credit_card','2024-11-02 12:30:00',10.00);

-- -------------------------
-- Notifications
-- -------------------------
INSERT IGNORE INTO `Notification` (user_id, event_id, notification_message, notification_type, notification_status, notification_timestamp)
VALUES (1,1,'Reminder: Chess Tournament coming up!','reminder','unread',NOW());

INSERT IGNORE INTO `Notification` (user_id, event_id, notification_message, notification_type, notification_status, notification_timestamp)
VALUES (2,2,'Drama Night registration confirmed!','announcement','unread',NOW());

-- -------------------------
-- Comments
-- -------------------------
INSERT IGNORE INTO `Comments` (user_id, event_id, comment_message, comment_date)
VALUES (1,1,'Excited for the tournament!','2024-11-02 14:00:00');

INSERT IGNORE INTO `Comments` (user_id, event_id, comment_message, comment_date)
VALUES (2,2,'Looking forward to the show!','2024-11-03 14:00:00');