-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS voting_system;
USE voting_system;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fullname VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    dob DATE NOT NULL,
    voter_id VARCHAR(20) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DESCRIBE users;

-- Create candidates table
CREATE TABLE IF NOT EXISTS candidates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create votes table
CREATE TABLE IF NOT EXISTS votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    candidate_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_vote (user_id)
);
