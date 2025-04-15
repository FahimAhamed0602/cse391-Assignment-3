-- Drop existing database for a clean setup
DROP DATABASE IF EXISTS car_workshop;
CREATE DATABASE car_workshop;
USE car_workshop;

-- Create mechanics table
CREATE TABLE mechanics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    max_slots INT DEFAULT 50 -- Increased to minimize booking errors
);

-- Create appointments table
CREATE TABLE appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    car_license VARCHAR(20) NOT NULL,
    car_engine VARCHAR(20) NOT NULL,
    appointment_date DATE NOT NULL,
    mechanic_id INT,
    FOREIGN KEY (mechanic_id) REFERENCES mechanics(id)
);

-- Insert initial mechanics
INSERT INTO mechanics (name, max_slots) VALUES
('John Smith', 50),
('Mike Johnson', 50),
('Sarah Williams', 50),
('David Brown', 50),
('Emma Davis', 50);