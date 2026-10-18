-- Expense module
-- phpMyAdmin e finance_api database select kore ei SQL ta run koro.

-- Protita kharoch (expense) ekta row.
-- Total expense = sob row-er amount-er SUM, tai add/edit/delete korle total automatic update hoy.
-- expense_at: kharoch add korar shomoy server automatic date ar time boshay.
CREATE TABLE IF NOT EXISTS expenses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,                           -- kisher kharoch
    amount DECIMAL(12,2) NOT NULL,                         -- kharoch-er poriman (Taka)
    note VARCHAR(255) DEFAULT NULL,                        -- optional note
    expense_at DATETIME NOT NULL,                          -- auto date and time
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL                       -- soft delete
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
