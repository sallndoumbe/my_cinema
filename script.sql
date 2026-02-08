CREATE DATABASE IF NOT EXISTS my_cinema CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE my_cinema;

CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration INT NOT NULL,
    release_date DATE,
    genre VARCHAR(100)
);

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    capacity INT NOT NULL,
    type VARCHAR(50) DEFAULT 'Standard',
    is_deleted TINYINT(1) DEFAULT 0
);

CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    room_id INT NOT NULL,
    start_at DATETIME NOT NULL,
    end_at DATETIME NOT NULL,
    FOREIGN KEY (movie_id) REFERENCES movies(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);