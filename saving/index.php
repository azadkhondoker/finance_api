<?php
include '../connection.php';
include '../auth_check.php';

// Member wise savings summary: protita member-er total savings (sob joma-r SUM)
$result = $cc->common_query(
    "SELECT m.id, m.name, m.phone,
            COALESCE(SUM(s.amount), 0) AS total_savings,
            COUNT(s.id) AS deposit_count,
            MAX(s.saving_date) AS last_saving_date
     FROM members m
     LEFT JOIN savings s ON s.member_id = m.id AND s.deleted_at IS NULL
     WHERE m.deleted_at IS NULL
     GROUP BY m.id, m.name, m.phone
     ORDER BY m.name ASC"
);

// Sob member-er savings mila ekta total savings
$total = 0;
foreach ($result['data'] as $row) {
    $total += (float) $row->total_savings;
}
$result['total_savings'] = $total;

echo json_encode($result);
