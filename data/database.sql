-- Database structure for Harmony Haven (MySQL-compatible)
-- Run this on your DB server to create tables

CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(200) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contacts (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  name VARCHAR(200),
  email VARCHAR(255),
  phone VARCHAR(50),
  subject VARCHAR(200),
  message TEXT
);

CREATE TABLE IF NOT EXISTS admissions (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  contactName VARCHAR(200),
  contactPhone VARCHAR(50),
  contactEmail VARCHAR(255),
  residentName VARCHAR(200),
  timeline VARCHAR(100),
  roomType VARCHAR(100),
  additionalInfo TEXT
);

CREATE TABLE IF NOT EXISTS password_resets (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(255),
  token VARCHAR(128),
  expires_at DATETIME,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Notes: For SQLite you may need to adjust AUTO_INCREMENT to 'INTEGER PRIMARY KEY AUTOINCREMENT' and CURRENT_TIMESTAMP usage.