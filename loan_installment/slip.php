<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';
include '../funds.php';
include '../loan_schedule.php';

// Paid kisti-r slip-er sob information: kisti, loan, borrower, koto dilo, ar ekhon koto baki
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$inst = $cc->common_select('loan_installments', '*', ['id' => $id]);
if (!$inst['status']) {
    json_fail('Installment not found.');
}
$inst = $inst['data'][0];
if ($inst->status !== 'paid') {
    json_fail('A slip is available only after the installment is paid.');
}

$loan = $cc->common_select('loans', '*', ['id' => $inst->loan_id]);
if (!$loan['status']) {
    json_fail('Loan not found.');
}
$loan = $loan['data'][0];

$loan_id = (int) $loan->id;
$payment_id = (int) $inst->payment_id;

// Ei kisti dewar por loan-er baki mul taka
$repaid_till = sum_query($cc, "SELECT COALESCE(SUM(principal_amount), 0) AS total FROM loan_payments
    WHERE loan_id = $loan_id AND deleted_at IS NULL AND id <= $payment_id");

$total = (int) sum_query($cc, "SELECT COUNT(*) AS total FROM loan_installments WHERE loan_id = $loan_id AND deleted_at IS NULL");
$paid_count = (int) sum_query($cc, "SELECT COUNT(*) AS total FROM loan_installments WHERE loan_id = $loan_id AND deleted_at IS NULL AND status = 'paid'");

// Porer unpaid kisti-r tarikh
$next = $cc->common_query("SELECT MIN(due_date) AS next_due FROM loan_installments
    WHERE loan_id = $loan_id AND deleted_at IS NULL AND status = 'unpaid'");
$next_due = $next['status'] ? $next['data'][0]->next_due : null;

$principal = (float) $inst->principal_part;
$interest = (float) $inst->interest_part;
$penalty = (float) $inst->penalty_paid;

echo json_encode(array(
    "status" => true,
    "message" => "Records found",
    "slip_no" => 'INS-' . str_pad($inst->id, 6, '0', STR_PAD_LEFT),
    "installment" => $inst,
    "loan" => $loan,
    "principal_part" => $principal,
    "interest_part" => $interest,
    "penalty_paid" => $penalty,
    "total_paid" => round($principal + $interest + $penalty, 2),
    "days_late" => days_late($inst->due_date, $inst->paid_date),
    "outstanding_after" => round((float) $loan->principal - $repaid_till, 2),
    "installment_total" => $total,
    "installments_paid" => $paid_count,
    "installments_left" => $total - $paid_count,
    "next_due_date" => $next_due
));
