<?php
date_default_timezone_set('Asia/Dhaka');
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

// Kon loan-e koyta kisti dewar shomoy ses hoye geche (Out of Time)
$today = date('Y-m-d');
$overdue = [];
$o = $cc->common_query("SELECT loan_id, COUNT(*) AS c FROM loan_installments
    WHERE deleted_at IS NULL AND status = 'unpaid' AND due_date < '$today' GROUP BY loan_id");
foreach ($o['data'] as $r) {
    $overdue[$r->loan_id] = (int) $r->c;
}

$outstanding = 0;
$interest = 0;
foreach ($result['data'] as $row) {
    $row->overdue_count = isset($overdue[$row->id]) ? $overdue[$row->id] : 0;
    $row->outstanding = (float) $row->principal - (float) $row->paid_principal;
    $row->status = $row->outstanding <= 0 ? 'closed' : 'active';
    $outstanding += $row->outstanding;
    $interest += (float) $row->paid_interest;
}
$result['total_outstanding'] = $outstanding;
$result['total_interest'] = $interest;

echo json_encode($result);
