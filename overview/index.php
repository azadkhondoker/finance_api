<?php
include '../connection.php';
include '../auth_check.php';

// Total savings: member der sob joma-r SUM (deleted bad)
$savings = $cc->common_query("SELECT COALESCE(SUM(s.amount), 0) AS total
                              FROM savings s
                              INNER JOIN members m ON m.id = s.member_id AND m.deleted_at IS NULL
                              WHERE s.deleted_at IS NULL");

// Total expense: sob kharoch-er SUM (deleted bad)
$expense = $cc->common_query("SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE deleted_at IS NULL");

$total_savings = (float) $savings['data'][0]->total;
$total_expense = (float) $expense['data'][0]->total;

echo json_encode(array(
    "status" => true,
    "message" => "Records found",
    "total_savings" => $total_savings,
    "total_expense" => $total_expense,
    // Expense bad diye (adjust kore) baki savings
    "balance" => $total_savings - $total_expense
));
