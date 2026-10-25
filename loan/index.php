<?php
include '../connection.php';
include '../auth_check.php';

// Sob loan + protitar koto ferot hoyeche
$result = $cc->common_query(
    "SELECT l.*,
            COALESCE(SUM(p.principal_amount), 0) AS paid_principal,
            COALESCE(SUM(p.interest_amount), 0) AS paid_interest
     FROM loans l
     LEFT JOIN loan_payments p ON p.loan_id = l.id AND p.deleted_at IS NULL
     WHERE l.deleted_at IS NULL
     GROUP BY l.id
     ORDER BY l.loan_date DESC, l.id DESC"
);

$outstanding = 0;
$interest = 0;
foreach ($result['data'] as $row) {
    $row->outstanding = (float) $row->principal - (float) $row->paid_principal;
    $row->status = $row->outstanding <= 0 ? 'closed' : 'active';
    $outstanding += $row->outstanding;
    $interest += (float) $row->paid_interest;
}
$result['total_outstanding'] = $outstanding;
$result['total_interest'] = $interest;

echo json_encode($result);
