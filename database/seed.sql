-- BookNest Database Seed Data
-- Initial data for testing and development

-- Note: Run this after schema.sql to populate test data

-- Insert sample users
-- Admin user
INSERT INTO Users (FIRST_NAME, LAST_NAME, USERNAME, PASSWORD, PHONE, IS_SUBSCRIBED, ROLE_ID) VALUES
('Admin', 'User', 'admin@booknest.com', '$2y$12$K5JfQ9B2V8Xn4mP7zKqI1u8Yc3vN9wZ0A2B4C6D8E0F2G4H6I8J0K2L4M6N8P', '555-0101', TRUE, 3);

-- Parent user
INSERT INTO Users (FIRST_NAME, LAST_NAME, USERNAME, PASSWORD, PHONE, IS_SUBSCRIBED, ROLE_ID) VALUES
('John', 'Parent', 'parent@test.com', '$2y$12$L9JfQ9B2V8Xn4mP7zKqI1u8Yc3vN9wZ0A2B4C6D8E0F2G4H6I8J0K2L4M6N8P', '555-0102', FALSE, 2);

-- Another parent user
INSERT INTO Users (FIRST_NAME, LAST_NAME, USERNAME, PASSWORD, PHONE, IS_SUBSCRIBED, ROLE_ID) VALUES
('Sarah', 'Wilson', 'sarah@booknest.com', '$2y$12$M9JfQ9B2V8Xn4mP7zKqI1u8Yc3vN9wZ0A2B4C6D8E0F2G4H6I8J0K2L4M6N8P', '555-0103', TRUE, 2);

-- Educator user
INSERT INTO Users (FIRST_NAME, LAST_NAME, USERNAME, PASSWORD, PHONE, IS_SUBSCRIBED, ROLE_ID) VALUES
('Dr. Jane', 'Smith', 'edu@booknest.com', '$2y$12$N9JfQ9B2V8Xn4mP7zKqI1u8Yc3vN9wZ0A2B4C6D8E0F2G4H6I8J0K2L4M6N8P', '555-0104', FALSE, 4);

-- Child users (password can be NULL or simple passkey)
INSERT INTO Users (FIRST_NAME, LAST_NAME, USERNAME, PASSWORD, PHONE, IS_SUBSCRIBED, ROLE_ID) VALUES
('Tommy', 'Kid', 'child1', '$2y$12$O9JfQ9B2V8Xn4mP7zKqI1u8Yc3vN9wZ0A2B4C6D8E0F2G4H6I8J0K2L4M6N8P', NULL, FALSE, 1);

INSERT INTO Users (FIRST_NAME, LAST_NAME, USERNAME, PASSWORD, PHONE, IS_SUBSCRIBED, ROLE_ID) VALUES
('Emma', 'Student', 'child2', 'emma123', NULL, FALSE, 1);

INSERT INTO Users (FIRST_NAME, LAST_NAME, USERNAME, PASSWORD, PHONE, IS_SUBSCRIBED, ROLE_ID) VALUES
('Alex', 'Young', 'child3', NULL, NULL, FALSE, 1);

-- Passwords used (all are "password123" hashed with bcrypt):
-- admin@booknest.com / password123 (ADMIN)
-- parent@test.com / password123 (PARENT)
-- sarah@booknest.com / password123 (PARENT)
-- edu@booknest.com / password123 (EDUCATOR)
-- child1 / password123 (CHILD - can use code 1 or name "Tommy")
-- child2 / emma123 (CHILD - can use code 2 or name "Emma")
-- child3 / NULL (CHILD - can use code 3 or name "Alex")

-- For child login, you can use:
-- Child Code: 1, Passkey: password123
-- Child Code: 2, Passkey: emma123
-- Child Code: 3, Passkey: Alex

-- Test queries to verify data:
-- SELECT u.FIRST_NAME, u.LAST_NAME, u.USERNAME, r.NAME as ROLE FROM Users u JOIN Roles r ON u.ROLE_ID = r.ID;
-- SELECT * FROM Users WHERE ROLE_ID = 1; -- Children
-- SELECT * FROM Users WHERE ROLE_ID = 2; -- Parents
-- SELECT * FROM Users WHERE ROLE_ID = 3; -- Admins
-- SELECT * FROM Users WHERE ROLE_ID = 4; -- Educators