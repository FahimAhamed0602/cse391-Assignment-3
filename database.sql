-- Drop existing database for a clean setup
DROP DATABASE IF EXISTS car_workshop;
CREATE DATABASE car_workshop;
USE car_workshop;

-- Create mechanics table
CREATE TABLE mechanics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    max_slots INT DEFAULT 100 -- High limit to avoid booking errors
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
('John Smith', 100),
('Mike Johnson', 100),
('Sarah Williams', 100),
('David Brown', 100),
('Emma Davis', 100);