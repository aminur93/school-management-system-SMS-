-- Create replication user
CREATE USER IF NOT EXISTS 'replication_user'@'%' IDENTIFIED BY 'replication_pass123';
GRANT REPLICATION SLAVE ON *.* TO 'replication_user'@'%';

-- Create Laravel user
CREATE USER IF NOT EXISTS 'laravel_user'@'%' IDENTIFIED BY 'laravel_password123';
GRANT ALL PRIVILEGES ON laravel_db.* TO 'laravel_user'@'%';

FLUSH PRIVILEGES;

-- Create laravel_db migrations table if needed
USE laravel_db;

-- CREATE TABLE IF NOT EXISTS migrations (
--     id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--     migration VARCHAR(255) NOT NULL,
--     batch INT NOT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
