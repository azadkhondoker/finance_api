-- Savings module
-- phpMyAdmin e finance_api database select kore ei SQL ta run koro.

-- 1) members table-e deleted_at / updated_at nai.
--    crud_class::common_select shob query-te "deleted_at IS NULL" jore dey,
--    tai column na thakle member list o savings page kaj korbe na.
ALTER TABLE members ADD COLUMN IF NOT EXISTS updated_at DATETIME NULL DEFAULT NULL;
ALTER TABLE members ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL DEFAULT NULL;

-- 2) savings table: protita joma (deposit) ekta row.
--    Member-er total savings = oi member-er sob row-er amount-er SUM,
--    tai notun joma dile ba amount edit/delete korle total automatic update hoy.
CREATE TABLE IF NOT EXISTS savings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id INT UNSIGNED NOT NULL,                       -- members.id
    amount DECIMAL(12,2) NOT NULL,                         -- joma-r poriman (Taka)
    saving_date DATE NOT NULL,                             -- joma-r tarikh
    note VARCHAR(255) DEFAULT NULL,                        -- optional note
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,                      -- soft delete
    KEY idx_savings_member (member_id),
    CONSTRAINT fk_savings_member FOREIGN KEY (member_id) REFERENCES members(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
