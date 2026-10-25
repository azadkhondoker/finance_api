<?php
include '../connection.php';
include '../auth_check.php';

// Ekta loan + tar sob ferot (payment) + hishab
$id = isset($_GET['id']) ? $_GET['id'] : '';

$loan = $cc->common_select('loans', '*', ['id' => $id]);
if (!$loan['status'] || count($loan['data']) == 0) {
    echo json_encode(array("message" => "Loan not found.", "status" => false, "data" => []));
    exit;
}
$row = $loan['data'][0];

$payments = $cc->common_select('loan_payments', '*', ['loan_id' => $row->id], 'AND', 'payment_date DESC, id', 'DESC');

$paid_principal = 0;
$paid_interest = 0;
foreach ($payments['data'] as $p) {
    $paid_principal += (float) $p->principal_amount;
    $paid_interest += (float) $p->interest_amount;
}

// Shomoyshima thakle expected interest = principal x rate% x din / 365 (simple interest)
$expected_interest = null;
if (!empty($row->due_date)) {
    $days = (strtotime($row->due_date) - strtotime($row->loan_date)) / 86400;
    if ($days > 0) {
        $expected_interest = round((float) $row->principal * (float) $row->interest_rate / 100 * $days / 365, 2);
    }
}

echo json_encode(array(
    "status" => true,
    "message" => "Records found",
    "loan" => $row,
    "payments" => $payments['data'],
    "paid_principal" => $paid_principal,
    "paid_interest" => $paid_interest,
    "outstanding" => (float) $row->principal - $paid_principal,
    "expected_interest" => $expected_interest
));
