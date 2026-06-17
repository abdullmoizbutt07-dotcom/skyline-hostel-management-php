-- ============================================================
-- Skyline Hostel Management System - Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS skyline_hostel;
USE skyline_hostel;

-- ============================================================
-- Table: admins
-- ============================================================
CREATE TABLE IF NOT EXISTS admins (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) UNIQUE NOT NULL,
    password    VARCHAR(255) NOT NULL,
    phone       VARCHAR(20),
    profile_pic VARCHAR(255) DEFAULT 'default-admin.png',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Table: students
-- ============================================================
CREATE TABLE IF NOT EXISTS students (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    reg_no          VARCHAR(50) UNIQUE NOT NULL,
    name            VARCHAR(100) NOT NULL,
    email           VARCHAR(150) UNIQUE NOT NULL,
    password        VARCHAR(255) NOT NULL,
    phone           VARCHAR(20),
    gender          ENUM('Male','Female','Other') NOT NULL,
    dob             DATE,
    cnic            VARCHAR(20),
    address         TEXT,
    guardian_name   VARCHAR(100),
    guardian_phone  VARCHAR(20),
    course          VARCHAR(100),
    year            VARCHAR(20),
    profile_pic     VARCHAR(255) DEFAULT 'default-student.png',
    status          ENUM('active','inactive','suspended') DEFAULT 'active',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Table: rooms
-- ============================================================
CREATE TABLE IF NOT EXISTS rooms (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    room_number     VARCHAR(20) UNIQUE NOT NULL,
    room_type       ENUM('Single','Double','Triple','Quad') NOT NULL,
    floor           VARCHAR(20),
    capacity        INT NOT NULL DEFAULT 1,
    occupied        INT NOT NULL DEFAULT 0,
    monthly_fee     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    amenities       TEXT,
    status          ENUM('available','full','maintenance') DEFAULT 'available',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Table: room_allocations
-- ============================================================
CREATE TABLE IF NOT EXISTS room_allocations (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT NOT NULL,
    room_id         INT NOT NULL,
    allocated_by    INT NOT NULL,
    allocation_date DATE NOT NULL,
    vacate_date     DATE DEFAULT NULL,
    status          ENUM('active','vacated','pending') DEFAULT 'pending',
    notes           TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id)    REFERENCES rooms(id)    ON DELETE CASCADE,
    FOREIGN KEY (allocated_by) REFERENCES admins(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Table: complaints
-- ============================================================
CREATE TABLE IF NOT EXISTS complaints (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT NOT NULL,
    title           VARCHAR(200) NOT NULL,
    description     TEXT NOT NULL,
    category        ENUM('Maintenance','Noise','Food','Security','Other') DEFAULT 'Other',
    priority        ENUM('Low','Medium','High') DEFAULT 'Medium',
    status          ENUM('pending','in_progress','resolved','closed') DEFAULT 'pending',
    admin_response  TEXT,
    resolved_at     TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Table: notices
-- ============================================================
CREATE TABLE IF NOT EXISTS notices (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    admin_id        INT NOT NULL,
    title           VARCHAR(255) NOT NULL,
    content         TEXT NOT NULL,
    category        ENUM('General','Academic','Maintenance','Event','Urgent') DEFAULT 'General',
    target_audience ENUM('all','male','female') DEFAULT 'all',
    is_pinned       TINYINT(1) DEFAULT 0,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Table: fees
-- ============================================================
CREATE TABLE IF NOT EXISTS fees (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT NOT NULL,
    room_id         INT,
    amount          DECIMAL(10,2) NOT NULL,
    fee_month       VARCHAR(20) NOT NULL,
    fee_year        YEAR NOT NULL,
    due_date        DATE NOT NULL,
    paid_date       DATE DEFAULT NULL,
    payment_method  ENUM('Cash','Bank Transfer','Online','Cheque') DEFAULT 'Cash',
    status          ENUM('pending','paid','overdue','waived') DEFAULT 'pending',
    receipt_no      VARCHAR(50),
    remarks         TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id)    REFERENCES rooms(id)    ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- Table: room_applications
-- ============================================================
CREATE TABLE IF NOT EXISTS room_applications (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT NOT NULL,
    preferred_type  ENUM('Single','Double','Triple','Quad'),
    preferred_floor VARCHAR(20),
    message         TEXT,
    status          ENUM('pending','approved','rejected') DEFAULT 'pending',
    admin_note      TEXT,
    applied_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Default Admin Account
-- Password: Admin@123 (bcrypt hashed)
-- ============================================================
INSERT INTO admins (name, email, password, phone) VALUES
('Super Admin', 'admin@skylinehostel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '03001234567');

-- ============================================================
-- Sample Rooms
-- ============================================================
INSERT INTO rooms (room_number, room_type, floor, capacity, monthly_fee, amenities, status) VALUES
('101', 'Single', 'Ground Floor', 1, 8000.00, 'AC, WiFi, Attached Bath', 'available'),
('102', 'Single', 'Ground Floor', 1, 8000.00, 'AC, WiFi, Attached Bath', 'available'),
('201', 'Double', 'First Floor',  2, 6000.00, 'Fan, WiFi, Shared Bath', 'available'),
('202', 'Double', 'First Floor',  2, 6000.00, 'Fan, WiFi, Shared Bath', 'available'),
('203', 'Triple', 'First Floor',  3, 4500.00, 'Fan, WiFi, Shared Bath', 'available'),
('301', 'Quad',   'Second Floor', 4, 3500.00, 'Fan, Shared Bath', 'available'),
('302', 'Quad',   'Second Floor', 4, 3500.00, 'Fan, Shared Bath', 'available'),
('103', 'Single', 'Ground Floor', 1, 9000.00, 'AC, WiFi, Attached Bath, Balcony', 'available'),
('204', 'Double', 'First Floor',  2, 6500.00, 'AC, WiFi, Shared Bath', 'available'),
('303', 'Triple', 'Second Floor', 3, 5000.00, 'AC, WiFi, Shared Bath', 'available');

-- ============================================================
-- Sample Notices
-- ============================================================
INSERT INTO notices (admin_id, title, content, category, is_pinned) VALUES
(1, 'Welcome to Skyline Hostel', 'We warmly welcome all new students to Skyline Hostel. Please read all rules and regulations carefully. For any queries, visit the reception.', 'General', 1),
(1, 'Monthly Fee Reminder', 'All students are reminded to pay their monthly hostel fee by the 10th of each month. Late payments will incur a penalty of Rs. 200.', 'General', 0),
(1, 'WiFi Maintenance', 'WiFi services will be temporarily unavailable on Sunday from 10 AM to 12 PM due to scheduled maintenance. Inconvenience is regretted.', 'Maintenance', 0);