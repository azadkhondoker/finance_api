<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';
include '../funds.php';

// Loan ferot: kotota mul taka (principal) ar kotota interest ashlo
$loan_id = isset($_POST['loan_id']) ? $_POST['loan_id'] : '';
$principal = isset($_POST['principal_amount']) && $_POST['principal_amount'] !== '' ? $_POST['principal_amount'] : 0;
$interest = isset($_POST['interest_amount']) && $_POST['interest_amount'] !== '' ? $_POST['interest_amount'] : 0;
$date = !empty($_POST['payment_date']) ? $_POST['payment_date'] : date('Y-m-d');

if (!ctype_digit((string) $loan_id) || !is_numeric($principal) || !is_numeric($interest)
    || (float) $principal < 0 || (float) $interest < 0 || ((float) $principal + (float) $interest) <= 0) {
    json_fail('Enter a valid principal and/or interest amount.');
}
if (!valid_date($date)) {
    json_fail('Invalid date.');
}

$loan = $cc->common_select('loans', '*', ['id' => $loan_id]);
if (!$loan['status'] || count($loan['data']) == 0) {
    json_fail('Loan not found.');
}

$paid = sum_query($cc, "SELECT COALESCE(SUM(principal_amount), 0) AS total FROM loan_payments WHERE loan_id = " . (int) $loan_id . " AND deleted_at IS NULL");
$outstanding = (float) $loan['data'][0]->principal - $paid;
if ((float) $principal > $outstanding) {
    json_fail('Principal repayment cannot be more than the outstanding amount (' . number_format($outstanding, 2) . ').');
}

echo json_encode($cc->common_insert('loan_payments', [
    'loan_id' => $loan_id,
    'principal_amount' => (float) $principal,
    'interest_amount' => (float) $interest,
    'payment_date' => $date,
    'note' => isset($_POST['note']) ? $_POST['note'] : '',
    'created_at' => date('Y-m-d H:i:s')
]));
