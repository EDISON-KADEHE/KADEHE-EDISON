CREATE DATABASE IF NOT EXISTS student_management_system
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE student_management_system;

CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admission_no VARCHAR(40) NOT NULL UNIQUE,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    phone VARCHAR(40) NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    date_of_birth DATE NULL,
    course VARCHAR(120) NOT NULL,
    address TEXT NULL,
    profile_image VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO admins (name, email, password)
SELECT 'System Admin', 'admin@example.com', '$2y$10$AjkrtK2/KEDecxXVu5nPHevE99HAI3OnGIEcHnVAZVUlU8RAwv2O2'
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM admins WHERE email = 'admin@example.com'
);
