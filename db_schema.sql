-- Utwórz bazę danych (np. foodapp) i tabele użytkowników oraz restauracji
CREATE DATABASE IF NOT EXISTS foodapp;
USE foodapp;

-- Tabela użytkowników
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    google_id VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 0,
    activation_token VARCHAR(64) DEFAULT NULL
);

-- Dane logowania restauratora
CREATE TABLE IF NOT EXISTS restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    google_id VARCHAR(255) DEFAULT NULL
);

-- Lokalizacje restauracji (może być wiele na jednego restauratora)
CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    restaurant_name VARCHAR(150) NOT NULL,
    city VARCHAR(100) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    opening_hours VARCHAR(255) DEFAULT NULL,
    contact_email VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(32) DEFAULT NULL,
    lat DOUBLE DEFAULT NULL,
    lng DOUBLE DEFAULT NULL,
    order_url VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);
