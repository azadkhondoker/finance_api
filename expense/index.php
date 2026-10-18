<?php
include '../connection.php';
include '../auth_check.php';

// Sob expense, notun age
$result = $cc->common_select('expenses', '*', [], 'AND', 'expense_at DESC, id', 'DESC');

// Sob expense mila ekta total expense
$total = 0;
foreach ($result['data'] as $row) {
    $total += (float) $row->amount;
}
$result['total_expense'] = $total;

echo json_encode($result);
