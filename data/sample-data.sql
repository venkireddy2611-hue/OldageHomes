-- Sample data for testing

-- Users
INSERT INTO users (name, email, password, created_at) VALUES
('Admin User', 'admin@eldercare.local', 'password-placeholder', datetime('now'));

-- Contacts
INSERT INTO contacts (timestamp, name, email, phone, subject, message) VALUES
(datetime('now'), 'Test User', 'test@example.com', '+911234567890', 'General Inquiry', 'This is a test message.');

-- Admissions
INSERT INTO admissions (timestamp, contactName, contactPhone, contactEmail, residentName, timeline, roomType, additionalInfo) VALUES
(datetime('now'), 'Jane Doe', '+919876543210', 'jane@example.com', 'John Doe', 'soon', 'private', 'Needs assistance with mobility.');

-- Notes: Replace the password placeholder with a hashed password when creating real accounts.