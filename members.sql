CREATE TABLE IF NOT EXISTS members (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,          -- Member ID
    name VARCHAR(100) NOT NULL,                           -- Name
    phone VARCHAR(20) NOT NULL,                           -- Phone number
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP  -- Date the member was added
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
