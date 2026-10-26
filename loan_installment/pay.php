<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';
include '../funds.php';
include '../loan_schedule.php';

// Ekta kisti paid kora. Deri hole jorimana ager hishab-e jug hoy.
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$inst = $cc->common_select('loan_installments', '*', ['id' => $id]);
if (!$inst['status']) {
    json_fail('Installment not found.');
}
$inst = $inst['data'][0];
if ($inst->status === 'paid') {
    json_fail('This installment is already paid.');
}

$loan = $cc->common_select('loans', '*', ['id' => $inst->loan_id]);
if (!$loan['status']) {
    json_fail('Loan not found.');
}
$loan = $loan['data'][0];

$today = date('Y-m-d');
$penalty = installment_penalty($inst, effective_penalty_rate($loan), $today);

// Ferot (payment) row: mul taka = kisti-r principal, interest = kisti-r interest + jorimana
$cc->conn->begin_transaction();
$payment = $cc->common_insert('loan_payments', [
    'loan_id' => $inst->loan_id,
    'principal_amount' => (float) $inst->principal_part,
    'interest_amount' => (float) $inst->interest_part + $penalty,
    'payment_date' => $today,
    'note' => 'Installment #' . $inst->installment_no . ($penalty > 0 ? ' (incl. penalty ' . number_format($penalty, 2) . ')' : ''),
    'installment_id' => $inst->id,
    'created_at' => date('Y-m-d H:i:s')
]);
if (!$payment['status']) {
    $cc->conn->rollback();
    json_fail($payment['message']);
}

$res = $cc->common_update('loan_installments', [
    'status' => 'paid',
    'paid_date' => $today,
    'penalty_paid' => $penalty,
    'payment_id' => $payment['data'],
    'updated_at' => date('Y-m-d H:i:s')
], ['id' => $id]);
if (!$res['status']) {
    $cc->conn->rollback();
    json_fail($res['message']);
}
$cc->conn->commit();

$res['penalty'] = $penalty;
echo json_encode($res);
