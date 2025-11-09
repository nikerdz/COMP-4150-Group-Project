CREATE TABLE IF NOT EXISTS User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    user_email VARCHAR(100) NOT NULL UNIQUE,
    user_password VARCHAR(255) NOT NULL,
    user_status ENUM('active', 'suspended') DEFAULT 'active',
    user_type ENUM('student', 'admin') DEFAULT 'student',
    join_date DATE DEFAULT (CURRENT_DATE),
    gender VARCHAR(20),
    faculty VARCHAR(100),
    level_of_study ENUM('undergraduate', 'graduate') DEFAULT 'undergraduate',
    year_of_study INT
);

CREATE TABLE IF NOT EXISTS Category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Club (
    club_id INT AUTO_INCREMENT PRIMARY KEY,
    club_name VARCHAR(100) NOT NULL,
    club_email VARCHAR(100) UNIQUE,
    club_description TEXT,
    creation_date DATE DEFAULT (CURRENT_DATE),
    club_condition ENUM('none', 'women_only', 'undergrad_only') DEFAULT 'none',
    club_status ENUM('active', 'inactive') DEFAULT 'active'
);

CREATE TABLE IF NOT EXISTS Event (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    club_id INT NOT NULL,
    event_name VARCHAR(100) NOT NULL,
    event_description TEXT,
    event_location VARCHAR(255),
    event_date DATETIME,
    capacity INT,
    event_status ENUM('pending', 'approved', 'cancelled') DEFAULT 'pending',
    event_condition ENUM('none', 'women_only', 'undergrad_only', 'first_year_only') DEFAULT 'none',
    event_fee DECIMAL(8,2) DEFAULT 0.00,
    FOREIGN KEY (club_id) REFERENCES Club(club_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Notification (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    notification_message TEXT NOT NULL,
    notification_type ENUM('reminder', 'announcement', 'update') DEFAULT 'announcement',
    notification_status ENUM('unread', 'read') DEFAULT 'unread',
    notification_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES Event(event_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Registration (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    rsvp BOOLEAN DEFAULT TRUE,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES Event(event_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL UNIQUE,
    payment_status ENUM('pending', 'completed', 'refunded') DEFAULT 'pending',
    payment_method ENUM('credit_card', 'debit', 'paypal', 'cash') DEFAULT 'cash',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    amount DECIMAL(8,2),
    FOREIGN KEY (registration_id) REFERENCES Registration(registration_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Membership (
    user_id INT NOT NULL,
    club_id INT NOT NULL,
    membership_date DATE DEFAULT (CURRENT_DATE),
    PRIMARY KEY (user_id, club_id),
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (club_id) REFERENCES Club(club_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Executive (
    user_id INT NOT NULL,
    club_id INT NOT NULL,
    executive_role VARCHAR(50) DEFAULT 'member',
    PRIMARY KEY (user_id, club_id),
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (club_id) REFERENCES Club(club_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    comment_message TEXT NOT NULL,
    comment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES Event(event_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS User_Interests (
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (user_id, category_id),
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Category(category_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Club_Tags (
    club_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (category_id, club_id),
    FOREIGN KEY (category_id) REFERENCES Category(category_id) ON DELETE CASCADE,
    FOREIGN KEY (club_id) REFERENCES Club(club_id) ON DELETE CASCADE
);
