<?php
date_default_timezone_set('Asia/Dhaka');
include '../connection.php';
include '../auth_check.php';
include '../funds.php';
include '../loan_schedule.php';

$name = isset($_POST['borrower_name']) ? trim($_POST['borrower_name']) : '';
$principal = isset($_POST['principal']) ? $_POST['principal'] : '';
$rate = isset($_POST['interest_rate']) && $_POST['interest_rate'] !== '' ? $_POST['interest_rate'] : 0;
$months = isset($_POST['duration_months']) ? $_POST['duration_months'] : '';
$type = isset($_POST['repayment_type']) ? $_POST['repayment_type'] : 'monthly';
$penalty_rate = isset($_POST['penalty_rate']) && $_POST['penalty_rate'] !== '' ? $_POST['penalty_rate'] : null;
$loan_date = !empty($_POST['loan_date']) ? $_POST['loan_date'] : date('Y-m-d');

if ($name === '' || !is_numeric($principal) || (float) $principal <= 0) {
    json_fail('Borrower name and a valid amount are required.');
}
if (!is_numeric($rate) || (float) $rate < 0) {
    json_fail('Interest rate must be 0 or more.');
}
if (!ctype_digit((string) $months) || (int) $months < 1 || (int) $months > 600) {
    json_fail('Loan duration (months) must be a whole number, 1 or more.');
}
if (!valid_repayment_type($type)) {
    json_fail('Choose weekly, half-monthly or monthly installments.');
}
if ($penalty_rate !== null && (!is_numeric($penalty_rate) || (float) $penalty_rate < 0)) {
    json_fail('Penalty rate must be 0 or more.');
}
if (!valid_date($loan_date)) {
    json_fail('Invalid date.');
}

// Available cash-er beshi loan deya jabe na
$funds = get_funds($cc);
if ((float) $principal > $funds['available']) {
    json_fail('Not enough available savings. Available: ' . number_format($funds['available'], 2));
}

// Kisti-r schedule auto toiri hoy; loan shesh hobar tarikh (due_date) o auto
$schedule = build_schedule((float) $principal, (float) $rate, (int) $months, $type, $loan_date);

$data = [
    'borrower_name' => $name,
    'borrower_phone' => isset($_POST['borrower_phone']) ? $_POST['borrower_phone'] : '',
    'principal' => (float) $principal,
    'interest_rate' => (float) $rate,
    'loan_date' => $loan_date,
    'due_date' => $schedule['due_date'],
    'duration_months' => (int) $months,
    'repayment_type' => $type,
    'note' => isset($_POST['note']) ? $_POST['note'] : '',
    'created_at' => date('Y-m-d H:i:s')
];
if ($penalty_rate !== null) {
    $data['penalty_rate'] = (float) $penalty_rate;
}
if (!empty($_POST['member_id']) && ctype_digit((string) $_POST['member_id'])) {
    $data['member_id'] = $_POST['member_id'];
}

// Loan ar tar sob kisti ekshathe save hobe, na hole kichui na
$cc->conn->begin_transaction();
$res = $cc->common_insert('loans', $data);
if (!$res['status']) {
    $cc->conn->rollback();
    json_fail($res['message']);
}
$loan_id = $res['data'];

foreach ($schedule['installments'] as $row) {
    $ins = $cc->common_insert('loan_installments', [
        'loan_id' => $loan_id,
        'installment_no' => $row['installment_no'],
        'due_date' => $row['due_date'],
        'principal_part' => $row['principal_part'],
        'interest_part' => $row['interest_part'],
        'status' => 'unpaid',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    if (!$ins['status']) {
        $cc->conn->rollback();
        json_fail($ins['message']);
    }
}
$cc->conn->commit();

echo json_encode($res);
