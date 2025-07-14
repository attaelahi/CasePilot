-- Insert sample case types
INSERT INTO case_types (name) VALUES 
('Criminal'),
('Civil'),
('Family'),
('Corporate'),
('Immigration'),
('Personal Injury');

-- Insert sample admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@casepilot.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
