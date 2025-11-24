-- ==========================
-- USERS (30 students + 1 admin)
-- ==========================

INSERT INTO `User` 
(first_name, last_name, user_email, user_password, user_status, user_type, gender, faculty, level_of_study, year_of_study)
VALUES
('Anika', 'Khan', 'anika.khan@uwindsor.ca', 'pw1', 'active', 'student', 'F', 'Science', 'undergraduate', 4),
('Shameer', 'Sheikh', 'shameer.sheikh@uwindsor.ca', 'pw2', 'active', 'student', 'M', 'Engineering', 'undergraduate', 3),

('Emily', 'Tran', 'emily.tran@uwindsor.ca', 'pw3', 'active', 'student', 'F', 'Engineering', 'undergraduate', 2),
('Daniel', 'Lee', 'daniel.lee@uwindsor.ca', 'pw4', 'active', 'student', 'M', 'Science', 'undergraduate', 1),
('Sophia', 'Martinez', 'sophia.martinez@uwindsor.ca', 'pw5', 'active', 'student', 'F', 'Odette School of Business', 'graduate', 3),
('Noah', 'Singh', 'noah.singh@uwindsor.ca', 'pw6', 'active', 'student', 'M', 'Science', 'undergraduate', 4),
('Ava', 'Patel', 'ava.patel@uwindsor.ca', 'pw7', 'active', 'student', 'F', 'Arts, Humanities, & Social Sciences', 'undergraduate', 2),
('Ethan', 'Brown', 'ethan.brown@uwindsor.ca', 'pw8', 'active', 'student', 'M', 'Arts, Humanities, & Social Sciences', 'undergraduate', 1),
('Maya', 'Sharma', 'maya.sharma@uwindsor.ca', 'pw9', 'active', 'student', 'F', 'Human Kinetics', 'undergraduate', 4),
('Lucas', 'Wong', 'lucas.wong@uwindsor.ca', 'pw10', 'active', 'student', 'M', 'Engineering', 'undergraduate', 2),

('Isabella', 'Costa', 'isabella.costa@uwindsor.ca', 'pw11', 'active', 'student', 'F', 'Odette School of Business', 'graduate', 1),
('Benjamin', 'Haddad', 'benjamin.haddad@uwindsor.ca', 'pw12', 'active', 'student', 'M', 'Law', 'graduate', 1),
('Aria', 'Gupta', 'aria.gupta@uwindsor.ca', 'pw13', 'active', 'student', 'F', 'Science', 'undergraduate', 3),
('Logan', 'Mitchell', 'logan.mitchell@uwindsor.ca', 'pw14', 'active', 'student', 'M', 'Science', 'undergraduate', 4),
('Zara', 'Hassan', 'zara.hassan@uwindsor.ca', 'pw15', 'active', 'student', 'F', 'Nursing', 'undergraduate', 2),
('Oliver', 'D’Souza', 'oliver.dsouza@uwindsor.ca', 'pw16', 'active', 'student', 'M', 'Science', 'undergraduate', 1),
('Chloe', 'Kim', 'chloe.kim@uwindsor.ca', 'pw17', 'active', 'student', 'F', 'Arts, Humanities, & Social Sciences', 'undergraduate', 3),
('Nathan', 'Carter', 'nathan.carter@uwindsor.ca', 'pw18', 'active', 'student', 'M', 'Human Kinetics', 'undergraduate', 4),
('Sofia', 'Rossi', 'sofia.rossi@uwindsor.ca', 'pw19', 'active', 'student', 'F', 'Education', 'undergraduate', 2),
('Aiden', 'Park', 'aiden.park@uwindsor.ca', 'pw20', 'active', 'student', 'M', 'Engineering', 'graduate', 3),

('Layla', 'Morgan', 'layla.morgan@uwindsor.ca', 'pw21', 'active', 'student', 'F', 'Odette School of Business', 'undergraduate', 2),
('Gabriel', 'White', 'gabriel.white@uwindsor.ca', 'pw22', 'active', 'student', 'M', 'Science', 'undergraduate', 1),
('Hannah', 'Baker', 'hannah.baker@uwindsor.ca', 'pw23', 'active', 'student', 'F', 'Science', 'undergraduate', 4),
('Leo', 'Nguyen', 'leo.nguyen@uwindsor.ca', 'pw24', 'active', 'student', 'M', 'Engineering', 'undergraduate', 2),
('Mira', 'Alami', 'mira.alami@uwindsor.ca', 'pw25', 'active', 'student', 'F', 'Arts, Humanities, & Social Sciences', 'undergraduate', 1),
('Isaac', 'Coleman', 'isaac.coleman@uwindsor.ca', 'pw26', 'active', 'student', 'M', 'Human Kinetics', 'undergraduate', 3),
('Selena', 'Kouris', 'selena.kouris@uwindsor.ca', 'pw27', 'active', 'student', 'F', 'Odette School of Business', 'undergraduate', 4),
('Jayden', 'Frost', 'jayden.frost@uwindsor.ca', 'pw28', 'active', 'student', 'M', 'Science', 'graduate', 2),
('Amira', 'Rahman', 'amira.rahman@uwindsor.ca', 'pw29', 'active', 'student', 'F', 'Engineering', 'undergraduate', 1),
('Marcus', 'James', 'marcus.james@uwindsor.ca', 'pw30', 'active', 'student', 'M', 'Science', 'undergraduate', 3),
('Admin', 'User', 'admin@uwindsor.ca', '$2y$10$icpd9Juy68td39j78i94.OKyrfPIQgHBAJK5kbeleXHjjGaSROhJy', 'active', 'admin', 'M', 'Science', 'undergraduate', 3);

-- ==========================
-- CATEGORIES (15)
-- ==========================

INSERT INTO `Category` (category_name) VALUES 
('Cultural'),
('Academic'),
('STEM'),
('Engineering'),
('Business'),
('Health & Wellness'),
('Volunteering'),
('Sports & Recreation'),
('Arts & Creativity'),
('Humanities'),
('Social Justice'),
('International'),
('Religious & Spiritual'),
('Technology'),
('Community & Outreach')
ON DUPLICATE KEY UPDATE category_name = category_name;


-- ==========================
-- CLUBS (25)
-- ==========================

INSERT INTO `Club` 
(club_name, club_email, club_description, creation_date, club_condition, club_status)
VALUES
('Computer Science Society', 'cssociety@uwindsor.ca', 
 'A student-led group supporting CS students through academic help, networking events, and tech workshops.',
 '2021-09-10', 'none', 'active'),

('Muslim Student Association', 'msa@uwindsor.ca',
 'A community space for Muslim students focused on faith, social events, and support on campus.',
 '2022-01-12', 'none', 'active'),

('Women in Engineering', 'womenineng@uwindsor.ca',
 'Empowering women in engineering through mentorship, panels, and professional development.',
 '2021-10-05', 'women_only', 'active'),

('Science Society', 'science.society@uwindsor.ca',
 'Represents all science students with academic resources, events, and faculty engagement.',
 '2020-09-01', 'none', 'active'),

('Punjabi Students Association', 'uwindsor.punjabi@gmail.com',
 'A vibrant cultural and social club celebrating Punjabi traditions, music, and student life.',
 '2023-02-07', 'none', 'active'),

('Odette Commerce Society', 'odette.commerce@uwindsor.ca',
 'Supports business students with competitions, networking, and industry engagement.',
 '2021-11-14', 'none', 'active'),

('Psychology Student Association', 'uwinpsychclub@gmail.com',
 'Offering academic support, events, and volunteer opportunities for psychology students.',
 '2022-03-18', 'none', 'active'),

('Caribbean & African Organization of Students', 'caoswindsor@uwindsor.ca',
 'Promoting Caribbean and African culture through social events, advocacy, and student unity.',
 '2021-09-22', 'none', 'active'),

('Women in Computer Science', 'wics@uwindsor.ca',
 'Supporting women in computing through mentorship, collaboration, and skill development.',
 '2022-01-20', 'women_only', 'active'),

('Engineering Society', 'engsoc@uwindsor.ca',
 'The official representative group for engineering students, hosting academic and social events.',
 '2020-09-03', 'none', 'active'),

('Black Student-Athlete Alliance', 'bsaa@uwindsor.ca',
 'A supportive community focused on advocacy, wellness, and celebrating Black student-athletes.',
 '2023-01-29', 'none', 'active'),

('Filipino Student Association', 'fsauwindsor@gmail.com',
 'Celebrating Filipino culture and creating a supportive community through events and socials.',
 '2023-03-10', 'none', 'active'),

('Tennis Club', 'uwin.tennis@uwindsor.ca',
 'A recreational club welcoming all skill levels to play, practice, and compete together.',
 '2022-05-19', 'none', 'active'),

('Formula Electric', 'fsae.uwindsor@uwindsor.ca',
 'A technical student team designing and building an electric Formula-style race car.',
 '2021-09-15', 'none', 'active'),

('Run Club', 'uwinrunclub@uwindsor.ca',
 'A community for runners of all levels offering campus runs, training groups, and challenges.',
 '2023-04-11', 'none', 'active'),

('Pre-Medical Society', 'premedsociety@uwindsor.ca',
 'Assisting students pursuing medical careers through resources, MCAT prep, and mentorship.',
 '2022-08-30', 'undergrad_only', 'active'),

('South-East Asian Student Association', 'seasa@uwindsor.ca',
 'Connecting South-East Asian students through cultural events, bonding, and academic support.',
 '2023-02-25', 'none', 'active'),

('Women in Leadership', 'wilwindsor@uwindsor.ca',
 'Empowering women across all faculties with workshops, mentorship, and networking.',
 '2022-03-05', 'women_only', 'active'),

('Board Game Club', 'boardgames.uwin@uwindsor.ca',
 'A fun, casual club where students meet weekly to play board games and socialize.',
 '2023-01-10', 'none', 'active'),

('UWin Cybersecurity Club', 'uwincyber@uwindsor.ca',
 'A student group focused on cybersecurity, ethical hacking, workshops, and competitions.',
 '2022-06-15', 'none', 'active'),

('Aqua Aid', 'aquaaid@uwindsor.ca',
 'A volunteer-focused club organizing water donation drives and community support events.',
 '2021-11-03', 'none', 'active'),

('Christian Student Gathering', 'uwindsor.csg@gmail.com',
 'A faith-based group hosting worship nights, Bible studies, and community gatherings.',
 '2020-09-17', 'none', 'active'),

('Model UN Windsor', 'uwinmodelun@uwindsor.ca',
 'Simulating United Nations debates and developing diplomacy, negotiation, and public speaking skills.',
 '2021-02-14', 'none', 'active'),

('Undergraduate Chemistry Club', 'chemclub@uwindsor.ca',
 'A group dedicated to supporting chemistry students with academic help and fun lab-based events.',
 '2022-04-09', 'none', 'active'),

('Bhangra & Giddha Club', 'bhangra.giddha@uwindsor.ca',
 'Celebrating Punjabi dance culture with weekly practices and energetic performances.',
 '2023-09-02', 'none', 'active');


-- ==========================
-- USER INTERESTS (2–3 per user)
-- ==========================

-- 1 Anika Khan (Science)
INSERT INTO User_Interests VALUES (1, 2), (1, 3), (1, 6);

-- 2 Shameer Sheikh (Engineering)
INSERT INTO User_Interests VALUES (2, 4), (2, 3);

-- 3 Emily Tran (Engineering)
INSERT INTO User_Interests VALUES (3, 4), (3, 3);

-- 4 Daniel Lee (Science - CS)
INSERT INTO User_Interests VALUES (4, 14), (4, 3), (4, 2);

-- 5 Sophia Martinez (Business)
INSERT INTO User_Interests VALUES (5, 5), (5, 2);

-- 6 Noah Singh (Science)
INSERT INTO User_Interests VALUES (6, 3), (6, 2);

-- 7 Ava Patel (AHSS)
INSERT INTO User_Interests VALUES (7, 9), (7, 1), (7, 10);

-- 8 Ethan Brown (AHSS)
INSERT INTO User_Interests VALUES (8, 9), (8, 1);

-- 9 Maya Sharma (HK)
INSERT INTO User_Interests VALUES (9, 6), (9, 8);

-- 10 Lucas Wong (Engineering)
INSERT INTO User_Interests VALUES (10, 4), (10, 3);

-- 11 Isabella Costa (Business grad)
INSERT INTO User_Interests VALUES (11, 5), (11, 11);

-- 12 Benjamin Haddad (Law grad)
INSERT INTO User_Interests VALUES (12, 10), (12, 11);

-- 13 Aria Gupta (Science)
INSERT INTO User_Interests VALUES (13, 3), (13, 2);

-- 14 Logan Mitchell (Science - CS)
INSERT INTO User_Interests VALUES (14, 14), (14, 3);

-- 15 Zara Hassan (Nursing)
INSERT INTO User_Interests VALUES (15, 6), (15, 7);

-- 16 Oliver D’Souza (Science)
INSERT INTO User_Interests VALUES (16, 2), (16, 3);

-- 17 Chloe Kim (AHSS)
INSERT INTO User_Interests VALUES (17, 9), (17, 1);

-- 18 Nathan Carter (HK)
INSERT INTO User_Interests VALUES (18, 6), (18, 8);

-- 19 Sofia Rossi (Education)
INSERT INTO User_Interests VALUES (19, 10), (19, 15), (19, 7);

-- 20 Aiden Park (Engineering)
INSERT INTO User_Interests VALUES (20, 3), (20, 4);

-- 21 Layla Morgan (Business)
INSERT INTO User_Interests VALUES (21, 5), (21, 11);

-- 22 Gabriel White (Science - CS)
INSERT INTO User_Interests VALUES (22, 14), (22, 3);

-- 23 Hannah Baker (Science)
INSERT INTO User_Interests VALUES (23, 3), (23, 2);

-- 24 Leo Nguyen (Engineering)
INSERT INTO User_Interests VALUES (24, 4), (24, 3);

-- 25 Mira Alami (AHSS)
INSERT INTO User_Interests VALUES (25, 1), (25, 9), (25, 12);

-- 26 Isaac Coleman (HK)
INSERT INTO User_Interests VALUES (26, 6), (26, 8);

-- 27 Selena Kouris (Business)
INSERT INTO User_Interests VALUES (27, 5), (27, 2);

-- 28 Jayden Frost (Science)
INSERT INTO User_Interests VALUES (28, 3), (28, 2);

-- 29 Amira Rahman (Engineering)
INSERT INTO User_Interests VALUES (29, 4), (29, 3);

-- 30 Marcus James (Science - CS)
INSERT INTO User_Interests VALUES (30, 14), (30, 3), (30, 2);


-- ==========================
-- CLUB TAGS (1–2 per club)
-- ==========================

-- 1 Computer Science Society
INSERT INTO Club_Tags VALUES (1, 3), (1, 14);

-- 2 Muslim Student Association
INSERT INTO Club_Tags VALUES (2, 13);

-- 3 Women in Engineering
INSERT INTO Club_Tags VALUES (3, 4), (3, 11);

-- 4 Science Society
INSERT INTO Club_Tags VALUES (4, 2), (4, 3);

-- 5 Punjabi Students Association
INSERT INTO Club_Tags VALUES (5, 1), (5, 12);

-- 6 Odette Commerce Society
INSERT INTO Club_Tags VALUES (6, 5), (6, 2);

-- 7 Psychology Student Association
INSERT INTO Club_Tags VALUES (7, 2), (7, 6);

-- 8 Caribbean & African Organization of Students
INSERT INTO Club_Tags VALUES (8, 1), (8, 12);

-- 9 Women in Computer Science
INSERT INTO Club_Tags VALUES (9, 14), (9, 11);

-- 10 Engineering Society
INSERT INTO Club_Tags VALUES (10, 4), (10, 2);

-- 11 Black Student-Athlete Alliance
INSERT INTO Club_Tags VALUES (11, 11), (11, 8);

-- 12 Filipino Student Association
INSERT INTO Club_Tags VALUES (12, 1), (12, 12);

-- 13 Tennis Club
INSERT INTO Club_Tags VALUES (13, 8);

-- 14 Formula Electric
INSERT INTO Club_Tags VALUES (14, 4), (14, 3);

-- 15 Run Club
INSERT INTO Club_Tags VALUES (15, 8), (15, 6);

-- 16 Pre-Medical Society
INSERT INTO Club_Tags VALUES (16, 2), (16, 6);

-- 17 South-East Asian Student Association
INSERT INTO Club_Tags VALUES (17, 1), (17, 12);

-- 18 Women in Leadership
INSERT INTO Club_Tags VALUES (18, 11), (18, 5);

-- 19 Board Game Club
INSERT INTO Club_Tags VALUES (19, 9);

-- 20 UWin Cybersecurity Club
INSERT INTO Club_Tags VALUES (20, 14), (20, 3);

-- 21 Aqua Aid
INSERT INTO Club_Tags VALUES (21, 7), (21, 15);

-- 22 Christian Student Gathering
INSERT INTO Club_Tags VALUES (22, 13);

-- 23 Model UN Windsor
INSERT INTO Club_Tags VALUES (23, 10), (23, 12);

-- 24 Undergraduate Chemistry Club
INSERT INTO Club_Tags VALUES (24, 2), (24, 3);

-- 25 Bhangra & Giddha Club
INSERT INTO Club_Tags VALUES (25, 1), (25, 9);


-- ==========================
-- MEMBERSHIP (8–12 per club)
-- ==========================

-- 1 Computer Science Society
INSERT INTO Membership (user_id, club_id) VALUES 
(1,1),(4,1),(10,1),(14,1),(20,1),(22,1),(30,1),(28,1),(24,1);

-- 2 Muslim Student Association
INSERT INTO Membership (user_id, club_id) VALUES
(1,2),(5,2),(7,2),(12,2),(15,2),(17,2),(19,2),(25,2);

-- 3 Women in Engineering
INSERT INTO Membership (user_id, club_id) VALUES
(3,3),(10,3),(20,3),(24,3),(29,3),(1,3),(21,3),(13,3);

-- 4 Science Society
INSERT INTO Membership (user_id, club_id) VALUES
(1,4),(6,4),(9,4),(13,4),(14,4),(16,4),(23,4),(28,4),(30,4);

-- 5 Punjabi Students Association
INSERT INTO Membership (user_id, club_id) VALUES
(1,5),(5,5),(7,5),(8,5),(12,5),(17,5),(25,5),(29,5),(21,5);

-- 6 Odette Commerce Society
INSERT INTO Membership (user_id, club_id) VALUES
(5,6),(11,6),(21,6),(27,6),(3,6),(12,6),(19,6),(1,6);

-- 7 Psychology Student Association
INSERT INTO Membership (user_id, club_id) VALUES
(7,7),(9,7),(13,7),(17,7),(19,7),(23,7),(25,7),(28,7);

-- 8 Caribbean & African Organization of Students
INSERT INTO Membership (user_id, club_id) VALUES
(5,8),(8,8),(11,8),(12,8),(17,8),(21,8),(25,8),(27,8),(30,8);

-- 9 Women in Computer Science
INSERT INTO Membership (user_id, club_id) VALUES
(1,9),(3,9),(4,9),(14,9),(17,9),(21,9),(25,9),(29,9);

-- 10 Engineering Society
INSERT INTO Membership (user_id, club_id) VALUES
(2,10),(3,10),(10,10),(20,10),(24,10),(14,10),(29,10),(30,10);

-- 11 Black Student-Athlete Alliance
INSERT INTO Membership (user_id, club_id) VALUES
(6,11),(8,11),(9,11),(12,11),(17,11),(18,11),(26,11),(30,11);

-- 12 Filipino Student Association
INSERT INTO Membership (user_id, club_id) VALUES
(5,12),(7,12),(8,12),(11,12),(17,12),(21,12),(25,12),(1,12);

-- 13 Tennis Club
INSERT INTO Membership (user_id, club_id) VALUES
(6,13),(9,13),(10,13),(18,13),(20,13),(24,13),(26,13),(27,13),(30,13);

-- 14 Formula Electric
INSERT INTO Membership (user_id, club_id) VALUES
(2,14),(3,14),(10,14),(14,14),(20,14),(24,14),(29,14),(1,14);

-- 15 Run Club
INSERT INTO Membership (user_id, club_id) VALUES
(6,15),(9,15),(10,15),(18,15),(20,15),(24,15),(26,15),(30,15);

-- 16 Pre-Medical Society
INSERT INTO Membership (user_id, club_id) VALUES
(6,16),(9,16),(13,16),(15,16),(18,16),(23,16),(28,16),(30,16);

-- 17 South-East Asian Student Association
INSERT INTO Membership (user_id, club_id) VALUES
(5,17),(7,17),(8,17),(17,17),(21,17),(25,17),(29,17),(12,17);

-- 18 Women in Leadership
INSERT INTO Membership (user_id, club_id) VALUES
(5,18),(7,18),(11,18),(15,18),(17,18),(21,18),(25,18),(27,18);

-- 19 Board Game Club
INSERT INTO Membership (user_id, club_id) VALUES
(1,19),(4,19),(7,19),(8,19),(14,19),(17,19),(21,19),(25,19),(30,19);

-- 20 UWin Cybersecurity Club
INSERT INTO Membership (user_id, club_id) VALUES
(4,20),(10,20),(14,20),(20,20),(22,20),(24,20),(28,20),(30,20);

-- 21 Aqua Aid
INSERT INTO Membership (user_id, club_id) VALUES
(7,21),(9,21),(11,21),(12,21),(15,21),(19,21),(25,21),(27,21);

-- 22 Christian Student Gathering
INSERT INTO Membership (user_id, club_id) VALUES
(5,22),(7,22),(11,22),(12,22),(17,22),(19,22),(21,22),(25,22);

-- 23 Model UN Windsor
INSERT INTO Membership (user_id, club_id) VALUES
(5,23),(8,23),(11,23),(12,23),(17,23),(19,23),(25,23),(27,23),(30,23);

-- 24 Undergraduate Chemistry Club
INSERT INTO Membership (user_id, club_id) VALUES
(1,24),(6,24),(13,24),(14,24),(16,24),(23,24),(28,24),(30,24);

-- 25 Bhangra & Giddha Club
INSERT INTO Membership (user_id, club_id) VALUES
(5,25),(7,25),(8,25),(17,25),(21,25),(25,25),(29,25),(30,25);


-- ==========================
-- EXECUTIVES (1-2 per club)
-- ==========================

-- 1 Computer Science Society (CS → choose CS-focused user)
INSERT INTO Executive VALUES (14, 1, 'executive');
INSERT INTO Executive VALUES (30, 1, 'executive');

-- 2 Muslim Student Association
INSERT INTO Executive VALUES (1, 2, 'executive');

-- 3 Women in Engineering (engineering + female)
INSERT INTO Executive VALUES (3, 3, 'executive');
INSERT INTO Executive VALUES (29, 3, 'executive'); 

-- 4 Science Society
INSERT INTO Executive VALUES (6, 4, 'executive');
INSERT INTO Executive VALUES (1, 4, 'executive');

-- 5 Punjabi Students Association
INSERT INTO Executive VALUES (7, 5, 'executive');
INSERT INTO Executive VALUES (14, 5, 'executive');

-- 6 Odette Commerce Society
INSERT INTO Executive VALUES (21, 6, 'executive');
INSERT INTO Executive VALUES (11, 6, 'executive');

-- 7 Psychology Student Association
INSERT INTO Executive VALUES (23, 7, 'executive');

-- 8 Caribbean & African Organization of Students
INSERT INTO Executive VALUES (12, 8, 'executive');
INSERT INTO Executive VALUES (3, 8, 'executive');

-- 9 Women in Computer Science (female + tech)
INSERT INTO Executive VALUES (17, 9, 'executive');
INSERT INTO Executive VALUES (3, 9, 'executive');

-- 10 Engineering Society
INSERT INTO Executive VALUES (24, 10, 'executive');

-- 11 Black Student-Athlete Alliance
INSERT INTO Executive VALUES (18, 11, 'executive');

-- 12 Filipino Student Association
INSERT INTO Executive VALUES (25, 12, 'executive');

-- 13 Tennis Club
INSERT INTO Executive VALUES (18, 13, 'executive');

-- 14 Formula Electric (engineering)
INSERT INTO Executive VALUES (20, 14, 'executive');

-- 15 Run Club (active HK student fits well)
INSERT INTO Executive VALUES (9, 15, 'executive');

-- 16 Pre-Medical Society (science or HK)
INSERT INTO Executive VALUES (13, 16, 'executive');
INSERT INTO Executive VALUES (6, 16, 'executive');

-- 17 South-East Asian Student Association
INSERT INTO Executive VALUES (5, 17, 'executive');

-- 18 Women in Leadership (female business student fits)
INSERT INTO Executive VALUES (27, 18, 'executive');

-- 19 Board Game Club (neutral; random member)
INSERT INTO Executive VALUES (1, 19, 'executive');

-- 20 UWin Cybersecurity Club (CS → tech student)
INSERT INTO Executive VALUES (22, 20, 'executive');
INSERT INTO Executive VALUES (3, 20, 'executive');

-- 21 Aqua Aid (volunteering oriented)
INSERT INTO Executive VALUES (19, 21, 'executive');

-- 22 Christian Student Gathering
INSERT INTO Executive VALUES (11, 22, 'executive');

-- 23 Model UN Windsor (humanities / AHSS)
INSERT INTO Executive VALUES (17, 23, 'executive');
INSERT INTO Executive VALUES (7, 23, 'executive');

-- 24 Undergraduate Chemistry Club (science)
INSERT INTO Executive VALUES (13, 24, 'executive');

-- 25 Bhangra & Giddha Club
INSERT INTO Executive VALUES (29, 25, 'executive');


-- -------------------------
-- Events - Active Clubs (IDs 1–30)
-- -------------------------

-- Club 1 — Computer Science Society (events 1, 2, 3)
INSERT INTO Event VALUES
(1, 1, 'Fall Coding Jam 2024',
 'A collaborative coding event where students pair up to solve algorithmic challenges. Participants work through timed puzzles and receive feedback from upper-year mentors.',
 'Essex Hall', '2024-10-12 14:00:00', 80, 'approved', 'none', TRUE, 0.00),

(2, 1, 'Winter Hack Night',
 'A casual evening hackathon focused on building small web and mobile prototypes. Students can drop in, form teams, and work on mini-projects with snacks and mentor support.',
 'Advanced Computing and Innovation Hub', '2025-12-06 18:00:00', 100, 'approved', 'none', TRUE, 0.00),

(3, 1, 'January Peer-Tutoring Kickoff',
 'An event introducing the CS Society’s winter tutoring program, where mentors explain how to join study groups and book help sessions. The session includes short demonstrations of debugging workflows.',
 'Leddy Library', '2026-01-15 17:00:00', 60, 'pending', 'none', TRUE, 0.00);

-- Club 3 — Women in Engineering (events 4, 5, 6)
INSERT INTO Event VALUES
(4, 3, 'Women in STEM Meet-and-Greet',
 'A networking mixer that brings together women from engineering and science programs. Attendees connect over refreshments and discuss mentorship opportunities.',
 'Ed Lumley Centre for Engineering Innovation', '2025-03-20 16:00:00', 60, 'approved', 'women_only', TRUE, 0.00),

(5, 3, 'December Industry Mentorship Night',
 'Local women engineers from industry will speak about career pathways, internships, and navigating the workplace. Students can meet mentors and sign up for shadowing opportunities.',
 'CEI 1100', '2025-12-08 18:00:00', 75, 'approved', 'women_only', TRUE, 0.00),

(6, 3, 'Engineering Wellness Workshop',
 'A guided workshop focused on mental wellness and stress management during exam season. Alumni share strategies for balancing academics with personal wellbeing.',
 'Human Kinetics Building', '2026-01-10 14:00:00', 50, 'pending', 'women_only', TRUE, 0.00);

-- Club 4 — Science Society (events 7, 8, 9)
INSERT INTO Event VALUES
(7, 4, 'Science Student Welcome Social',
 'A social kickoff where new and returning science students connect with peers and faculty. The event includes games, Q&A sessions, and light refreshments.',
 'CAW Student Centre', '2024-09-07 13:00:00', 120, 'approved', 'undergrad_only', TRUE, 0.00),

(8, 4, 'End-of-Semester Study Hangout',
 'A relaxed group study afternoon offering collaborative spaces, snacks, and support from Science Society volunteers. Ideal for students finishing fall semester assignments.',
 'Leddy Library', '2025-12-05 12:00:00', 90, 'approved', 'none', TRUE, 0.00),

(9, 4, 'January Research Opportunities Expo',
 'Faculty members and undergraduate researchers present winter research openings. Students can explore labs, ask questions, and learn how to join active research teams.',
 'Essex Centre of Research (CORe)', '2026-01-20 14:00:00', 120, 'pending', 'undergrad_only', TRUE, 0.00);

-- Club 5 — Punjabi Students Association (events 10, 11, 12)
INSERT INTO Event VALUES
(10, 5, 'PSA Culture Night 2024',
 'A lively celebration featuring music, cultural activities, and performances from PSA members. Guests enjoy food and a warm, welcoming atmosphere.',
 'CAW Student Centre', '2024-11-18 19:00:00', 150, 'approved', 'none', TRUE, 5.00),

(11, 5, 'December Bhangra Workshop',
 'An energetic dance workshop open to all experience levels. Instructors teach foundational Bhangra steps while sharing the cultural history behind the art.',
 'Jackman Dramatic Arts Centre', '2025-12-09 17:00:00', 80, 'approved', 'none', TRUE, 0.00),

(12, 5, 'January Cultural Mixer',
 'A meet-and-mingle session where students connect over music, tea, and conversation. PSA executives introduce winter plans and invite volunteers to join committees.',
 'Vanier Hall', '2026-01-18 18:00:00', 100, 'pending', 'none', TRUE, 0.00);

-- Club 6 — Odette Commerce Society (events 13, 14, 15)
INSERT INTO Event VALUES
(13, 6, 'Fall Networking Mixer 2024',
 'Business students meet with Odette alumni to discuss co-op opportunities and career paths. The event features short talks and one-on-one conversation tables.',
 'Odette School of Business', '2024-10-22 17:00:00', 120, 'approved', 'none', TRUE, 0.00),

(14, 6, 'December Resume Review Night',
 'Peer mentors and Odette alumni review resumes and offer personalized feedback. Students are encouraged to bring printed copies for live editing and career tips.',
 'Odette School of Business', '2025-12-11 18:00:00', 90, 'approved', 'none', TRUE, 0.00),

(15, 6, 'January Case Competition Prep Session',
 'A workshop introducing case analysis techniques used in business competitions. Attendees learn frameworks, strategies, and how to approach real case prompts.',
 'Odette School of Business', '2026-01-17 14:00:00', 80, 'pending', 'none', TRUE, 0.00);

-- Club 7 — Psychology Student Association (events 16, 17, 18)
INSERT INTO Event VALUES
(16, 7, 'Cognitive Science Guest Talk',
 'A visiting researcher from Toronto presents new findings on cognitive bias and human reasoning. Students participate in a Q&A and casual discussion afterward.',
 'Erie Hall', '2025-02-16 16:00:00', 100, 'approved', 'none', TRUE, 0.00),

(17, 7, 'December Exam Wellness Session',
 'A calming workshop offering tips for stress reduction, mindfulness, and preparing the brain for memory-heavy exams. Includes guided breathing and optional journaling activities.',
 'Dillon Hall', '2025-12-04 15:00:00', 60, 'approved', 'none', TRUE, 0.00),

(18, 7, 'January Psych Research Poster Showcase',
 'Undergraduate researchers display posters and discuss their projects with attendees. A great opportunity for students interested in joining labs for the winter semester.',
 'Toldo Health Education Centre', '2026-01-14 12:00:00', 80, 'pending', 'undergrad_only', TRUE, 0.00);

-- Club 10 — Engineering Society (events 19, 20, 21)
INSERT INTO Event VALUES
(19, 10, 'Frosh Week Engineering Bash',
 'A long-running tradition welcoming first-year engineering students with games and team challenges. Upper-year mentors help newcomers feel at home in the faculty.',
 'CEI Courtyard', '2024-09-04 13:00:00', 200, 'approved', 'first_year_only', TRUE, 0.00),

(20, 10, 'December Design Build Challenge',
 'Teams design and test small mechanical builds using provided materials. The challenge focuses on teamwork, problem-solving, and creativity under time pressure.',
 'Ed Lumley Centre for Engineering Innovation', '2025-12-10 16:00:00', 120, 'approved', 'none', TRUE, 0.00),

(21, 10, 'January Industry Panel Night',
 'Industry professionals from multiple engineering fields share insights on job expectations, career planning, and networking strategies for new graduates.',
 'CEI 1101', '2026-01-21 18:00:00', 90, 'pending', 'none', TRUE, 0.00);

-- Club 13 — Tennis Club (events 22, 23, 24)
INSERT INTO Event VALUES
(22, 13, 'Fall Open Court 2024',
 'An open-court session where players of all skill levels meet to hit rallies and learn basic technique. Racquets are provided for new members.',
 'St. Denis Centre', '2024-09-28 11:00:00', 60, 'approved', 'none', TRUE, 0.00),

(23, 13, 'December Indoor Match Day',
 'A friendly indoor match day to keep players active through exam season. Matches are grouped by skill level so everyone can participate comfortably.',
 'Toldo Lancer Centre', '2025-12-07 13:00:00', 40, 'approved', 'none', TRUE, 0.00),

(24, 13, 'January Skills Development Clinic',
 'A guided clinic featuring drills that target footwork, serve improvements, and rally consistency. Perfect for players looking to refine their technique.',
 'Toldo Lancer Centre', '2026-01-19 14:00:00', 40, 'pending', 'none', TRUE, 0.00);

-- Club 16 — Pre-Medical Society (events 25, 26, 27)
INSERT INTO Event VALUES
(25, 16, 'MCAT Strategy Session 2024',
 'Upper-year students share tips for MCAT preparation, study timelines, and recommended resources. Attendees receive sample study schedules and flashcard sets.',
 'Toldo Health Education Centre', '2024-11-02 15:00:00', 100, 'approved', 'undergrad_only', TRUE, 0.00),

(26, 16, 'December Winter Med-School Panel',
 'Medical students and alumni discuss their experiences applying to medical school, writing personal statements, and preparing for interviews. Includes an open Q&A.',
 'Toldo Health Education Centre', '2025-12-12 17:00:00', 120, 'approved', 'undergrad_only', TRUE, 0.00),

(27, 16, 'January Clinical Simulation Workshop',
 'Participants rotate through simulation stations practicing vitals, patient communication, and clinical reasoning. Limited spots ensure hands-on engagement.',
 'Dr. Murray O’Neil Medical Education Centre', '2026-01-22 13:00:00', 50, 'pending', 'undergrad_only', TRUE, 15.00);

-- Club 20 — UWin Cybersecurity Club (events 28, 29, 30)
INSERT INTO Event VALUES
(28, 20, 'Intro to Ethical Hacking 2025',
 'A hands-on workshop introducing students to common penetration testing tools and techniques. Participants experiment in a safe virtual lab environment.',
 'Advanced Computing and Innovation Hub', '2025-03-05 14:00:00', 80, 'approved', 'none', TRUE, 0.00),

(29, 20, 'December Capture-the-Flag Scrimmage',
 'Teams compete in a beginner-friendly cybersecurity challenge featuring puzzles in cryptography, forensics, and network security. No prior experience required.',
 'Essex Hall', '2025-12-13 16:00:00', 60, 'approved', 'none', TRUE, 0.00),

(30, 20, 'January Malware Analysis Lab',
 'An advanced workshop where students learn the basics of analyzing benign malware samples in an isolated environment. Safety protocols are reviewed thoroughly.',
 'Advanced Computing and Innovation Hub', '2026-01-24 11:00:00', 40, 'pending', 'none', TRUE, 0.00);


-- -------------------------
-- Events - Less Active Clubs (IDs 31–50)
-- -------------------------

INSERT INTO Event VALUES
(31, 2, 'MSA Welcome Dinner 2025',
 'An annual gathering that welcomes new members into the MSA community. The evening includes a shared meal, introductions, and an overview of upcoming faith-based and social events.',
 'CAW Student Centre', '2025-09-19 18:00:00', 120, 'approved', 'none', TRUE, 3.00),

(32, 2, 'December Prayer & Reflection Night',
 'A quiet evening event offering group prayer, reflection circles, and community bonding. Students are encouraged to bring questions, share experiences, and support one another.',
 'Assumption Hall', '2025-12-15 17:00:00', 100, 'approved', 'none', TRUE, 0.00);

INSERT INTO Event VALUES
(33, 8, 'CAOS Fall Social 2024',
 'A vibrant social night featuring Afro-Caribbean music, games, and refreshments. Students spend the evening meeting new people and celebrating cultural diversity.',
 'Vanier Hall', '2024-11-03 19:00:00', 180, 'approved', 'none', TRUE, 2.00),

(34, 8, 'December Holiday Games Night',
 'A relaxed holiday-themed evening with games, music, and creative activities designed to help students unwind before exams. Everyone is welcome to join the fun.',
 'CAW Student Centre', '2025-12-14 18:00:00', 100, 'approved', 'none', TRUE, 0.00);

INSERT INTO Event VALUES
(35, 9, 'WiCS Mentorship Kickoff 2025',
 'An introduction to the WiCS mentorship program, connecting new members with upper-year students for guidance, academic support, and community building. The session ends with informal networking.',
 'Essex Hall', '2025-05-10 15:00:00', 70, 'approved', 'women_only', TRUE, 0.00),

(36, 9, 'January Tech Career Prep Session',
 'A workshop designed to help students prepare for winter internship applications, featuring resume tips, technical interview practice, and guidance from female tech professionals.',
 'Advanced Computing and Innovation Hub', '2026-01-12 17:00:00', 80, 'approved', 'women_only', TRUE, 0.00);

INSERT INTO Event VALUES
(37, 11, 'Fitness & Wellness Seminar',
 'A seminar where trainers discuss sustainable workout routines, nutrition for student-athletes, and mental health during training seasons. Attendees also participate in a short group workout.',
 'St. Denis Centre', '2025-03-08 11:00:00', 60, 'approved', 'none', TRUE, 0.00),

(38, 11, 'January Community Sport Day',
 'A friendly community event offering open courts for basketball, volleyball, and other activities. Athletes can connect, practice, and help new members build confidence in sports environments.',
 'Toldo Lancer Centre', '2026-01-16 14:00:00', 80, 'approved', 'none', TRUE, 0.00);

INSERT INTO Event VALUES
(39, 18, 'Women’s Leadership Panel 2025',
 'Female leaders from across campus and local businesses share insights on confidence, communication, and leadership development. The event concludes with an open networking period.',
 'Odette School of Business', '2025-04-14 17:00:00', 90, 'approved', 'women_only', TRUE, 0.00),

(40, 18, 'December Goal-Setting Workshop',
 'A structured workshop that helps participants form actionable academic, personal, and career goals for the upcoming semester. It includes guided reflection and planner templates.',
 'Dillon Hall', '2025-12-16 16:00:00', 70, 'approved', 'women_only', TRUE, 0.00);

INSERT INTO Event VALUES
(41, 12, 'Filipino Heritage Night 2024',
 'A celebration of Filipino culture featuring traditional games, music, and food. Students gather to enjoy performances and learn about Filipino traditions.',
 'Vanier Hall', '2024-10-15 18:00:00', 120, 'approved', 'none', TRUE, 0.00);

INSERT INTO Event VALUES
(42, 14, 'Workshop: Intro to Electric Vehicle Systems',
 'A technical workshop covering the basics of EV systems, battery fundamentals, and safe handling procedures. Perfect for students interested in joining the design team.',
 'Ed Lumley Centre for Engineering Innovation', '2025-02-22 13:00:00', 60, 'approved', 'none', TRUE, 0.00);

INSERT INTO Event VALUES
(43, 15, 'Fall Community Run 2025',
 'A friendly 5km run open to all students, held along the riverside trail. The event encourages staying active and building a supportive fitness community.',
 'Toldo Lancer Centre', '2025-09-21 09:00:00', 100, 'approved', 'none', TRUE, 0.00);

INSERT INTO Event VALUES
(44, 17, 'SEASA Cultural Mixer 2024',
 'A social gathering where students enjoy music, snacks, and conversations celebrating Southeast Asian cultures. The event creates a relaxing and welcoming environment for new members.',
 'CAW Student Centre', '2024-11-08 18:00:00', 100, 'approved', 'none', TRUE, 0.00);

INSERT INTO Event VALUES
(45, 19, 'Board Game Night 2025',
 'A relaxed evening of board games, strategy matches, and socializing. New players are introduced to a variety of classic and modern games.',
 'Leddy Library', '2025-01-29 17:00:00', 40, 'approved', 'none', TRUE, 0.00);

INSERT INTO Event VALUES
(46, 21, 'Water Drive Volunteer Day',
 'Students organize and deliver bottled water donations to local shelters. Volunteers sort supplies, pack deliveries, and assist coordinators throughout the day.',
 'Welcome Centre', '2025-05-02 12:00:00', 50, 'approved', 'none', TRUE, 0.00);

INSERT INTO Event VALUES
(47, 22, 'Evening Worship & Fellowship 2025',
 'A peaceful evening of worship, prayer, and reflection, followed by a small-group discussion. Students share stories and encourage one another in faith.',
 'Assumption Hall', '2025-03-12 18:00:00', 60, 'approved', 'none', TRUE, 0.00);

INSERT INTO Event VALUES
(48, 23, 'MUN Debate Practice 2025',
 'Participants practice formal debate formats and resolution writing ahead of upcoming conferences. The session helps new delegates learn parliamentary procedures.',
 'Dillon Hall', '2025-10-07 16:00:00', 40, 'approved', 'none', TRUE, 0.00);

INSERT INTO Event VALUES
(49, 24, 'Intro to Lab Skills Workshop',
 'A hands-on workshop teaching pipetting, titration basics, and essential laboratory safety skills. Great for first-year students entering their first lab courses.',
 'Essex Hall', '2025-09-14 14:00:00', 50, 'approved', 'undergrad_only', TRUE, 0.00);

INSERT INTO Event VALUES
(50, 25, 'Fall Dance Workshop 2024',
 'A lively workshop where students learn fundamental Bhangra and Giddha steps in a friendly environment. No experience is required, and all movements are taught step-by-step.',
 'Jackman Dramatic Arts Centre', '2024-10-19 13:00:00', 80, 'approved', 'none', TRUE, 0.00);



-- -------------------------
-- Registration
-- -------------------------

-- Event 1 — Fall Coding Jam 2024 (Club 1)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(1,1,'2024-09-25 15:00:00'),
(4,1,'2024-09-26 14:30:00'),
(10,1,'2024-09-27 16:00:00'),
(14,1,'2024-09-28 12:15:00'),
(20,1,'2024-09-29 11:40:00'),
(22,1,'2024-10-01 17:30:00'),
(30,1,'2024-10-02 13:10:00'),
(24,1,'2024-10-03 16:10:00'),
(13,1,'2024-10-05 12:00:00'),
(21,1,'2024-10-07 15:30:00'),
(28,1,'2024-10-08 14:00:00'),
(3,1,'2024-10-10 11:00:00');

-- Event 2 — Winter Hack Night (Club 1)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(14,2,'2025-11-28 16:10:00'),
(1,2,'2025-11-30 14:00:00'),
(10,2,'2025-12-01 17:20:00'),
(22,2,'2025-12-02 12:40:00'),
(24,2,'2025-12-03 18:00:00'),
(30,2,'2025-12-04 13:20:00'),
(3,2,'2025-12-05 15:00:00');

-- Event 4 — Women in STEM Meet-and-Greet (Club 3)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(3,4,'2025-03-01 13:30:00'),
(17,4,'2025-03-03 16:10:00'),
(21,4,'2025-03-04 14:00:00'),
(29,4,'2025-03-05 15:20:00'),
(7,4,'2025-03-07 13:45:00'),
(25,4,'2025-03-09 11:50:00');

-- Event 5 — December Industry Mentorship Night (Club 3)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(3,5,'2025-11-29 16:00:00'),
(17,5,'2025-12-01 14:30:00'),
(21,5,'2025-12-02 18:40:00'),
(25,5,'2025-12-03 13:10:00'),
(29,5,'2025-12-04 17:45:00');

-- Event 7 — Science Student Welcome Social (Club 4)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(1,7,'2024-08-29 14:10:00'),
(6,7,'2024-08-30 13:00:00'),
(9,7,'2024-09-01 17:00:00'),
(13,7,'2024-09-02 12:30:00'),
(14,7,'2024-09-02 14:50:00'),
(16,7,'2024-09-03 15:45:00'),
(23,7,'2024-09-04 16:00:00'),
(28,7,'2024-09-04 17:30:00'),
(30,7,'2024-09-05 13:40:00'),
(18,7,'2024-09-05 12:00:00'),
(20,7,'2024-09-06 11:20:00');

-- Event 8 — End-of-Semester Study Hangout (Club 4)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(1,8,'2025-11-27 15:10:00'),
(6,8,'2025-11-28 16:00:00'),
(13,8,'2025-11-29 13:20:00'),
(16,8,'2025-11-30 14:50:00'),
(14,8,'2025-12-01 15:40:00'),
(23,8,'2025-12-02 12:30:00');

-- Event 10 — PSA Culture Night 2024 (Club 5)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(1,10,'2024-10-10 14:00:00'),
(5,10,'2024-10-11 17:00:00'),
(7,10,'2024-10-12 18:00:00'),
(8,10,'2024-10-12 16:50:00'),
(12,10,'2024-10-13 11:20:00'),
(17,10,'2024-10-13 14:30:00'),
(25,10,'2024-10-14 15:40:00'),
(29,10,'2024-10-15 13:10:00'),
(21,10,'2024-10-15 12:00:00'),
(3,10,'2024-10-16 16:00:00'),
(9,10,'2024-10-16 17:40:00'),
(30,10,'2024-10-17 13:30:00'),
(14,10,'2024-10-17 14:45:00');

-- Event 11 — December Bhangra Workshop (Club 5)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(5,11,'2025-11-28 16:40:00'),
(7,11,'2025-11-29 17:50:00'),
(25,11,'2025-11-30 15:00:00'),
(21,11,'2025-12-01 13:40:00'),
(29,11,'2025-12-01 16:10:00'),
(30,11,'2025-12-02 17:55:00'),
(17,11,'2025-12-03 14:20:00');

-- Event 13 — Fall Networking Mixer 2024 (Club 6)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(5,13,'2024-10-05 14:00:00'),
(11,13,'2024-10-06 16:00:00'),
(21,13,'2024-10-07 17:10:00'),
(27,13,'2024-10-08 13:45:00'),
(3,13,'2024-10-08 15:30:00'),
(19,13,'2024-10-09 12:50:00'),
(1,13,'2024-10-09 13:10:00'),
(12,13,'2024-10-10 16:30:00'),
(25,13,'2024-10-10 14:40:00'),
(6,13,'2024-10-11 11:30:00');

-- Event 14 — Resume Review Night (Club 6)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(5,14,'2025-11-28 14:00:00'),
(11,14,'2025-11-29 16:20:00'),
(21,14,'2025-11-30 12:10:00'),
(27,14,'2025-12-01 15:20:00'),
(19,14,'2025-12-02 16:50:00'),
(1,14,'2025-12-03 17:40:00');

-- Event 16 — Cognitive Science Guest Talk (Club 7)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(7,16,'2025-02-01 14:10:00'),
(9,16,'2025-02-02 15:00:00'),
(13,16,'2025-02-03 16:50:00'),
(17,16,'2025-02-04 12:40:00'),
(19,16,'2025-02-05 14:30:00'),
(23,16,'2025-02-06 17:10:00');

-- Event 17 — December Exam Wellness Session (Club 7)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(7,17,'2025-11-27 17:20:00'),
(9,17,'2025-11-29 13:50:00'),
(17,17,'2025-12-01 14:40:00'),
(19,17,'2025-12-02 16:20:00'),
(25,17,'2025-12-03 12:30:00');

-- Event 19 — Frosh Week Engineering Bash (Club 10)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(2,19,'2024-08-20 11:10:00'),
(3,19,'2024-08-21 12:00:00'),
(10,19,'2024-08-22 13:20:00'),
(14,19,'2024-08-23 17:00:00'),
(20,19,'2024-08-24 14:10:00'),
(24,19,'2024-08-24 15:30:00'),
(29,19,'2024-08-25 16:40:00'),
(1,19,'2024-08-26 12:00:00'),
(13,19,'2024-08-26 16:20:00'),
(18,19,'2024-08-27 12:50:00'),
(26,19,'2024-08-28 13:50:00'),
(30,19,'2024-08-28 14:00:00');

-- Event 20 — December Design Build Challenge (Club 10)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(2,20,'2025-11-27 14:40:00'),
(3,20,'2025-11-29 16:10:00'),
(10,20,'2025-12-01 12:50:00'),
(20,20,'2025-12-02 16:30:00'),
(24,20,'2025-12-03 15:50:00'),
(29,20,'2025-12-04 17:20:00');

-- Event 22 — Fall Open Court (Club 13)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(6,22,'2024-09-20 11:10:00'),
(9,22,'2024-09-21 12:40:00'),
(10,22,'2024-09-22 10:30:00'),
(18,22,'2024-09-23 17:00:00'),
(20,22,'2024-09-24 14:10:00'),
(24,22,'2024-09-25 16:40:00'),
(26,22,'2024-09-26 15:20:00'),
(27,22,'2024-09-27 13:40:00');

-- Event 23 — December Indoor Match Day (Club 13)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(6,23,'2025-11-29 13:00:00'),
(9,23,'2025-11-30 11:50:00'),
(18,23,'2025-12-01 17:20:00'),
(26,23,'2025-12-02 15:00:00'),
(30,23,'2025-12-03 13:10:00');

-- Event 25 — MCAT Strategy Session 2024 (Club 16)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(6,25,'2024-10-20 13:00:00'),
(9,25,'2024-10-21 11:30:00'),
(13,25,'2024-10-22 14:50:00'),
(15,25,'2024-10-22 16:00:00'),
(18,25,'2024-10-23 12:20:00'),
(23,25,'2024-10-24 14:40:00'),
(28,25,'2024-10-25 11:30:00'),
(30,25,'2024-10-25 16:10:00'),
(1,25,'2024-10-26 13:00:00'),
(10,25,'2024-10-27 15:30:00'),
(14,25,'2024-10-27 16:40:00'),
(20,25,'2024-10-28 12:00:00');

-- Event 26 — December Winter Med-School Panel (Club 16)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(6,26,'2025-11-28 15:20:00'),
(9,26,'2025-11-29 16:40:00'),
(13,26,'2025-11-30 12:10:00'),
(15,26,'2025-12-01 18:00:00'),
(18,26,'2025-12-02 14:20:00'),
(23,26,'2025-12-03 13:40:00'),
(30,26,'2025-12-04 17:15:00');

-- Event 28 — Intro to Ethical Hacking 2025 (Club 20)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(4,28,'2025-02-20 14:10:00'),
(10,28,'2025-02-21 13:50:00'),
(14,28,'2025-02-22 15:40:00'),
(20,28,'2025-02-23 16:00:00'),
(22,28,'2025-02-24 14:30:00'),
(24,28,'2025-02-25 12:40:00'),
(30,28,'2025-02-26 17:10:00');

-- Event 29 — December Capture-the-Flag Scrimmage (Club 20)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(4,29,'2025-11-29 12:00:00'),
(10,29,'2025-11-30 15:10:00'),
(14,29,'2025-12-01 17:45:00'),
(20,29,'2025-12-01 14:50:00'),
(22,29,'2025-12-02 13:40:00'),
(24,29,'2025-12-02 16:20:00'),
(30,29,'2025-12-03 11:20:00'),
(3,29,'2025-12-03 14:00:00'),
(21,29,'2025-12-04 17:10:00'),
(1,29,'2025-12-04 15:25:00');

-- Event 31 — MSA Welcome Dinner 2025 (Club 2)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(1,31,'2025-09-05 13:20:00'),
(7,31,'2025-09-06 14:10:00'),
(12,31,'2025-09-07 15:40:00'),
(17,31,'2025-09-08 16:30:00'),
(25,31,'2025-09-09 12:50:00'),
(29,31,'2025-09-10 13:40:00');

-- Event 32 — December Prayer & Reflection Night (Club 2)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(1,32,'2025-11-29 12:50:00'),
(7,32,'2025-11-30 13:10:00'),
(12,32,'2025-12-01 14:40:00'),
(17,32,'2025-12-02 16:10:00'),
(25,32,'2025-12-03 15:20:00');

-- Event 33 — CAOS Fall Social 2024 (Club 8)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(8,33,'2024-10-20 13:20:00'),
(12,33,'2024-10-21 14:10:00'),
(17,33,'2024-10-22 15:40:00'),
(25,33,'2024-10-23 12:10:00'),
(29,33,'2024-10-23 16:00:00'),
(3,33,'2024-10-24 17:15:00'),
(7,33,'2024-10-25 11:30:00'),
(19,33,'2024-10-25 14:20:00'),
(21,33,'2024-10-26 15:50:00'),
(28,33,'2024-10-27 13:40:00');

-- Event 34 — December Holiday Games Night (Club 8)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(8,34,'2025-11-29 13:10:00'),
(12,34,'2025-11-30 14:20:00'),
(17,34,'2025-12-01 15:45:00'),
(29,34,'2025-12-02 17:30:00'),
(21,34,'2025-12-03 13:10:00');

-- Event 35 — WiCS Mentorship Kickoff 2025 (Club 9)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(3,35,'2025-04-20 15:00:00'),
(9,35,'2025-04-21 16:20:00'),
(14,35,'2025-04-22 12:40:00'),
(17,35,'2025-04-23 13:50:00'),
(21,35,'2025-04-24 14:10:00'),
(25,35,'2025-04-25 16:30:00'),
(29,35,'2025-04-26 18:00:00');

-- Event 36 — January Tech Career Prep (Club 9)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(3,36,'2025-12-15 14:00:00'),
(9,36,'2025-12-16 15:20:00'),
(17,36,'2025-12-17 16:40:00'),
(25,36,'2025-12-18 13:10:00'),
(29,36,'2025-12-19 14:30:00');

-- Event 37 — Fitness & Wellness Seminar (Club 11)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(11,37,'2025-02-25 12:30:00'),
(18,37,'2025-02-26 14:00:00'),
(23,37,'2025-02-27 15:20:00'),
(26,37,'2025-02-28 11:40:00'),
(9,37,'2025-03-01 16:10:00'),
(3,37,'2025-03-02 13:30:00');

-- Event 38 — January Community Sport Day (Club 11)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(11,38,'2025-12-15 12:50:00'),
(18,38,'2025-12-16 14:10:00'),
(23,38,'2025-12-17 16:30:00'),
(26,38,'2025-12-18 11:20:00'),
(3,38,'2025-12-19 13:40:00');

-- Event 39 — Women’s Leadership Panel 2025 (Club 18)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(7,39,'2025-03-20 15:00:00'),
(17,39,'2025-03-21 14:10:00'),
(21,39,'2025-03-22 12:40:00'),
(27,39,'2025-03-23 15:20:00'),
(11,39,'2025-03-24 16:50:00'),
(25,39,'2025-03-25 14:20:00');

-- Event 40 — December Goal-Setting Workshop (Club 18)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(7,40,'2025-11-30 12:20:00'),
(17,40,'2025-12-01 14:10:00'),
(21,40,'2025-12-02 16:00:00'),
(27,40,'2025-12-03 15:00:00'),
(25,40,'2025-12-04 17:40:00');

-- Event 41 — Filipino Heritage Night 2024 (Club 12)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(8,41,'2024-10-03 14:20:00'),
(12,41,'2024-10-04 16:10:00'),
(17,41,'2024-10-05 15:40:00'),
(25,41,'2024-10-06 11:20:00'),
(29,41,'2024-10-06 17:00:00'),
(3,41,'2024-10-07 14:50:00'),
(21,41,'2024-10-08 12:40:00');

-- Event 42 — Intro to EV Systems Workshop (Club 14)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(20,42,'2025-02-10 13:10:00'),
(24,42,'2025-02-11 14:00:00'),
(10,42,'2025-02-12 15:10:00');

-- Event 43 — Fall Community Run 2025 (Club 15)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(9,43,'2025-09-05 09:10:00'),
(18,43,'2025-09-06 10:00:00'),
(26,43,'2025-09-07 11:30:00'),
(6,43,'2025-09-08 12:20:00'),
(30,43,'2025-09-09 13:40:00'),
(20,43,'2025-09-10 14:10:00');

-- Event 44 — SEASA Cultural Mixer 2024 (Club 17)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(5,44,'2024-10-30 15:00:00'),
(7,44,'2024-10-31 16:20:00'),
(12,44,'2024-11-01 14:50:00'),
(21,44,'2024-11-02 13:10:00'),
(25,44,'2024-11-03 11:40:00');

-- Event 45 — Board Game Night 2025 (Club 19)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(1,45,'2025-01-20 14:00:00'),
(9,45,'2025-01-21 15:10:00'),
(17,45,'2025-01-22 16:40:00');

-- Event 46 — Water Drive Volunteer Day (Club 21)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(6,46,'2025-04-20 11:30:00'),
(19,46,'2025-04-21 12:40:00'),
(21,46,'2025-04-22 15:10:00'),
(28,46,'2025-04-23 14:00:00'),
(29,46,'2025-04-24 16:30:00');

-- Event 47 — Worship & Fellowship Night (Club 22)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(11,47,'2025-03-01 15:20:00'),
(19,47,'2025-03-02 13:40:00'),
(25,47,'2025-03-03 14:50:00');

-- Event 48 — MUN Debate Practice 2025 (Club 23)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(8,48,'2025-09-20 15:00:00'),
(17,48,'2025-09-21 16:30:00'),
(21,48,'2025-09-22 12:20:00'),
(3,48,'2025-09-23 14:00:00'),
(25,48,'2025-09-24 16:10:00');

-- Event 49 — Intro to Lab Skills Workshop (Club 24)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(6,49,'2025-09-01 13:40:00'),
(9,49,'2025-09-02 11:00:00'),
(13,49,'2025-09-03 14:10:00'),
(14,49,'2025-09-04 12:30:00'),
(23,49,'2025-09-05 15:20:00'),
(30,49,'2025-09-06 16:40:00');

-- Event 50 — Fall Dance Workshop 2024 (Club 25)
INSERT INTO Registration (user_id, event_id, registration_date) VALUES
(5,50,'2024-10-01 13:00:00'),
(7,50,'2024-10-02 14:20:00'),
(12,50,'2024-10-03 16:10:00'),
(17,50,'2024-10-04 12:00:00'),
(21,50,'2024-10-05 13:50:00'),
(29,50,'2024-10-06 15:10:00');



-- -------------------------
-- Payment
-- -------------------------

-- Event 10 — $5 fee (registrations 48–60)
INSERT INTO Payment (registration_id, payment_status, payment_method, payment_date, amount) VALUES
(48, 'completed', 'credit_card', '2024-10-10 15:00:00', 5.00),
(49, 'completed', 'credit_card', '2024-10-11 18:00:00', 5.00),
(50, 'completed', 'credit_card', '2024-10-12 19:00:00', 5.00),
(51, 'completed', 'credit_card', '2024-10-12 17:50:00', 5.00),
(52, 'completed', 'credit_card', '2024-10-13 12:20:00', 5.00),
(53, 'completed', 'credit_card', '2024-10-13 15:30:00', 5.00),
(54, 'completed', 'credit_card', '2024-10-14 16:40:00', 5.00),
(55, 'completed', 'credit_card', '2024-10-15 14:10:00', 5.00),
(56, 'completed', 'credit_card', '2024-10-15 13:00:00', 5.00),
(57, 'completed', 'credit_card', '2024-10-16 17:00:00', 5.00),
(58, 'completed', 'credit_card', '2024-10-16 18:40:00', 5.00),
(59, 'completed', 'credit_card', '2024-10-17 14:30:00', 5.00),
(60, 'completed', 'credit_card', '2024-10-17 15:45:00', 5.00);

-- Event 31 — $3 fee (registrations 162–167)
INSERT INTO Payment (registration_id, payment_status, payment_method, payment_date, amount) VALUES
(162, 'completed', 'credit_card', '2025-09-05 14:20:00', 3.00),
(163, 'completed', 'credit_card', '2025-09-06 15:10:00', 3.00),
(164, 'completed', 'credit_card', '2025-09-07 16:40:00', 3.00),
(165, 'completed', 'credit_card', '2025-09-08 17:30:00', 3.00),
(166, 'completed', 'credit_card', '2025-09-09 13:50:00', 3.00),
(167, 'completed', 'credit_card', '2025-09-10 14:40:00', 3.00);

-- Event 33 — $2 fee (registrations 173–182)
INSERT INTO Payment (registration_id, payment_status, payment_method, payment_date, amount) VALUES
(173, 'completed', 'credit_card', '2024-10-20 14:20:00', 2.00),
(174, 'completed', 'credit_card', '2024-10-21 15:10:00', 2.00),
(175, 'completed', 'credit_card', '2024-10-22 16:40:00', 2.00),
(176, 'completed', 'credit_card', '2024-10-23 13:10:00', 2.00),
(177, 'completed', 'credit_card', '2024-10-23 17:00:00', 2.00),
(178, 'completed', 'credit_card', '2024-10-24 18:15:00', 2.00),
(179, 'completed', 'credit_card', '2024-10-25 12:30:00', 2.00),
(180, 'completed', 'credit_card', '2024-10-25 15:20:00', 2.00),
(181, 'completed', 'credit_card', '2024-10-26 16:50:00', 2.00),
(182, 'completed', 'credit_card', '2024-10-27 14:40:00', 2.00);


-- ===============================
-- Notifications (24 total)
-- 8 reminders, 8 announcements, 8 updates
-- ===============================

-- --------- REMINDERS (8) ---------
INSERT INTO Notification (user_id, event_id, notification_message, notification_type, notification_status, notification_timestamp) VALUES
(1, 2, 'Reminder: Winter Hack Night starts soon.', 'reminder', 'unread', '2025-12-05 12:00:00'),
(7, 11, 'Reminder: Your Bhangra Workshop is tomorrow.', 'reminder', 'unread', '2025-12-01 09:00:00'),
(14, 8, 'Reminder: Study Hangout begins this week.', 'reminder', 'unread', '2025-11-30 10:30:00'),
(20, 29, 'Reminder: Capture-the-Flag Scrimmage is coming up.', 'reminder', 'unread', '2025-12-01 11:00:00'),
(12, 34, 'Reminder: Holiday Games Night is tomorrow.', 'reminder', 'unread', '2025-12-01 13:00:00'),
(3, 17, 'Reminder: Exam Wellness Session is today.', 'reminder', 'read', '2025-12-02 09:00:00'),
(25, 40, 'Reminder: Goal-Setting Workshop starts soon.', 'reminder', 'unread', '2025-12-03 10:15:00'),
(9, 23, 'Reminder: Indoor Match Day is tomorrow.', 'reminder', 'unread', '2025-12-01 12:10:00');

-- --------- ANNOUNCEMENTS (8) ---------
INSERT INTO Notification (user_id, event_id, notification_message, notification_type, notification_status, notification_timestamp) VALUES
(10, 1, 'Your registration for Fall Coding Jam is confirmed.', 'new', 'unread', '2024-10-01 10:00:00'),
(3, 4, 'You are registered for the Women in STEM Meet-and-Greet.', 'new', 'unread', '2025-03-01 10:30:00'),
(5, 10, 'PSA Culture Night registration successful.', 'new', 'read', '2024-10-11 09:30:00'),
(6, 7, 'Welcome Social registration received.', 'new', 'unread', '2024-09-01 10:15:00'),
(21, 13, 'You are confirmed for the Fall Networking Mixer.', 'new', 'unread', '2024-10-06 12:00:00'),
(11, 37, 'Your spot in the Fitness & Wellness Seminar is confirmed.', 'new', 'unread', '2025-02-25 10:45:00'),
(25, 33, 'You are registered for CAOS Fall Social.', 'new', 'unread', '2024-10-23 11:00:00'),
(1, 31, 'Your MSA Welcome Dinner registration is confirmed.', 'new', 'unread', '2025-09-06 10:00:00');

-- --------- UPDATES (8) ---------
INSERT INTO Notification (user_id, event_id, notification_message, notification_type, notification_status, notification_timestamp) VALUES
(22, 2, 'Update: Winter Hack Night room has changed.', 'update', 'unread', '2025-12-03 09:30:00'),
(17, 5, 'Update: Industry Mentorship Night check-in time adjusted.', 'update', 'unread', '2025-12-02 08:00:00'),
(14, 7, 'Update: Welcome Social now includes free snacks.', 'update', 'read', '2024-09-02 10:00:00'),
(20, 20, 'Update: Design Build Challenge materials list updated.', 'update', 'unread', '2025-12-02 09:50:00'),
(9, 37, 'Update: Fitness Seminar instructor updated.', 'update', 'unread', '2025-02-28 09:30:00'),
(3, 28, 'Update: Ethical Hacking workshop slides posted online.', 'update', 'unread', '2025-02-21 18:00:00'),
(18, 43, 'Update: Community Run meeting point updated.', 'update', 'unread', '2025-09-06 08:30:00'),
(30, 23, 'Update: Match Day schedule adjusted.', 'update', 'unread', '2025-12-02 10:30:00');


-- -------------------------
-- Comments
-- -------------------------

-- ========= PAST EVENTS (24) =========
INSERT INTO Comments (user_id, event_id, comment_message, comment_date) VALUES
(1, 1, 'The coding jam was really fun last year. I learned a lot working through the challenges.', '2024-10-15 14:20:00'),
(4, 1, 'I liked the mentors, they were super helpful during the puzzles.', '2024-10-16 12:40:00'),
(10, 10, 'PSA Culture Night was amazing! The performances were so good.', '2024-10-18 18:30:00'),
(7, 10, 'I enjoyed the event a lot. The atmosphere felt very welcoming.', '2024-10-18 19:00:00'),
(21, 13, 'Networking Mixer went well. I met a few alumni who gave great advice.', '2024-10-12 15:10:00'),
(19, 13, 'I think the mixer helped me understand co-op options better.', '2024-10-13 16:20:00'),
(6, 22, 'Open Court was fun. I got to practice rallies with different players.', '2024-09-28 18:10:00'),
(10, 22, 'I really liked the tennis session. The drills helped improve my footwork.', '2024-09-29 12:30:00'),
(12, 41, 'Filipino Heritage Night was vibrant and full of energy. Loved the food!', '2024-10-09 13:40:00'),
(3, 41, 'The cultural performances were great. I’m glad I went.', '2024-10-09 17:15:00'),
(25, 33, 'CAOS Social was super fun. The music was great and everyone was friendly.', '2024-10-28 14:40:00'),
(29, 33, 'I had a great time meeting new people at the social.', '2024-10-28 16:50:00'),
(14, 7, 'The welcome social was a great way to meet new science students.', '2024-09-07 16:10:00'),
(23, 7, 'I liked the games they had. It helped break the ice.', '2024-09-08 11:40:00'),
(9, 33, 'I really enjoyed the CAOS social. It was lively and fun.', '2024-10-24 18:10:00'),
(7, 33, 'The music at the event was amazing, I met so many new people.', '2024-10-26 12:20:00'),
(21, 22, 'Open Court helped me get back into tennis after a long time.', '2024-09-28 15:30:00'),
(24, 22, 'I had a great time practicing serves during the event.', '2024-09-29 14:50:00'),
(8, 41, 'Heritage Night was full of culture and energy. Loved the performances.', '2024-10-08 13:20:00'),
(17, 41, 'It was my first time attending and I really enjoyed the atmosphere.', '2024-10-08 17:15:00'),
(6, 13, 'The mixer helped me learn about business clubs I didn’t know existed.', '2024-10-09 15:00:00'),
(11, 13, 'I liked hearing from alumni. Their career stories were inspiring.', '2024-10-11 12:10:00'),
(25, 10, 'PSA Culture Night was super fun. I hope they host more events like this.', '2024-10-16 19:30:00'),
(29, 10, 'Great event overall. The food and performances were excellent.', '2024-10-17 11:45:00');


-- ========= UPCOMING EVENTS (24) — present tense only =========
INSERT INTO Comments (user_id, event_id, comment_message, comment_date) VALUES
(14, 2, 'I am looking forward to attending Winter Hack Night.', '2025-11-29 13:10:00'),
(1, 2, 'This event looks really exciting. I can’t wait to build with others.', '2025-11-30 14:10:00'),
(5, 11, 'I am planning to join the Bhangra Workshop. It sounds fun!', '2025-12-01 10:20:00'),
(29, 11, 'I am excited to learn some new dance steps at the workshop.', '2025-12-02 12:00:00'),
(14, 8, 'I am going to the study hangout to prepare for finals.', '2025-12-01 15:30:00'),
(23, 8, 'I like that this event gives a quiet place to study.', '2025-12-02 12:50:00'),
(3, 20, 'I am joining the Design Build Challenge this week.', '2025-12-03 13:00:00'),
(24, 20, 'I am excited to work with a team on this challenge.', '2025-12-03 16:00:00'),
(20, 29, 'I am participating in the CTF scrimmage. The challenges seem fun.', '2025-12-02 13:30:00'),
(30, 29, 'I am preparing for this event and hope to solve a few puzzles.', '2025-12-03 13:50:00'),
(3, 36, 'I am attending the career prep session to improve my interview skills.', '2025-12-15 14:20:00'),
(25, 36, 'I am looking forward to learning resume tips at this session.', '2025-12-16 13:30:00'),
(10, 2, 'I am excited to work on a small project during Hack Night.', '2025-12-01 12:20:00'),
(22, 2, 'I am preparing ideas for Hack Night. It should be productive.', '2025-12-02 10:40:00'),
(17, 11, 'I am joining the Bhangra Workshop again this year. It is always fun.', '2025-12-03 13:00:00'),
(30, 11, 'I am looking forward to learning more advanced moves this time.', '2025-12-03 17:40:00'),
(16, 8, 'I am attending the study hangout to finish my assignments.', '2025-11-30 16:30:00'),
(13, 8, 'I like that the study hangout gives me a quiet place to focus.', '2025-11-29 14:30:00'),
(2, 20, 'I am excited to try the design challenge. It sounds very hands-on.', '2025-11-28 12:40:00'),
(29, 20, 'I am joining a team for the challenge and getting ready for it.', '2025-12-01 16:50:00'),
(14, 29, 'I am excited to compete in my first CTF event.', '2025-12-01 18:00:00'),
(3, 29, 'I am preparing for the cryptography problems at the scrimmage.', '2025-12-03 12:20:00'),
(9, 36, 'I am planning to attend the career prep session to practice mock interviews.', '2025-12-16 15:00:00'),
(29, 36, 'I am excited to attend this workshop and update my resume afterwards.', '2025-12-17 12:10:00');
